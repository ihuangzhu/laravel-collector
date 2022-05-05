<?php


namespace App\Services;


use App\Enums\GameStatus;
use App\Helpers\LplHelper;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\GameMatchRound;
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
                $game->status = $gameItem['GameStatus'];
                $game->setUpdatedAt($gameList['lastUpTime'] ?? $game->updated_at);
                return $game->save();
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
            $matchScoreA = $matchItem['ScoreA'] ?? 0;
            $matchScoreB = $matchItem['ScoreB'] ?? 0;

            /*
             * 1）比赛
             */
            if ($gameMatch = GameMatch::query()->where(['game_id' => $game->id, 'sign' => $matchItem['bMatchId']])->first()) { // 编辑
                if ($gameMatch->status == GameStatus::END) continue;

                $gameMatch->score = "{$matchScoreA}:{$matchScoreB}";
                $gameMatch->status = $matchItem['MatchStatus'];
                $gameMatch->update();
            } else { // 新增
                $teamA = $teamMap[$matchItem['TeamA']]['TeamName'] ?? 'A';
                $teamB = $teamMap[$matchItem['TeamB']]['TeamName'] ?? 'B';

                $gameMatch = new GameMatch();
                $gameMatch->fill([
                    'game_id' => $game->id,
                    'sign' => $matchItem['bMatchId'],
                    'name' => $teamMap ? "{$teamA} vs {$teamB}" : $matchItem['bMatchName'],
                    'score' => "{$matchScoreA}:{$matchScoreB}",
                    'status' => $matchItem['MatchStatus'],
                    'team_a' => $teamA,
                    'team_b' => $teamB,
                    'score_a' => $matchScoreA,
                    'score_b' => $matchScoreB,
                    'start_at' => $matchItem['MatchDate'],
                ])->save();
            }

            /*
             * 2）回合
             */
            $matchDetail = LplHelper::matchDetail($gameMatch->sign); // 获取比赛安排详情
            foreach ($matchDetail['sMatchInfo'] ?? [] as $roundItem) {
                $roundScoreA = $roundItem['MatchWin'] == '1' ? 1 : 0;
                $roundScoreB = $roundItem['MatchWin'] == '2' ? 1 : 0;

                if ($gameMatchRound = GameMatchRound::query()->where(['game_id' => $game->id, 'match_id' => $gameMatch->id, 'sign' => $roundItem['sMatchId']])->first()) { // 更新
                    if ($gameMatchRound->status == GameStatus::END) continue;

                    $gameMatchRound->status = $roundItem['MatchStatus'];
                    $gameMatchRound->score_a = $roundScoreA;
                    $gameMatchRound->score_b = $roundScoreB;
                    $gameMatchRound->update();
                } else { // 新增
                    $gameMatchRound = new GameMatchRound();
                    $gameMatchRound->fill([
                        'game_id' => $game->id,
                        'match_id' => $gameMatch->id,
                        'sign' => $roundItem['sMatchId'],
                        'status' => $roundItem['MatchStatus'],
                        'team_a' => $gameMatch->team_a,
                        'team_b' => $gameMatch->team_b,
                        'score_a' => $roundScoreA,
                        'score_b' => $roundScoreB,
                    ])->save();
                }
            }
        }

        return true;
    }

}
