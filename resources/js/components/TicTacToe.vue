<template>

    <div class="outer-wrap">

        <div class="player-outer-wrapper">

            <div v-for=" player in players " :key=" player.id " class="player-inner-wrapper">

                <h3 :style=" currentPlayer.id == player.id ? 'color: blue' : '' ">Player: {{ player.id }}</h3>
                <h5>{{ player.type }}</h5>
                <h5>Marker: {{ player.marker.toUpperCase() }}</h5>
            </div>
        </div>

        <table>

            <tr v-for=" j in boardRows" :key=" j ">

                <td v-for=" k in boardColumns " :class=" cellClasses( ( ( j - 1 ) * boardColumns ) + k ) " :id=" ( ( j - 1 ) * boardColumns ) + k " @click=" claimCell( ( ( j - 1 ) * boardColumns ) + k ) " :key=" k "></td>
            </tr>
        </table>

        <div class="playback-bar-outer-wrapper">

            <h1>Game Playback</h1>
            <h3 v-if=" winningMetaData.winningCombination || catsGame "><a href="/games">New Game</a></h3>
            <div class="playback-bar-inner-wrap">

                <div class="playback-card" v-for=" ( state, index ) in gameStates " :key=" index ">

                    {{ index == 0 ? 'Initial State' : 'Move ' + index }}
                </div>
                <div class="playback-card revert-card" @click=" revertMove() " v-if=" !gameOver && gameStates.length > 1 ">

                    Revert Move
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios';

    export default {

        props: [

            'playback',
            'game'
        ],
        data: () => {

            return {

                boardColumns      : 3, // board dimensions can be served by the server, which can store board sizes in the DB based on what game it is: chess, tictactoe, tictactoe Royale, etc..
                boardRows         : 3,
                gameStates        : [],
                players           : [

                    {
                        id     : 0,
                        type   : 'human',
                        marker : 'x'
                    },
                    {
                        id     : 1,
                        type   : 'computer',
                        marker : 'o'
                    }
                ],
                boardCombinations : []
            }
        },
        methods: {

            claimCell( id ){

                let cell = document.getElementById( id );
                // console.log( 'claiming cellID ' + id + ': ', cell, 'with current value: ', cell.innerHTML.trim() );

                if( !this.gameOver && cell.innerHTML.trim() == '' ){
                    // only if a games still on and clicking a blank cell..

                    // apply the appropriate marker to the specific cell
                    document.getElementById( id ).innerHTML = this.currentPlayer.marker.toUpperCase();

                    // read the board and push state
                    let newState = this.readStateFromCells();
                    this.gameStates.push( newState );

                    // sync to server
                    axios.patch( '/games/' + this.game.id, {

                        type : 'move',
                        newState
                    }).then( res => {
                        // console.log( 'response: ', res );

                        // update board combinations
                        this.checkBoardCombinations();

                        // and if new player is AI controlled, prompt 'it' to move..
                        this.makeZuckerbergProud();
                    })
                    .catch( error => {
                        console.error( 'Error: ', error );

                        this.revertMove();
                    });
                }
            },


            makeZuckerbergProud(){
                // definitely would abstract the move making algorithm into a single function call.. since I use the same loop a few times.. but time..

                if( !this.gameOver && this.currentPlayer.type == 'computer' ){

                    const myMarker       = this.currentPlayer.marker.toLowerCase();
                    const opponentMarker = this.opponent.marker.toLowerCase();

                    // initialize a set of groups with varying values to move within
                    const mixedCombinations   = [];
                    const emptyCombinations   = [];
                    const partialCombinations = [];

                    let madeDecision = false;

                    // master algorithm..
                    // Step 1: seem to be alive..
                    console.log( 'Zuckerberg: hmm..' );

                    // Step 2: Analyze the board combinations ( varies per game, will have to swap out when newer games are added )
                    for( let i = 0; i < this.boardCombinations.length; i++ ){

                        // console.log( 'this combination: ', this.boardCombinations[ i ] );

                        if( this.boardCombinations[ i ][ myMarker ].length == 2 && this.boardCombinations[ i ].emptyCells.length == 1 ){
                            // take the first winning move you can find

                            console.log( 'Zuckerberg: YOU CANT BLOCK ME' );
                            madeDecision = true;
                            this.claimCell( this.boardCombinations[ i ].emptyCells[ 0 ] );
                            break;
                        }
                        if( this.boardCombinations[ i ][ opponentMarker ].length == 2 && this.boardCombinations[ i ].emptyCells.length == 1 ){
                            // block the first winning move the opponent has

                            console.log( 'Zuckerberg: nice try!' );
                            madeDecision = true;
                            this.claimCell( this.boardCombinations[ i ].emptyCells[ 0 ] );
                            break;
                        }

                        if( this.boardCombinations[ i ][ myMarker ].length > 0 && this.boardCombinations[ i ].emptyCells.length > 0 ) partialCombinations.push( this.boardCombinations[ i ].emptyCells );
                        if( this.boardCombinations[ i ].emptyCells.length == 3 ) emptyCombinations.push( this.boardCombinations[ i ].emptyCells );
                    }

                    if( !madeDecision ){

                        // console.log( 'partial Combinations: ', partialCombinations );
                        // console.log( 'empty Combinations: ', emptyCombinations );

                        if( partialCombinations.length > 0 ){

                            console.log( 'Zuckerberg: my win is inevitable..' );
                            this.claimCell( partialCombinations[ 0 ][ 0 ] );
                        } else {

                            console.log( 'Zuckerberg: i\'ll just go here..' );
                            this.claimCell( emptyCombinations[ 0 ][ 0 ] );
                        }

                    }
                }
            },


            revertMove(){

                // pop the latest state off of gameStates, if the opponent is Zuckerberg then take 2 off so you can actually move again..
                this.gameStates.pop();
                if( this.players.find( player => player.type == 'computer' ) ) this.gameStates.pop();

                // take the latest state
                const latestState = this.gameStates[ this.gameStates.length - 1 ];

                // sync to server
                axios.patch( '/games/' + this.game.id, {

                    type : 'revert'
                }).then( res => {
                    // console.log( 'response: ', res );

                    // if status successful, load it to the board
                    this.loadStateIntoCells( latestState );

                    // update board combinations
                    this.checkBoardCombinations();
                })
                .catch( error => {

                    console.error( 'Error: ', error );
                });
            },


            loadStateIntoCells( state = null ){

                // console.log( 'loading state: ', state );
                if( [ null, '' ].includes( state ) ) document.querySelectorAll( '.cell' ).forEach( cell => cell.innerHTML = '' ); // clear a fresh board
                else {

                    state.split( '' ).forEach( ( value, index ) => {

                        document.getElementById( index + 1 ).innerHTML = ( value == '.' ? '' : value.toUpperCase() );
                    });
                }
            },
            readStateFromCells(){

                let state = [];
                document.querySelectorAll( '.cell' ).forEach( ( cell, index ) => {

                    state.push( cell.innerHTML == '' ? '.' : cell.innerHTML.toLowerCase() ); // else return the full board state;
                });

                // console.log( 'reading board state: ', state.join( '' ) );
                return state.join( '' );
            },


            checkBoardCombinations(){

                const combinations = [
                    // i should also derive this from the game type and the board dimensions..

                    [ 1, 2, 3 ],
                    [ 4, 5, 6 ],
                    [ 7, 8, 9 ],
                    [ 1, 4, 7 ],
                    [ 2, 5, 8 ],
                    [ 3, 6, 9 ],
                    [ 1, 5, 9 ],
                    [ 3, 5, 7 ]
                ];
                const withMetaData = [];

                combinations.forEach( ( combo, index ) => {

                    // mark each combo as:
                    // occupied by 3 same
                    // occupied by 2 same w/ 1 blank
                    // occupied by 1 w/ 2 blank
                    // occupied by mixed set

                    const x = [];
                    const o = [];
                    const emptyCells = [];

                    combo.forEach( cell => {

                        const value = document.getElementById( cell ).innerHTML.toLowerCase();
                        switch( value ){

                            case 'x':

                                x.push( cell );
                                break;
                            case 'o':

                                o.push( cell )
                                break;
                            case '' :
                            default :

                                emptyCells.push( cell );
                                break;
                        }
                    });

                    withMetaData.push({

                        x, o, emptyCells
                    });
                });

                this.boardCombinations = withMetaData;
            },

            cellClasses( id ){

                const base = [ 'cell' ];
                if( this.winningMetaData.winningCombination != null && this.winningMetaData.winningCombination.includes( id ) ) base.push( 'winner-winner' );
                if( this.catsGame ) base.push( 'tied-up' );

                return base.join( ' ' );
            },
        },
        computed: {

            currentPlayer(){
                // current player can be derived from the total amount of moves that have been made & total amount of players

                return this.players[ ( this.totalMoves + this.players.length ) % this.players.length ];
            },
            opponent(){

                return this.players[ ( this.totalMoves + this.players.length + 1 ) % this.players.length ]
            },
            totalMoves(){

                return this.gameStates.length - 1;
            },

            playerCells(){
                // grab all indexes of the board where the player's marker is found

                let cells = [];
                document.querySelectorAll( '.cell' ).forEach( ( cell, index ) => {

                    if( cell.innerHTML.toLowerCase() == this.currentPlayer.marker.toLowerCase() ) cells.push( index + 1 );
                });

                // console.log( 'player has cells: ', cells.join() );
                return cells.join();
            },

            catsGame(){

                if( this.totalMoves == ( this.boardColumns * this.boardRows ) && this.winningMetaData.winningCombination == null ){

                    console.log( 'Zuckerberg: you get away this time..' );
                    console.log( '-- CATS GAME --' );
                    return true;
                } else return false;
            },
            gameOver(){

                return ( this.catsGame || this.winningMetaData.winningCombination != null );
            },


            againstAI(){

                return this.players.find( player => player.type == 'computer' );
            },

            winningMetaData(){

                let gameData = {

                    winningPlayer      : null,
                    winningCombination : null
                };
                this.boardCombinations.forEach( combo => {

                    if( combo.emptyCells.length == 0 && combo.x.length == 3 ){

                        gameData.winningPlayer      = this.players.find( player => player.marker.toLowerCase() == 'x' );
                        gameData.winningCombination = combo.x;
                        console.log( '-- GAME OVER --', 'player ' + gameData.winningPlayer.id + ' has won!' );
                        if( gameData.winningPlayer.type == 'computer' ) console.log( 'Zuckerberg: LOL gg noob' );
                        if( gameData.winningPlayer.type == 'human'    ) console.log( 'Zuckerberg: WHAT?! I must become stronger..' );
                    } else if( combo.emptyCells.length == 0 && combo.o.length == 3 ){

                        gameData.winningPlayer      = this.players.find( player => player.marker.toLowerCase() == 'o' );
                        gameData.winningCombination = combo.o;
                        console.log( '-- GAME OVER --', gameData.winningPlayer.id + ' has won!' );
                        if( gameData.winningPlayer.type == 'computer' ) console.log( 'Zuckerberg: LOL gg noob' );
                        if( gameData.winningPlayer.type == 'human'    ) console.log( 'Zuckerberg: WHAT?! I must become stronger..' );
                    }
                });

                return gameData;
            }
        },
        mounted(){

            // initialize game state by stripping all irrelevant meta data from server's playback
            const gameStates = [];
            this.playback.forEach( state => gameStates.push( state.details ) );
            this.gameStates = gameStates;

            // initialize the board using the latest playback state..
            this.loadStateIntoCells( gameStates[ this.totalMoves ] );

            // initialize board combinations, important for when loading a past game
            this.checkBoardCombinations();
        }
    }
