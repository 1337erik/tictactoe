<template>

    <div class="outer-wrap">

        <div class="player-outer-wrapper">

            <div v-for=" player in players " :key=" player.id " class="player-inner-wrapper">

                <h3 :class=" isTurn( player.id ) ">Player: {{ player.id }}</h3>
                <h5>{{ player.type }}</h5>
                <h5>Marker: {{ player.marker }}</h5>
            </div>
        </div>
        <table>

            <tr>

                <td class="cell" id="0" @click=" claimCell( 0 ) "></td>
                <td class="cell" id="1" @click=" claimCell( 1 ) "></td>
                <td class="cell" id="2" @click=" claimCell( 2 ) "></td>
            </tr>
            <tr>

                <td class="cell" id="3" @click=" claimCell( 3 ) "></td>
                <td class="cell" id="4" @click=" claimCell( 4 ) "></td>
                <td class="cell" id="5" @click=" claimCell( 5 ) "></td>
            </tr>
            <tr>

                <td class="cell" id="6" @click=" claimCell( 6 ) "></td>
                <td class="cell" id="7" @click=" claimCell( 7 ) "></td>
                <td class="cell" id="8" @click=" claimCell( 8 ) "></td>
            </tr>
        </table>

        <div class="endgame my-4">

            <div class="text"></div>

            <button @click=" freshGame() ">Clear Board</button>
        </div>
    </div>
</template>

