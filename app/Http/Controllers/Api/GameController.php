<?php

namespace App\Http\Controllers\Api;


use App\Models\Game;
use App\Models\GameMatch;
use App\Models\GameMode;

class GameController extends BaseController
{

    /**
     * @return mixed
     */
    public function index()
    {
        $gameQuery = Game::query();

        if ($modeId = request('mode_id')) {
            $gameQuery->where('mode_id', $modeId);
        }

        $gameMatches = $gameQuery->get();
        return $this->success($gameMatches);
    }

    /**
     * @return mixed
     */
    public function mode()
    {
        $gameModeQuery = GameMode::query();

        if ($modeId = request('mode_id')) {
            $gameModeQuery->where('id', $modeId);
        }

        $gameMatches = $gameModeQuery->get();
        return $this->success($gameMatches);
    }

    /**
     * @return mixed
     */
    public function matches()
    {
        $gameId = request('game_id');
        if (empty($gameId)) return $this->failed('Params err.');

        $gameMatchQuery = GameMatch::query()->where('game_id', $gameId);
        if ($date = request('date')) {
            $gameMatchQuery->whereDate('start_at', $date);
        }
        if ($status = request('status')) {
            $gameMatchQuery->whereIn('status', explode(',', $status));
        }

        $gameMatches = $gameMatchQuery->get();
        return $this->success($gameMatches);
    }

}
