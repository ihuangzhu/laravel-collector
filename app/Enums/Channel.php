<?php


namespace App\Enums;


class Channel
{

    // 类型
    const CST = 0; // 自定义
    const LPL = 1; // 英雄联盟
    const CCTV = 2; // 央视体育

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
            self::CST => '自定义',
            self::LPL => '英雄联盟',
            self::CCTV => '央视体育',
        ];

        if (is_null($key)) return $map;
        return $map[$key] ?? $def;
    }

}
