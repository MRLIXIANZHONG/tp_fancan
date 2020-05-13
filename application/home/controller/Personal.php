<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/12/30/030
 * Time: 17:00
 */

namespace app\home\controller;

use think\facade\Request;
use app\home\logic\PersonalLogic;

class Personal extends Base
{
    /**
     * 我的发布
     */
    public function myRelease()
    {
        $result = (new PersonalLogic())->getReleaseList($this->getParams(), $this->is_login);
        if(!is_array($result)){
            return redirect('/post');
        }
        return $this->fetch('release', $result);
    }
    /**
     * 我的发布
     */
    public function test(){
        (new PersonalLogic())->getMenuAuth();
    }
}