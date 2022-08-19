<?php

namespace App\Http\Controllers\Api;


use App\Jobs\ScanSubscriber;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\GameSubscribe;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SubscribeController extends BaseController
{

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function index()
    {
        // 校验参数
        $validator = validator()->make(request()->all(), [
            'game_id' => 'required|exists:' . Game::class . ',id',
            'callback_url' => 'required|url',
        ], [
            'game_id.required' => '比赛ID必须',
            'game_id.exists' => '比赛数据不存在',
            'callback_url.required' => '回调链接必须',
            'callback_url.url' => '回调链接格式错误',
        ]);
        if ($validator->fails()) return $this->failed($validator->errors()->first());

        // 是否存在
        $request = request()->only(['game_id', 'callback_url']);
        if (GameSubscribe::query()->where($request)->exists()) {
            return $this->message('订阅成功');
        }

        // 保存订阅
        $gameSubscribe = new GameSubscribe();
        if ($gameSubscribe->fill($request)->save()) {
            return $this->message('订阅成功');
        }

        return $this->failed('订阅失败');
    }

    public function test()
    {
        if (request()->isMethod('post')) {
            Log::debug('收到回调：', request()->all());

            return response()->json([
                'code' => 0,
                'data' => request()->all(),
            ]);
        }

        $matchId = request('match_id', 348);
        $gameMatch = GameMatch::query()->where(['id' => $matchId])->first();
        ScanSubscriber::dispatch($gameMatch)->onQueue('scan');
        dd('done', Redis::connection()->client()->isConnected());
    }

}
