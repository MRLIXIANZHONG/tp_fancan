<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25/025
 * Time: 15:33
 */

namespace app\home\controller;


use think\Controller;

class Login extends Controller
{
    public function login()
    {
        return view('login');
    }
}