<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'game_user', function ( Blueprint $table ) {

            $table->bigIncrements( 'id' );
            $table->unsignedBigInteger( 'game_id' );
            $table->unsignedBigInteger( 'user_id' );
            $table->string( 'role' )->default( 'spectator' ); // player or spectator
            $table->timestamps();

            $table->unique([ 'game_id', 'user_id' ]);

            $table->foreign( 'game_id' )->references( 'id' )->on( 'games' );
            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_users');
    }
}
