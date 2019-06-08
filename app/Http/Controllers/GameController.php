<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\State;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * 
     * used for creating a game before showing it..
     * Relevant details:
     *  - is player signed in?
     *  - which game? ( for now, tic-tac-toe is the only one allowed )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Big TODO:
        // prevent new games from starting..
        // maybe fetch an existing game with 'incomplete' status?
        // or regularly clear out empty/unfinished games..
        // I'd like to not have to enforce signup to play against AI..

        // Step 1: Initialize. Set or create a new game, then set or initialize game state
        $game = new Game([

            'type' => 1 // tic-tac-toe is the only one allowed right now. eventually could be chess, cards, etc.
        ]);
        $game->save();

        $game->states()->create(); // create a blank state for the game to begin if there isn't already one

        // Step 2: if the current player is logged in then associate them to the game
        $user = Auth::user();
        if( $user ) $user->games()->attach( $game, [ 'role' => 'player' ] );

        return redirect()->route( 'play.games.show', compact( 'game' ) );
    }



    public function startLive( Game $game )
    {

        return view( 'unavailable' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show( Game $game )
    {

        return view( 'tictactoe.show', compact( 'game' ) );
    }

    public function seePast( Game $game )
    {

        $games = Auth::user()->games;
        // Log::debug( 'games: ' . print_r( $games, true ) );

        return view( 'tictactoe.seePast', compact( 'games' ) );
    }
    public function seeLive( Game $game )
    {

        return view( 'unavailable' );
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {

        switch( $request->type ){

            case 'revert':

                return State::orderBy( 'id', 'desc' )->where( 'game_id', $game->id )->take( 2 )->delete();
                break;
            case 'move':

                return $game->states()->create([

                    'details' => $request->newState
                ]);
                break;
            case 'saveWinner':

                $game->winner_id = Auth::user()->id;
                return $game->save() ? $game->toJson() : response( $game->errors, 400 );
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }
}
