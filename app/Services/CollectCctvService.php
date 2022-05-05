<?php


namespace App\Services;


use App\Enums\GameStatus;
use App\Helpers\CctvHelper;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\GameMatchRound;
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
            return $game->save();
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
                $matchScoreA = $matchItem['homeScore'] ?? 0;
                $matchScoreB = $matchItem['guestScore'] ?? 0;

                /*
                 * 1）比赛
                 */
                if ($gameMatch = GameMatch::query()->where(['game_id' => $game->id, 'sign' => $matchItem['id']])->first()) { // 更新
                    if ($gameMatch->status == GameStatus::END) continue;

                    $gameMatch->score = "{$matchScoreA}:{$matchScoreB}";
                    $gameMatch->status = $matchItem['gameStatus'];
                    $gameMatch->score_a = $matchScoreA;
                    $gameMatch->score_b = $matchScoreB;
                    $gameMatch->update();
                } else { // 新增
                    $teamA = $matchItem['homeName'] ?? 'A';
                    $teamB = $matchItem['guestName'] ?? 'B';

                    $gameMatch = new GameMatch();
                    $gameMatch->fill([
                        'game_id' => $game->id,
                        'sign' => $matchItem['id'],
                        'name' => "{$teamA} vs {$teamB}",
                        'score' => "{$matchScoreA}:{$matchScoreB}",
                        'status' => $matchItem['gameStatus'],
                        'team_a' => $matchItem['homeName'],
                        'team_b' => $matchItem['guestName'],
                        'score_a' => $matchScoreA,
                        'score_b' => $matchScoreB,
                        'start_at' => $matchItem['startTime'],
                    ])->save();
                }

                /*
                 * 2）回合
                 */
                foreach ($matchItem['scores'] ?? [] as $sign => $roundItem) {
                    $roundScoreA = $roundItem['team1'] ?? 0;
                    $roundScoreB = $roundItem['team2'] ?? 0;

                    if ($gameMatchRound = GameMatchRound::query()->where(['game_id' => $game->id, 'match_id' => $gameMatch->id, 'sign' => $sign])->first()) { // 更新
                        if ($gameMatchRound->status == GameStatus::END) continue;

                        $gameMatchRound->status = $gameMatch->status;
                        $gameMatchRound->score_a = $roundScoreA;
                        $gameMatchRound->score_b = $roundScoreB;
                        $gameMatchRound->update();
                    } else { // 新增
                        $gameMatchRound = new GameMatchRound();
                        $gameMatchRound->fill([
                            'game_id' => $game->id,
                            'match_id' => $gameMatch->id,
                            'sign' => $sign,
                            'status' => $gameMatch->status,
                            'team_a' => $gameMatch->team_a,
                            'team_b' => $gameMatch->team_b,
                            'score_a' => $roundScoreA,
                            'score_b' => $roundScoreB,
                        ])->save();
                    }
                }
            }
        }

        return true;
    }

}
