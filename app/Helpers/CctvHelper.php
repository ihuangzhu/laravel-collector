<?php


namespace App\Helpers;

use GuzzleHttp\Exception\GuzzleException;

class CctvHelper extends RequestHelper
{

    /**
     * 获取比赛列表
     *
     * @return array|false
     * @throws GuzzleException
     */
    public static function gameList()
    {
        return self::get('https://cbs-u.sports.cctv.com/pc/league/hot_list', [
            'client' => 'pc',
            'ran' => now()->valueOf(),
        ]);
    }

    /**
     * 获取比赛列表
     *
     * @param string $gameId
     * @return array|false
     * @throws GuzzleException
     */
    public static function gameDateList($gameId)
    {
        return self::get('https://cbs-u.sports.cctv.com/pc/game/date_list', [
            'client' => 'pc',
            'leagueId' => $gameId,
            'ran' => now()->valueOf(),
        ]);
    }

    /**
     * 获取比赛排表
     *
     * @param string $gameId
     * @param string $startTime
     * @param string $endTime
     * @return array|false
     * @throws GuzzleException
     */
    public static function matchList($gameId, $startTime, $endTime)
    {
        return self::get('https://cbs-u.sports.cctv.com/pc/game/date_game_list', [
            'client' => 'pc',
            'startTime' => $startTime,
            'endTime' => $endTime,
            'leagueId' => $gameId,
            'ran' => now()->valueOf(),
        ]);
    }

}
