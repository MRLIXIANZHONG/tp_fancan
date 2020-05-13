<?php
/**
 * 车辆买卖验证场景
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 11:20
 */

namespace app\home\validate;


class CarValidate extends Validate
{
    /**
     * 定义验证规则
     * @access private
     */
    private $_rule = [
        'tel' => 'mobile',
        'ClassId' => ['integer', 'gt' => 0],
        'Infokind' => ['integer', 'gt' => 0],
        'source' => ['integer', 'gt' => 0],
        'brandN' => ['integer', 'gt' => 0],
        'Price' => ['integer', 'gt' => 0],
        'mileage' => ['integer', 'gt' => 0],
        'IsHaveCar' => ['integer', 'gt' => 0],
        'pinHour' => ['integer', 'gt' => 0],
        'pinMinute' => ['integer', 'gt' => 0],
        'ZuoWei' => ['integer', 'gt' => 0],
        'LicenseYear' => ['require'],
        'QiDian' => ['require'],
        'ZhongDian' => ['require'],
        'deparTime' => ['require'],
        'Title' => ['require', 'length' => '5,25']
    ];
    /**
     * 定义提示信息
     * @access private
     */
    private $_message  =   [
        'tel'               => '联系电话是您的必填项!',
        'ClassId.integer'   => '请选择类别',
        'ClassId.gt'        => '请选择类别',
        'Infokind.integer'  => '请选择信息类别',
        'Infokind.gt'       => '请选择信息类别',
        'source.integer'    => '请选择来源!',
        'source.gt'         => '请选择来源!',
        'brandN.integer'    => '请选择品牌!',
        'brandN.gt'         => '请选择品牌!',
        'Price.integer'     => '价格是您的必填项!',
        'Price.gt'          => '价格是您的必填项!',
        'mileage.integer'   => '行驶里程是您的必填项!',
        'mileage.gt'        => '行驶里程是您的必填项!',
        'IsHaveCar.integer' => '拼车人是您的必填项!',
        'IsHaveCar.gt'      => '拼车人是您的必填项!',
        'pinHour.integer'   => '选择小时是您的必填项!',
        'pinHour.gt'        => '选择小时是您的必填项!',
        'pinMinute.integer' => '选择分钟是您的必填项!',
        'pinMinute.gt'      => '选择分钟是您的必填项!',
        'ZuoWei.integer'    => '提供座位是您的必填项!',
        'ZuoWei.gt'         => '提供座位是您的必填项!',
        'LicenseYear.require' => '上牌时间是您的必填项!',
        'QiDian.require'    => '起点是您的必填项!',
        'ZhongDian.require' => '终点是您的必填项!',
        'deparTime.require' => '出发时间是您的必填项!',
        'Title.require'     => '标题是您的必填项!',
        'Title.length'      => '标题长度应为5-25个字符请确认'
    ];
    /**
     * 定义场景
     * @access protected
     */
    protected $scene = [
        'car_save'  =>  ['tel', 'ClassId', 'Infokind', 'source', 'brandN', 'Price', 'mileage', 'LicenseYear', 'Title'],
        'project_save'  =>  ['tel', 'ClassId', 'Infokind', 'source', 'Price', 'Title'],
        'carpool_save'  =>  ['tel', 'IsHaveCar', 'Infokind', 'QiDian', 'ZhongDian', 'deparTime','pinHour', 'pinMinute', 'ZuoWei']
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