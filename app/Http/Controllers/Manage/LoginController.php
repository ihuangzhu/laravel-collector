<?php

namespace App\Http\Controllers\Manage;


class LoginController extends BaseController
{

    /**
     * 登录
     */
    public function login()
    {
        if (request()->isMethod('post')) {
//            dd(request()->all());

            return redirect(url('/manage'));
        }

        return view('manage.login.index');
    }

    /**
     * 登录
     */
    public function logout()
    {
        // todo logout

        return redirect(url('/manage/login'));
    }

}
