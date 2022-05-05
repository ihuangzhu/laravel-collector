<?php


namespace App\Helpers;

use GuzzleHttp\Exception\GuzzleException;

class LplHelper extends RequestHelper
{

    /**
     * 获取所有参赛队伍
     *
     * @return array|false
     * @throws GuzzleException
     */
    public static function teamList()
    {
        return self::get('https://lpl.qq.com/web201612/data/LOL_MATCH2_TEAM_LIST.js');
    }

    /**
     * 获取参赛队伍详情
     *
     * @param string $teamId
     * @return array|false
     * @throws GuzzleException
     */
    public static function teamInfo($teamId)
    {
        return self::get("https://lpl.qq.com/web201612/data/LOL_MATCH{$teamId}_TEAM_TEAM8_INFO.js");
    }

    /**
     * 获取比赛列表
     *
     * @return array|false
     * @throws GuzzleException
     */
    public static function gameList()
    {
        return self::get('https://lpl.qq.com/web201612/data/LOL_SGameList_Info.js');
    }

    /**
     * 获取比赛排表
     *
     * @param string $gameId
     * @return array|false
     * @throws GuzzleException
     */
    public static function matchList($gameId)
    {
        return self::get("https://lpl.qq.com/web201612/data/LOL_MATCH2_MATCH_HOMEPAGE_BMATCH_LIST_{$gameId}.js");
    }

    /**
     * 获取比赛排表详情
     *
     * @param string $matchId
     * @return array|false
     * @throws GuzzleException
     */
    public static function matchDetail($matchId)
    {
        return self::get("https://lpl.qq.com/web201612/data/LOL_MATCH_DETAIL_{$matchId}.js");
    }

}
