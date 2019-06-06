<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * 
     * used for the main game board to show..
     * Relevant details:
     *  - vs AI?
     *  - is player signed in?
     *  - which game? ( for now, tic-tac-toe is the only one allowed )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view( 'tictactoe.board' );
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

        return view( 'tictactoe.show' );
    }

    public function showPast( Game $game )
    {

        return view( 'unavailable' );
    }
    public function showLive( Game $game )
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
        //
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
