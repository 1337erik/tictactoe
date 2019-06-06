<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\State;

class Game extends Model
{

    protected $guarded = [];

    public function states()
    {

        return $this->hasMany( State::class );
    }

    public function users()
    {

        return $this->belongsToMany( User::class )
            ->withPivot( 'role' )
            ->withTimestamps();
    }
}
