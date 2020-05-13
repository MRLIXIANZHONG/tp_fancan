<?php
/**
 * 生活服务验证场景
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 11:20
 */

namespace app\home\validate;


class LiveValidate extends Validate
{
    /**
     * 定义验证规则
     * @access private
     */
    private $_rule = [
        'tel' => 'mobile',
        'ClassId' => ['integer', 'gt' => 0],
        'DX_1' => ['integer', 'gt' => 0],
        'sh_CompName' => ['require', 'max' => 25],
        'Title' => ['require', 'max' => 25],
        'info' => ['require', 'max' => 2000],
        'linkman' => ['require', 'length' => '2,10'],
    ];
    /**
     * 定义提示信息
     * @access private
     */
    private $_message  =   [
        'tel' => '联系电话是您的必填项!',
        'ClassId.integer'     => '请选择类别',
        'ClassId.gt'     => '请选择类别',
        'DX_1.integer'     => '请选择类别',
        'DX_1.gt'     => '请选择类别',
        'sh_CompName.require'   => '公司名称是您的必填项!',
        'sh_CompName.max'   => '您填写的公司名称过长!',
        'Title.require'   => '标题是您的必填项!',
        'Title.max'   => '您填写的标题过长!',
        'info.require'  => '详细描述是您的必填项!',
        'info.max'  => '您输入的详细描述字数不符合要求，请按要求填写',
        'linkman.require'  => '联系人是您的必填项!',
        'linkman.length'  => '联系人姓名长度为2-10个字符',


    ];
    /**
     * 定义场景
     * @access protected
     */
    protected $scene = [
        'personal_save'  =>  ['tel', 'ClassId', 'DX_1', 'Title', 'info', 'linkman'],
        'company_save'  =>  ['tel', 'ClassId', 'DX_1', 'sh_CompName', 'Title', 'info', 'linkman']
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