<?php


namespace App\Helpers;

use GuzzleHttp\Exception\GuzzleException;

class CstHelper extends RequestHelper
{

    /**
     * 请求(GET)
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array|false
     * @throws GuzzleException
     */
    public static function get($url, $data = [], $headers = [])
    {
        return parent::get($url, $data, $headers);
    }

    /**
     * 请求(POST)
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array|false
     * @throws GuzzleException
     */
    public static function post($url, $data = [], $headers = [])
    {
        return parent::post($url, $data, $headers);
    }

}
