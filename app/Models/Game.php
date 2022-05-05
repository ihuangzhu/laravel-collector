<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel', 'sign', 'name', 'sub_name', 'start_at', 'end_at', 'status',
    ];

    /**
     * Get the rule associated with the game.
     */
    public function rule()
    {
        return $this->hasOne(GameRule::class);
    }
}
