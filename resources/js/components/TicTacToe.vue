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

                let cells = document.querySelectorAll( '.cell' ).forEach( cell => cell.innerHTML = '' );
            },
            claimCell( id ){

                let cell = document.getElementById( id );

                if( !this.gameOver && cell.innerHTML.trim() == '' ){
                    // only if a games still on and clicking a blank cell..

                    const player = this.players.find( player => player.id == this.playersTurn );
                    cell.innerHTML = player.marker; // apply the appropriate marker
                    player.cells.push( id );

                    let win = this.checkGameOver( player ); // check if game is over,
                    if( win ) this.declareWinner( player ); // and congratulate the winner..
                    else {

                        if( ++this.totalMoves == 9 ) this.declareWinner(); // call cats-game if neccessary,
                        else this.playersTurn = ( this.playersTurn + 1 ) % 2; // or switch players
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
                            break;
                        }
                    }
                }
                return this.gameOver; // defaults to false
            },
            declareWinner( player = null ){

                if( player ) console.log( 'Player ' + player.id + ' has fucking sent it!' );
                else console.log( 'CATS GAME' );
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
