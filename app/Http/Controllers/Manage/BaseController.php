<?php

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    // 返回状态
    const CODE_SUCCESS = 0; // 执行成功
    const CODE_FAILED = 1; // 执行失败

    /**
     * @param $data
     * @param int $status
     * @param array $header
     * @return mixed
     */
    public function respond($data, $status = Response::HTTP_OK, $header = [])
    {
        return response()->json($data, $status, $header);
    }

    /**
     * 返回错误
     *
     * @param $msg
     * @param mixed $data
     * @param int $code
     * @param array $ext
     * @return mixed
     */
    public function failed($msg = 'failed', $data = [], $code = self::CODE_FAILED, $ext = [])
    {
        return $this->respond(array_merge([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ], $ext));
    }

    /**
     * 返回成功
     *
     * @param string $msg
     * @param int $code
     * @return mixed
     */
    public function message($msg = 'success', $code = self::CODE_SUCCESS)
    {
        return $this->respond([
            'code' => $code,
            'msg' => $msg,
            'data' => null,
        ]);
    }

    /**
     * 返回成功，带数据
     *
     * @param mixed $data
     * @param string $msg
     * @param int $code
     * @param array $ext
     * @return mixed
     */
    public function success($data, $msg = 'success', $code = self::CODE_SUCCESS, $ext = [])
    {
        return $this->respond(array_merge([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ], $ext));
    }

}