</script>

<style scoped>

    .player-outer-wrapper {

        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .player-inner-wrapper {

        text-align: center;
        flex: 1;
    }

    .yes-turn {

        font-weight: bold;
        color: blue;
    }

    .outer-wrap {

        margin-top: 65px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .cell {

        border: 2px solid #333;
        height: 100px;
        width: 100px;
        font-size: 45px;
        cursor: pointer;


        text-align: center;
        vertical-align: middle;
    }

    .winner-winner {

        background-color: rgba(3, 3, 150, 0.521);
    }

    .tied-up {

        background-color: rgba(148, 5, 5, 0.596);
    }

    table {

        margin-top: 35px;
        border-collapse: collapse;
        width: 100%;
        max-width: 300px;
    }

    table tr:first-child td {

        border-top: 0;
    }

    table tr:last-child td {

        border-bottom: 0;
    }

    table tr td:first-child {

        border-left: 0;
    }

    table tr td:last-child {

        border-right: 0;
    }

    .action-bar-outer-wrapper {

        margin-top: 40px;
    }




    /***************************************** playback section */
    .playback-bar-outer-wrapper {

        margin-top: 48px;
        width: 100%;
        text-align: center;
    }

    .playback-card {

        background-color: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0px 2px 5px 0px #ccc;
        display: inline-block;
        margin: 10px;
        width: 100px;
        height: 100px;
    }

    .revert-card {

        cursor: pointer;
    }
</style>
