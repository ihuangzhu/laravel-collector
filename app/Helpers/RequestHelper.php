<?php


namespace App\Helpers;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

abstract class RequestHelper
{
    // 最大加载时间
    const TIMEOUT = 300;

    /**
     * 请求(GET)
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array|false
     * @throws GuzzleException
     */
    protected static function get($url, $data = [], $headers = [])
    {
        $httpClient = new HttpClient(['verify' => false, 'timeout' => self::TIMEOUT]);
        $response = $httpClient->get($url, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::QUERY => $data,
        ]);
        $responseBody = $response->getBody()->getContents();
        return self::parseResponse($responseBody);
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
    protected static function post($url, $data = [], $headers = [])
    {
        $httpClient = new HttpClient(['verify' => false, 'timeout' => self::TIMEOUT]);
        $response = $httpClient->post($url, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::FORM_PARAMS => $data,
        ]);
        $responseBody = $response->getBody()->getContents();
        return self::parseResponse($responseBody);
    }

    /**
     * 处理请求结果数据
     *
     * @param string $response
     * @return array|false
     */
    protected static function parseResponse($response)
    {
        if (preg_match('/(?={).*(?<=})/', $response, $matches)) {
            return json_decode($matches[0], true) ?: [];
        }

        Log::error('Unmatched json string: ' . $response);
        return false;
    }
}
