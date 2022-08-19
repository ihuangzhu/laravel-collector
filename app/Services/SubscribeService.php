<?php


namespace App\Services;


use App\Jobs\NotifySubscriber;
use App\Models\GameMatch;
use App\Models\GameSubscribe;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscribeService
{

    /**
     * 扫描订阅
     *
     * @param GameMatch $gameMatch
     * @return void
     */
    public function scan(GameMatch $gameMatch)
    {
        GameSubscribe::query()->where(['game_id' => $gameMatch->game_id])->each(function (GameSubscribe $gameSubscribe) use ($gameMatch) {
            NotifySubscriber::dispatch($gameMatch, $gameSubscribe)->onQueue('notify');
        });
    }

    /**
     * 回调通知
     *
     * @param GameMatch $gameMatch
     * @param GameSubscribe $gameSubscribe
     * @throws Throwable
     */
    public function notify(GameMatch $gameMatch, GameSubscribe $gameSubscribe)
    {
        if (empty($gameSubscribe->callback_url)) return;

        try {
            $httpClient = new HttpClient(['verify' => false, 'timeout' => 10]);
            $response = $httpClient->post($gameSubscribe->callback_url, [
                RequestOptions::HEADERS => ['x-requested-with' => 'XMLHttpRequest'],
                RequestOptions::JSON => $gameMatch->toArray(),
            ]);
            $responseBody = $response->getBody()->getContents();
            Log::debug('回调返回', [
                'url' => $gameSubscribe->callback_url,
                'request' => $gameMatch->toJson(),
                'response' => $responseBody,
            ]);

            if ($json = json_decode($responseBody, true) ?: []) {
                if ($json['code'] == 0) return;

                throw new Exception('回调执行失败');
            }

            throw new Exception('回调请求失败');
        } catch (Throwable $t) {
            Log::debug('回调失败：' . $t->getMessage());
            throw new Exception($t->getMessage());
        }
    }

}