<script>

    export default {

        data: () => {

            return {

                gameOver    : false,
                playersTurn : 0,
                totalMoves  : 0,
                players     : [

                    {
                        id     : 0,
                        type   : 'human',
                        marker : 'X',
                        cells  : []
                    },
                    {
                        id     : 1,
                        type   : 'computer',
                        marker : 'O',
                        cells  : []
                    }
                ],
                winCombos : [

                    [ 0, 1, 2 ],
                    [ 3, 4, 5 ],
                    [ 6, 7, 8 ],
                    [ 0, 3, 6 ],
                    [ 1, 4, 7 ],
                    [ 2, 5, 8 ],
                    [ 0, 4, 8 ],
                    [ 6, 4, 2 ]
                ]
            }
        },
        methods: {

            isTurn( id ){

                return id == this.playersTurn ? 'yes-turn' : '';
            },
            freshGame(){

                this.gameOver    = false;
                this.playersTurn = 0;
                this.totalMoves  = 0;
                this.players.forEach( player => player.cells = [] );

                let cells = document.querySelectorAll( '.cell' ).forEach( cell => {

                    cell.innerHTML = '';
                    cell.classList.remove( 'winner-winner' );
                    cell.classList.remove( 'tied-up' );
                });
            },
            claimCell( id ){

                let cell = document.getElementById( id );

                if( !this.gameOver && cell.innerHTML.trim() == '' ){
                    // only if a games still on and clicking a blank cell..

                    const player   = this.players.find( player => player.id == this.playersTurn );
                    cell.innerHTML = player.marker; // apply the appropriate marker
                    player.cells.push( id );

                    let win = this.checkGameOver( player ); // check if game is over,
                    if( win ) this.declareWinner( player ); // and congratulate the winner..
                    else {

                        if( ++this.totalMoves == 9 ) this.declareWinner(); // call cats-game if neccessary,
                        else {

                            this.playersTurn = ( this.playersTurn + 1 ) % 2; // or switch players

                            // and check if new player is if AI controlled to prompt 'it' to move..
                            this.makeZuckerbergProud();
                        }
                    }
                }
            },
            checkGameOver( currentPlayer ){

                let count; // initialize values

                if( currentPlayer.cells.length >= 3 ){
                    // can only be over if the current player has at least 3 cells..

                    for( let i = 0; i < this.winCombos.length; i++ ){
                        // not using forEach because you cant 'break' out of forEach

                        count = 0; // how many cells line up in this combo
                        currentPlayer.cells.forEach( cell => {

                            if( this.winCombos[ i ].includes( cell ) ) count++;
                        });

                        if( count == 3 ){

                            this.gameOver = true

                            this.winCombos[ i ].forEach( cellId => document.getElementById( cellId ).classList.add( 'winner-winner' ) );
                            break;
                        }
                    }
                }
                return this.gameOver; // defaults to false
            },
            declareWinner( player = null ){

                if( player ) console.log( 'Player ' + player.id + ' has fucking sent it!' );
                else {

                    console.log( 'CATS GAME' );
                    document.querySelectorAll( '.cell' ).forEach( cell => cell.classList.add( 'tied-up' ) );
                }
            },
            makeZuckerbergProud(){
                // definitely would abstract the move making algorithm into a single function call.. since I use the same loop a few times.. but time..

                const currentPlayer = this.players.find( player => player.id == this.playersTurn );
                const opponent      = this.players.find( player => player.id != this.playersTurn );

                if( currentPlayer.type == 'computer' ){

                    // master algorithm..
                    // step 1: seem to be alive..
                    console.log( 'Zuckerberg: hmm..' );

                    let rand;
                    let cells = document.querySelectorAll( '.cell' );
                    for( let k = 0; k < cells.length; k++ ){
                        // just make sure the chosen spot is available
                        console.log( 'checking k: ' + k );

                        rand = Math.floor( Math.random() * 9 );
                        if( cells[ rand ].innerHTML.trim() == '' ){

                            console.log( 'ill just go here: ' + rand );
                            this.claimCell( rand );
                            break;
                        }
                    }

                    // step 2: if its the first move AI is taking, just pick anything
                    /*
                    if( currentPlayer.cells.length == 0 ){

                        let rand;
                        let cells = document.querySelectorAll( '.cell' );
                        for( let k = 0; k < cells.length; k++ ){
                            // just make sure the chosen spot is available

                            rand = Math.floor( Math.random() * 9 );
                            if( cells[ rand ].innerHTML.trim() == '' ) break;
                        }

                        console.log( 'ill just go here..' );
                        this.claimCell( rand );
                    } else {
                        // else, step 3: figure out if opponent has any winning moves to block

                        let count        = null;
                        let neededCell   = null;
                        let decisionMade = null;

                        if( opponent.cells.length >= 2 ){
                            // blocking moves can only exist if the opponents taken at least 2 moves..
                            console.log( 'you might have something..' );

                            for( let i = 0; i < this.winCombos.length; i++ ){

                                count = 0;

                                opponent.cells.forEach( cell => {

                                    if( this.winCombos[ i ].includes( cell ) ){

                                        count++;
                                    } else neededCell = cell; // if count gets to 2, then the third will be stored here
                                });

                                if( count == 2 ){
                                    // if count doesn't get to 2, both variables will simply be reset upon next iteration anyways
                                    // so if a blocking move is found, store it and break the loop ( if 2 blocking moves exist, well gg anyways you beat the robots )

                                    decisionMade = neededCell;
                                    console.log( decisionMade );
                                    break;
                                }
                            }
                        }

                        if( decisionMade ) {
                            // blocking move found.. take it

                            console.log( 'YOU CANT BLOCK ME' );
                            setTimeout( this.claimCell( neededCell ), 500 );
                        } else {
                            // if opponent has no winning moves, step 4: find 'my' best next move, either a second-out-of-three or a three-out-of-three

                            for( let i = 0; i < this.winCombos.length; i++ ){

                                count          = 0;

                                let candidateCount = 0;
                                let candidateCell  = null;

                                decisionMade   = null;

                                currentPlayer.cells.forEach( cell => {

                                    if( this.winCombos[ i ].includes( cell ) ){

                                        count++;
                                    } else candidateCell = cell; // if count gets above 1, then at least move towards completing this one..
                                });

                                if( count == 2 ){
                                    // if count doesn't get to 2, both variables will simply be reset upon next iteration anyways

                                    
                                    break;
                                } 
                            }
                        }
                    }
                    */
                }
            }
        },
        computed: {

            currentPlayer(){

            },
            winningCell(){

                // this.winCombos.forEach( cell => {

                //     count = 0;

                //     this.currentPlayer.cells.forEach( cell => {

                //         if( this.winCombos[ i ].includes( cell ) ){

                //             count++;
                //         } else neededCell = cell; // if count gets to 2, then the third will be stored here
                //     });

                //     if( count == 2 ){
                //         // if count doesn't get to 2, both variables will simply be reset upon next iteration anyways
                //         // so if a blocking move is found, store it and break the loop ( if 2 blocking moves exist, well gg anyways you beat the robots )

                //         decisionMade = neededCell;
                //         console.log( decisionMade );
                //         break;
                //     }
                // });
            },
            nextBestCell(){


            }
        },
        mounted(){

            this.freshGame();
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
</style>
