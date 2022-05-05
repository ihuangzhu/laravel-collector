<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'sign', 'name', 'score', 'status', 'team_a', 'team_b', 'score_a', 'score_b', 'start_at',
    ];

}
