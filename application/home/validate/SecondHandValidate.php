<?php
/**
 * 二手交易验证场景
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 11:20
 */

namespace app\home\validate;


class SecondHandValidate extends Validate
{
    /**
     * 定义验证规则
     * @access private
     */
    private $_rule = [
        'tel' => 'mobile',
        'oTel' => 'mobile',
        'ClassId' => ['integer', 'gt' => 0],
        'ChannelId' => ['integer', 'gt' => 0],
        'Chengse' => ['integer', 'gt' => 0],
        'Title' => ['require', 'length' => '5,25'],
        'oTitle' => ['require', 'length' => '5,25'],
        'oAddRess' => ['require', 'length' => '2,30'],
        'info' => ['require', 'max' => 2000],
        'oInfo' => ['require', 'max' => 2000]
    ];
    /**
     * 定义提示信息
     * @access private
     */
    private $_message  =   [
        'tel' => '联系电话是您的必填项!',
        'oTel' => '联系电话是您的必填项!',
        'ClassId.integer'     => '请选择类别',
        'ClassId.gt'     => '请选择类别',
        'ChannelId.integer'     => '请选择信息类别',
        'ChannelId.gt'     => '请选择信息类别',
        'Chengse.integer'     => '请选择二手物品成色!',
        'Chengse.gt'     => '请选择二手物品成色!',
        'Title.require'   => '标题是您的必填项!',
        'Title.length'   => '标题长度应为5-25个字符请确认',
        'oTitle.require'   => '标题是您的必填项!',
        'oTitle.length'   => '标题长度应为5-25个字符请确认',
        'oAddRess.require'   => '地址长度应为5-25个字符请确认!',
        'oAddRess.length'   => '地址长度应为5-25个字符请确认!',
        'info.require'  => '详细描述是您的必填项!',
        'info.max'  => '您输入的详细描述字数不符合要求，请按要求填写',
        'oInfo.require'  => '详细描述是您的必填项!',
        'oInfo.max'  => '您输入的详细描述字数不符合要求，请按要求填写'
    ];
    /**
     * 定义场景
     * @access protected
     */
    protected $scene = [
        'second_save'  =>  ['tel', 'ClassId', 'ChannelId', 'Chengse', 'Title', 'info'],
        'second_recovery_save'  =>  ['oTel', 'oTitle', 'oAddRess', 'oInfo'],
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