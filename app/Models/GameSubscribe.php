<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSubscribe extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'callback_url',
    ];

}
