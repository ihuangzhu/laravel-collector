<?php


namespace App\Enums;


class GameStatus
{

    // 比赛状态
    const DEFAULT = 0; // 未开始
    const PREPARE = 1; // 未开始
    const START = 2; // 已开始
    const END = 3; // 已结束

    /**
     * 虚拟币
     *
     * @param int $key
     * @param bool $def
     * @return array|bool|mixed
     */
    public static function getMap($key = null, $def = null)
    {
        $map = [
            self::DEFAULT => '未开始',
            self::PREPARE => '未开始',
            self::START => '已开始',
            self::END => '已结束',
        ];

        if (is_null($key)) return $map;
        return $map[$key] ?? $def;
    }

}
