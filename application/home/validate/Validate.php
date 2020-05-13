<?php
/**
 * 验证场景模式基础类
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 11:20
 */

namespace app\home\validate;

use verify\Verify;

class Validate extends \think\Validate
{
    protected static $isLoaded = false;
    /**
     * 定义验证规则
     * @access protected
     */
    protected $rule = [

    ];
    /**
     * 定义提示信息
     * @access protected
     */
    protected $message = [

    ];

    /**
     * 判断手机号码是否正确(如果是空则为正确)
     * @param $value
     * @return bool|string
     */
    protected function checkMobile($value){
        if(Verify::isNullOrEmpty($value)) return true;
        if(Verify::isMobile($value)) return true;
        return '手机号码格式错误';
    }
    /**
     * 根据场景定义数据
     * @access public
     * @param string 场景
     * @param array 校验数据
     */
    public function checkParams($scene, array $params):bool{
        if(!self::$isLoaded){
            $this->rule($this->_rule);
            $this->message($this->_message);
        }
        return $this->scene($scene)->check($params);
    }
}