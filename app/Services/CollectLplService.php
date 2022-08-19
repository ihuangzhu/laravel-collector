<?php


namespace App\Services;


use App\Enums\GameStatus;
use App\Helpers\LplHelper;
use App\Jobs\ScanSubscriber;
use App\Models\Game;
use App\Models\GameMatch;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CollectLplService
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
        // 获取比赛列表
        $gameList = LplHelper::gameList();
        foreach ($gameList['gameList'] ?? '' as $gameItem) {
            if ($game->sign == $gameItem['sGameId']) {
                if ($gameItem['sDate']) $game->start_at = $gameItem['sDate'];
                if ($gameItem['eDate']) $game->end_at = $gameItem['eDate'];
                $game->status = $gameItem['GameStatus'];
                $game->setUpdatedAt($gameList['lastUpTime'] ?? $game->updated_at);
                return $game->update();
            }
        }

        Log::warning('Unmatched the game', [
            'game' => $game->toArray(),
            'games' => $gameList,
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
        // 获取比赛安排列表
        $matchList = LplHelper::matchList($game->sign);
        if (isset($matchList['lastUpTime']) && $matchList['lastUpTime'] == $game->updated_at) return false;

        // 获取参赛队伍列表
        $teamList = LplHelper::teamList();
        $teamMap = $teamList['msg'] ?? [];

        // 同步比赛安排列表
        foreach ($matchList['msg'] ?? [] as $matchItem) {
            $teamA = $teamMap[$matchItem['TeamA']]['TeamName'] ?? 'A';
            $teamB = $teamMap[$matchItem['TeamB']]['TeamName'] ?? 'B';
            $scoreA = $matchItem['ScoreA'] ?? 0;
            $scoreB = $matchItem['ScoreB'] ?? 0;

            // 比赛数据
            if ($gameMatch = GameMatch::query()->where(['game_id' => $game->id, 'sign' => $matchItem['bMatchId']])->first()) { // 编辑
                if ($gameMatch->status == GameStatus::END) continue;

                $gameMatch->name = "{$teamA} vs {$teamB}";
                $gameMatch->score = "{$scoreA}:{$scoreB}";
                $gameMatch->status = $matchItem['MatchStatus'];
                $gameMatch->team_a = $teamA;
                $gameMatch->team_b = $teamB;
                $gameMatch->score_a = $scoreA;
                $gameMatch->score_b = $scoreB;
                $gameMatch->start_at = $matchItem['MatchDate'];
                $gameMatch->update();

                // 比赛结束执行回调
                if ($gameMatch->status == GameStatus::END) ScanSubscriber::dispatch($gameMatch)->onQueue('scan');
            } else { // 新增
                $gameMatch = new GameMatch();
                $gameMatch->fill([
                    'game_id' => $game->id,
                    'sign' => $matchItem['bMatchId'],
                    'name' => "{$teamA} vs {$teamB}",
                    'score' => "{$scoreA}:{$scoreB}",
                    'status' => $matchItem['MatchStatus'],
                    'team_a' => $teamA,
                    'team_b' => $teamB,
                    'score_a' => $scoreA,
                    'score_b' => $scoreB,
                    'start_at' => $matchItem['MatchDate'],
                ])->save();
            }
        }

        return true;
    }

}
