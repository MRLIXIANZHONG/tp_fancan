<?php
/**
 * 房产验证场景
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 11:20
 */

namespace app\home\validate;


class HouseValidate extends Validate
{
    /**
     * 定义验证规则
     * @access private
     */
    private $_rule = [
        'Tel' => 'mobile',
        'oTitle' => ['require', 'max' => 25],
        'Mianji' => ['integer', 'gt' => 0],
        'oInfo' => ['require', 'max' => 2000],
        'address' => ['require', 'max' => 30],
        'SellingPoint' => ['max' => 500],
        'SaleMentality' => ['max' => 500],
        'ServiceInfo' => ['max' => 500]
    ];
    /**
     * 定义提示信息
     * @access private
     */
    private $_message  =   [
        'Tel' => '联系电话是您的必填项!',
        'oTitle.require'   => '标题是您的必填项!',
        'oTitle.max'   => '您填写的标题过长!',
        'Mianji.integer'     => '面积大小应为1-10000之间的数字!',
        'Mianji.gt'     => '面积大小应为1-10000之间的数字!',
        'oInfo.require'  => '详细描述是您的必填项!',
        'oInfo.max'  => '您输入的详细描述字数不符合要求，请按要求填写',
        'address.require'  => '地址字符应在2-30字符内请确认!',
        'SellingPoint.max'  => '核心卖点信息不能大于500个字!',
        'SaleMentality.max'  => '业主心态信息不能大于500个字!',
        'ServiceInfo.max'  => '服务介绍信息不能大于500个字!'
    ];
    /**
     * 定义场景
     * @access protected
     */
    protected $scene = [
        'shop_save'  =>  ['tel', 'title', 'info', 'address'],
        'sell_save'  =>  ['tel', 'title', 'info', 'SellingPoint', 'SaleMentality', 'ServiceInfo', 'address'],
        'lease_save' =>  ['tel', 'title', 'info', 'ServiceInfo', 'address'],
        'buy_save'   =>  ['tel', 'title', 'info', 'address'],
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