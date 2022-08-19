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
        'mode_id', 'channel', 'sign', 'name', 'sub_name', 'start_at', 'end_at', 'status',
    ];

    /**
     * Get the rule associated with the game.
     */
    public function rule()
    {
        return $this->hasOne(GameRule::class);
    }

    /**
     * Get the mode that owns the game.
     */
    public function mode()
    {
        return $this->belongsTo(GameMode::class);
    }
}
