<?php
/**
 * 宠物验证场景
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 11:20
 */

namespace app\home\validate;


class PetValidate extends Validate
{
    /**
     * 定义验证规则
     * @access private
     */
    private $_rule = [
        'tel' => 'mobile',
        'ChannelId' => ['integer', 'gt' => 0],
        'title' => ['require', 'max' => 25],
        'info' => ['require']
    ];
    /**
     * 定义提示信息
     * @access private
     */
    private $_message  =   [
        'tel' => '联系电话是您的必填项!',
        'ChannelId.integer'     => '请选择宠物类别!',
        'ChannelId.gt'     => '请选择宠物类别!',
        'title.require'   => '标题是您的必填项!',
        'title.max'   => '您填写的标题过长!',
        'info.require'  => '宠物详细描述是您的必填项!'
    ];
    /**
     * 定义场景
     * @access protected
     */
    protected $scene = [
        'save' => ['tel', 'ChannelId','title', 'info'],
    ];

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