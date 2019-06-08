<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'games', function ( Blueprint $table ) {

            $table->bigIncrements( 'id' );
            $table->unsignedInteger( 'type' )->default( 1 ); // opens us up for different games
            $table->unsignedInteger( 'winner_id' )->nullable(); // winner of game saved here
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'games' );
    }
}
