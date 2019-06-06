<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get( '/', function () {

    return view( 'welcome' );
});

Auth::routes();

Route::get( '/home', 'HomeController@index' )->name( 'home' );


Route::get( 'games/seePast',   'GameController@showPast'  )->name( 'play.games.showPast'  );
Route::get( 'games/seeLive',   'GameController@showLive'  )->name( 'play.games.showLive'  );
Route::get( 'games/startLive', 'GameController@startLive' )->name( 'play.games.startLive' );
Route::resource( 'games', 'GameController', [

    'as' => 'play'
]);
Route::apiResource( 'states', 'StateController' );