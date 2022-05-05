<?php


namespace App\Services;


use App\Enums\Channel;
use App\Enums\GameStatus;
use App\Jobs\CollectCctv;
use App\Jobs\CollectLpl;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class CollectService
{

    /**
     * 更新
     *
     * @param int $gameId
     */
    public function refresh($gameId = null)
    {
        $gameQuery = Game::query();
        if ($gameId) $gameQuery->where(['id' => $gameId]);
        $gameQuery->where('status', '<>', GameStatus::END)->each(function ($game) {
            if ($game->status != GameStatus::END) {
                switch ($game->channel) {
                    case Channel::CST:
//                        CollectCst::dispatch($game); // 异步执行
                        app(CollectCstService::class)->refresh($game); // 同步执行
                        break;
                    case Channel::LPL:
//                        CollectLpl::dispatch($game); // 异步执行
                        app(CollectLplService::class)->refresh($game); // 同步执行
                        break;
                    case Channel::CCTV:
//                        CollectCctv::dispatch($game); // 异步执行
                        app(CollectCctvService::class)->refresh($game); // 同步执行
                        break;
                    default:
                        Log::warning('Unknown channel type:' . $game->channel->type);
                }
            }
        });
    }

}
