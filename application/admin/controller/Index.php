<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Config;

class Index extends Controller
{
    public function index()
    {
        phpinfo();

    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
