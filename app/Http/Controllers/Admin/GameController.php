<?php

namespace App\Http\Controllers\Admin;


use App\Enums\Channel;
use App\Enums\GameStatus;
use App\Helpers\CctvHelper;
use App\Helpers\LplHelper;
use App\Models\Game;
use App\Services\CollectService;

class GameController extends BaseController
{

    /**
     * 游戏列表
     */
    public function index()
    {
        $gameQuery = Game::query();

        if ($name = request('name')) {
            $gameQuery->where('name', 'like', $name);
        }

        $gamePaginate = $gameQuery->paginate();
        return view('manage.game.index', [
            'games' => $gamePaginate,
        ]);
    }

    /**
     * 游戏编辑
     */
    public function edit()
    {
        if (request()->isMethod('post')) {
            $channel = request('channel');
            if (empty($channel)) return $this->failed('请选择频道');

            $gameJson = request('game');
            $gameJson = json_decode($gameJson, true) ?: [];
            if (empty($gameJson)) return $this->failed('请选择游戏');

            if (Game::query()->where(['channel' => $channel, 'sign' => $gameJson['sign']])->exists()) {
                return $this->failed('游戏已经存在');
            }

            $game = new Game();
            $gameJson['channel'] = $channel;
            if ($game->fill($gameJson)->save()) {
                $this->message('操作成功');
            }

            $this->failed('操作失败');
        }

        return view('manage.game.edit');
    }

    /**
     * 查询
     */
    public function query()
    {
        $channel = request('channel');
        if (empty($channel)) return $this->failed('请选择频道');
        if (empty(Channel::getMap($channel))) return $this->failed('频道不存在');

        $games = [];
        switch ($channel) {
            case Channel::LPL:
                $gameList = LplHelper::gameList();
                foreach ($gameList['gameList'] ?? [] as $game) {
                    $games[] = [
                        'sign' => $game['sGameId'],
                        'name' => $game['bGameName'],
                        'sub_name' => $game['sGameName'],
                        'start_at' => $game['sDate'],
                        'end_at' => $game['eDate'],
                        'status' => $game['GameStatus'],
                    ];
                }
                break;
            case Channel::CCTV:
                $gameList = CctvHelper::gameList();
                foreach ($gameList['results'] ?? [] as $game) {
                    $games[] = [
                        'sign' => $game['id'],
                        'name' => $game['shortName'],
                        'sub_name' => $game['itemName'],
                        'status' => GameStatus::PREPARE,
                    ];
                }
                break;
            default:
                return $this->failed('不支持的频道');
        }

        return $this->success($games);
    }

    /**
     * 刷新
     */
    public function refresh()
    {
        app(CollectService::class)->refresh(request('id'));
        return $this->message();
    }

}
