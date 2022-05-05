<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMatchRound extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'match_id', 'sign', 'status', 'team_a', 'team_b', 'score_a', 'score_b',
    ];

}
