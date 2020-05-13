<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/18/018
 * Time: 13:59
 */

namespace app\home\controller;

use app\home\logic\LiveLogic;
use app\home\logic\PetLogic;
use think\facade\Request;
use app\home\logic\HouseLogic;
use app\home\logic\CarLogic;
use app\home\logic\SecondHandLogic;
use app\home\logic\CommonLogic;

class Release extends Base
{
    /**
     * 宠物发布页面
     * @access public
     */
    public function create()
    {
        if(!Request::param('type_id')){
            return json('非法参数');
        }
        $result = (new PetLogic())->getHandleCreate($this->getParams(), $this->is_login);
        if(!is_array($result)){
            return json($result);
        }
        $result['is_login'] = $this->is_login;
        $result['url'] = Request::url(true);
        $result['class1'] = 8;
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        return $this->fetch('create', $result);

    }
    /**
     * 获取宠物提交数据
     * @access public
     */
    public function pet_cat_save()
    {
        $result = (new PetLogic())->getHandleSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 房产交易发布页面
     * @access public
     */
    public function houseCreate()
    {
        if(!Request::param('type_id')){
            return json('非法参数');
        }
        $result = (new HouseLogic())->getCreate($this->getParams(), $this->is_login);
        if(!is_array($result)){
            return json($result);
        }
        $result['url'] = Request::url(true);
        $result['class1'] = 3;
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        return $this->fetch($this->loadTemplate($result['type_id']), $result);
    }
    /**
     * 房产交易店铺转让处理提交数据
     * @access public
     */
    public function shopSave()
    {
        $result = (new HouseLogic())->getShopSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 房产交易房屋出售处理提交数据
     * @access public
     */
    public function houseSellSave()
    {
        $result = (new HouseLogic())->getHouseSellSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 房产交易房屋出租处理提交数据
     * @access public
     */
    public function houseLeaseSave()
    {
        $result = (new HouseLogic())->getHouseLeaseSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 房产交易房屋求购，求租，处理提交数据
     * @access public
     */
    public function houseBuySave()
    {
        $result = (new HouseLogic())->getHouseBuySave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 加载房屋出租模板
     * @access public
     */
    public function loadTemplate($type_id)
    {
        switch($type_id)
        {
            case 1://房屋出售
                return "house_sell";
                break;
            case 2://房屋出租
                return "house_lease";
                break;
            case 3://房屋求购
                return "house_buy";
                break;
            case 4://房屋求租
                return "house_buy";
                break;
            case 5://店铺转让
                return 'shop_create';
                break;
        }
    }
    /**
     * 生活服务发布页面
     * @access public
     */
    public function liveCreate()
    {
        if(!Request::param('type_id')){
            return json('非法参数');
        }
        $result = (new LiveLogic())->getCreate($this->getParams(), $this->is_login);
        if(!is_array($result)){
            return json($result);
        }
        $result['is_login'] = $this->is_login;
        $result['url'] = Request::url(true);
        $result['class1'] = 4;
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        return $this->fetch('live_create', $result);

    }
    /**
     * 生活服务处理提交数据
     * @access public
     */
    public function liveSave()
    {
        $result = (new LiveLogic())->getLiveSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 二手信息发布页面
     * @access public
     */
    public function secondHandCreate()
    {
        if(!Request::param('type_id')){
            return json('非法参数');
        }
        $result = (new SecondHandLogic())->getCreate($this->getParams(), $this->is_login);
        if(!is_array($result)){
            return json($result);
        }
        $result['is_login'] = $this->is_login;
        $result['url'] = Request::url(true);
        $result['class1'] = 2;
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        return $this->fetch($this->loadSecondTemplate($result['type_id']), $result);
    }
    /**
     * 二手信息处理提交数据
     * @access public
     */
    public function secondHandSave()
    {
        $result = (new SecondHandLogic())->getSecondSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 二手信息求购回收处理提交数据
     * @access public
     */
    public function secondRecoveryHandSave()
    {
        $result = (new SecondHandLogic())->getRecoverySecondSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 加载房屋出租模板
     * @access public
     */
    public function loadSecondTemplate($type_id)
    {
        switch($type_id)
        {
            case 9://求购回收
                return "second__recovery_create";
                break;
            default:
                return "second_create";
        }
    }
    /**
     * 车辆买卖发布页面
     * @access public
     */
    public function carCreate()
    {
        if(!Request::param('type_id')){
            return json('非法参数');
        }
        $result = (new CarLogic())->getCreate($this->getParams(), $this->is_login);
        if(!is_array($result)){
            return json($result);
        }
        $result['is_login'] = $this->is_login;
        $result['url'] = Request::url(true);
        $result['class1'] = 5;
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        return $this->fetch('car_second', $result);
        //return $this->fetch($this->loadCarTemplate($result['type_id']), $result);
    }
    /**
     * 车辆买卖处理提交数据
     * @access public
     */
    public function carSave()
    {
        $result = (new CarLogic())->getCarSave($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 分类发布验证码
     * @access public
     */
    public function sendCode()
    {
        $result =  (new CommonLogic())->sendVerificationCode($this->getParams(), $this->is_login);
        return json($result);
    }
    /**
     * 校验验证码
     * @access public
     */
    public function checkCode()
    {
        $result =  (new CommonLogic())->checkCode($this->getParams(), $this->is_login);
        return json($result);
    }
}