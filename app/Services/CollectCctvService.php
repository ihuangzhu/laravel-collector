<?php


namespace App\Services;


use App\Enums\GameStatus;
use App\Helpers\CctvHelper;
use App\Jobs\ScanSubscriber;
use App\Models\Game;
use App\Models\GameMatch;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CollectCctvService
{
    /**
     * 更新比赛
     *
     * @param Game $game
     * @throws GuzzleException
     */
    public function refresh(Game $game)
    {
        if ($this->refreshGame($game)) {
            $this->refreshGameMatch($game);
        }
    }

    /**
     * 更新比赛
     *
     * @param Game $game
     * @return bool
     * @throws GuzzleException
     */
    public function refreshGame(Game $game)
    {
        // 获取比赛日期列表
        $gameDateList = CctvHelper::gameDateList($game->sign);
        if (empty($gameDateList['data'])) return false;
        if ($dates = $gameDateList['data']['dates'] ?? false) {
            $today = $gameDateList['data']['today'] ?? now()->format('Y-m-d');
            $firstDay = head($dates);
            $lastDay = last($dates);

            $status = GameStatus::PREPARE;
            if ($today >= $firstDay['date']) $status = GameStatus::START;
            if ($today > $lastDay['date']) $status = GameStatus::END;

            $game->start_at = $firstDay['date'];
            $game->end_at = $lastDay['date'];
            $game->status = $status;
            $game->setUpdatedAt(now());
            return $game->update();
        }

        Log::warning('Fail to get the game dates', [
            'game' => $game->toArray(),
            'games' => $gameDateList,
        ]);
        return false;
    }

    /**
     * 更新比赛排表
     *
     * @param Game $game
     * @return bool
     * @throws GuzzleException
     */
    public function refreshGameMatch(Game $game)
    {
        if (empty($game->start_at) || empty($game->end_at)) return false;

        // 获取比赛安排列表
        $matchList = CctvHelper::matchList($game->sign, $game->start_at, $game->end_at);

        // 同步比赛安排列表
        foreach ($matchList['results'] ?? [] as $matchDay) {
            foreach ($matchDay['list'] ?? [] as $matchItem) {
                $teamA = $matchItem['homeName'] ?? 'A';
                $teamB = $matchItem['guestName'] ?? 'B';
                $scoreA = $matchItem['homeScore'] ?? 0;
                $scoreB = $matchItem['guestScore'] ?? 0;

                // 比赛数据
                if ($gameMatch = GameMatch::query()->where(['game_id' => $game->id, 'sign' => $matchItem['id']])->first()) { // 更新
                    if ($gameMatch->status == GameStatus::END) continue;

                    $gameMatch->name = "{$teamA} vs {$teamB}";
                    $gameMatch->score = "{$scoreA}:{$scoreB}";
                    $gameMatch->status = $matchItem['gameStatus'];
                    $gameMatch->team_a = $teamA;
                    $gameMatch->team_b = $teamB;
                    $gameMatch->score_a = $scoreA;
                    $gameMatch->score_b = $scoreB;
                    $gameMatch->start_at = $matchItem['startTime'];
                    $gameMatch->update();

                    // 比赛结束执行回调
                    if ($gameMatch->status == GameStatus::END) ScanSubscriber::dispatch($gameMatch)->onQueue('scan');
                } else { // 新增
                    $gameMatch = new GameMatch();
                    $gameMatch->fill([
                        'game_id' => $game->id,
                        'sign' => $matchItem['id'],
                        'name' => "{$teamA} vs {$teamB}",
                        'score' => "{$scoreA}:{$scoreB}",
                        'status' => $matchItem['gameStatus'],
                        'team_a' => $teamA,
                        'team_b' => $teamB,
                        'score_a' => $scoreA,
                        'score_b' => $scoreB,
                        'start_at' => $matchItem['startTime'],
                    ])->save();
                }
            }
        }

        return true;
    }

}
