<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25/025
 * Time: 15:19
 */

namespace app\admin\controller;


use think\Controller;

class Login extends Controller
{
    public function login()
    {
        return view('login');
    }
}