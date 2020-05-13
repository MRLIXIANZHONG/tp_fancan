<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/15/015
 * Time: 10:31
 */

namespace app\home\logic;

use think\facade\Cache;
use app\home\model\StorageTable;
use app\home\model\PostSetupTable;
use app\home\model\PostSearchTable;
use app\home\model\CarKindTable;
use app\home\model\CarBrandTable;
use app\home\model\CheliangTable;
use app\home\model\TrafficInfoTable;

use app\home\validate\CarValidate;

class CarLogic extends Logic
{
    use \app\home\traits\Common;

    public $secondurl="/post/cheliang/";

    public function getCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');

        if(!in_array($type_id, $this->get_id_data())){
            return '参数错误';
        }
        return $this->getCarSecondCreate($type_id, $is_login);
       /* $secondData =[1,9,3];
        if(in_array($type_id, $secondData)){
            return $this->getCarSecondCreate($type_id, $is_login);
        }else{
            return $this->getCarCreate($type_id, $is_login);
        }*/
    }
    /**
     * 添加页面展示
     * @param
     * @return
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getCarSecondCreate(int $type_id, int $is_login)
    {

        $IsPostFufei = 0;
        $tjprice_cid = 4;//车辆卖卖
        $sourceId = 9;//车辆
        if($type_id == 8){
            $tjprice_cid = 15;//拼车单独结算
            $sourceId = 15;
        }
        $tabCls = 3;//车辆卖卖
        $strTempLink = 0;//是否是商家
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, $sourceId, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
            //当前账号是否是车行
            if($is_login == 1){
                $resData = (new CheliangTable())->getInfo(session('username'));
                if(count($resData) > 0){
                    $strTempLink = 1;
                }
            }

        }
        //获取类别
        if(Cache::get($this->cachePrefix.'car_findData_'.$type_id)){
            $data['findData'] = Cache::get($this->cachePrefix.'car_findData_'.$type_id);
        }else{
            $data['findData'] = (new CarKindTable())->getByFid($type_id);
            Cache::set($this->cachePrefix.'car_findData_'.$type_id, $data['findData'], 600);
        }
        $data['useHot'] = 0;
        if($type_id == 1 || $type_id == 9 || $type_id == 3){

            if(Cache::get($this->cachePrefix.'car_brandData_'.$type_id)){
                $data['brandData'] = Cache::get($this->cachePrefix.'car_brandData_'.$type_id);
            }else{
                $data['brandData'] = (new CarBrandTable())->getFidList($type_id);
                Cache::set($this->cachePrefix.'car_brandData_'.$type_id, $data['brandData'], 600);
            }
            //dump($data['brandData']);
            //热门品牌
            if($type_id == 1 || $type_id == 9){

                if(Cache::get($this->cachePrefix.'car_hotData_'.$type_id)){
                    $data['hotData'] = Cache::get($this->cachePrefix.'car_hotData_'.$type_id);
                }else{
                    $data['hotData'] = (new CarBrandTable())->getHotList($type_id, 1);
                    Cache::set($this->cachePrefix.'car_hotData_'.$type_id, $data['hotData'], 600);
                }
                $data['useHot'] = 1;
            }
            //找到所有字母简写

            if(Cache::get($this->cachePrefix.'car_brandEnameData_'.$type_id)){
                $data['brandEnameData'] = Cache::get($this->cachePrefix.'car_brandEnameData_'.$type_id);
            }else{
                $data['brandEnameData'] = (new CarBrandTable())->getByFid($type_id);
                Cache::set($this->cachePrefix.'car_brandEnameData_'.$type_id, $data['brandEnameData'], 600);
            }
        }

        //获取电话

        if(Cache::get($this->cachePrefix.'service_comptel'.$tabCls)){
            $serData = Cache::get($this->cachePrefix.'service_comptel'.$tabCls);
            $data['service_comptel'] = $serData['ServicesTel'];
            $data['service_qq'] = $serData['ServicesQQ'];
        }else{
            $serData = $this->getServiceComptel($tabCls);
            $data['service_comptel'] = $serData['ServicesTel'];
            $data['service_qq'] = $serData['ServicesQQ'];
            Cache::set($this->cachePrefix.'service_comptel'.$tabCls, $serData, 600);
        }
        //dump($data['service_comptel']);exit;
        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'recom_adv'.$tjprice_cid)){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'recom_adv'.$tjprice_cid);
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'recom_adv'.$tjprice_cid, $data['recom_adv'], 600);
        }
        $data['strTempTitle']['kindname'] = $this->get_category_data($type_id);
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['strTempLink'] = $strTempLink;
        $data['tipName'] = '车辆';
        $data['topName'] = '车辆买卖';
        if($type_id == 8){
            $data['nowTime'] = time();
        }

        unset($resData);
        return $data;
    }
    /**
     * 车辆买卖处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getCarSave(array $params, int $is_login)
    {
        $tabCls = 3;
        $sourceId = 9;//车辆
        //格式化数据
        $data = $this->formatData($params);
        if(!is_array($data)){
            return $data;
        }
        //dump($data);exit;
        //当前账号是否是二手商家
        if($is_login == 1){
            $resData = (new CheliangTable())->getInfo(session('username'));
            if(count($resData) > 0){
                $data['linkman'] = $resData['linkman'];
                $data['tel'] = $resData['tel'];
                $data['qq'] = $resData['qq'];
            }
            unset($resData);
        }
        //dump($data);exit;
        //数据校验
        $validate = new CarValidate();
        $valType = 'car_save';
        if($data['Infokind'] == 4 || $data['Infokind'] == 7){
            $valType = 'project_save';
        }elseif($data['Infokind'] == 8){
            $valType = 'carpool_save';
        }
        if(!$validate->checkParams($valType,$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg'] && $data['telmsg'] != '888888'){
            if(!$this->checkMobileCode($data['tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($data['Infokind'] == 8){
            $sourceId = 15;//拼车
        }
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, $sourceId, session('username'), getIP());
            if(!$serverInfo){
                return '配置读取失败!';
            }
            $able_sumNum = $serverInfo['able_sumNum'];
            $able_todayNum = $serverInfo['able_todayNum'];
            if($able_sumNum > 0 && $able_todayNum > 0){
                $isPostFufei=0;//付费发布，0为不付费 1为付费发布
            }else{
                $isPostFufei=1;
            }
        }else{
            return '请登录后发布信息!';//请登录后
        }
        //是否禁发
        if($this->check_tel($data['tel'], session('username'))){
            return 6;
        }
        //qq禁发
        if(isset($data['qq']) && $data['qq']){
            if($this->check_qq($data['qq'])){
                return 10;
            }
        }


        $title = $data['Title'];
        $info = $data['info'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'car_save', 19);
            if($result['code'] == 1001){
                return '您发布的信息可能涉及非法信息,请重新发布!';
            }elseif($result['code'] == 1002){//转审
                // 检查白名单 认证会员不审核
                $isWhite = $this->checkWhite(session('username'), $tabCls);
                if(!$isWhite){
                    $ccoochk = 0;
                }
            }elseif($result['code'] == 1005){
                $title = $result['title'];
                $info = $result['info'];
            }

        }

        //获取系统审核级别
        $result = (new PostSetupTable())->getSetup($tabCls);
        if(count($result) > 0){
            if($result['ccoochk'] == 0){
                $ccoochk = 0;
            }
        }

        //敏感词替换
        $data['Title'] = $title;
        $data['info'] = $info;
        //发布间隔限制
        $is_interval = $this->check_fabu_interval($title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }
        //完整度
        $tmpInfoNum = $this->get_info_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }
        $data['infoNum'] = $tmpInfoNum;
        $data['Siteid'] = $this->site_id;
        $data['Username'] = session('username');
        $data['ccoochk'] = $ccoochk;
        $data['ip'] = getIP();
        //付费
        $data['isdel'] = 0;
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }
        if($data['Infokind'] != 8){
            unset($data['deparTime']);
        }
        //调用存储过程发布信息
        //$tempId = (new StorageTable())->exeEsInfoIUD($data);
        $tempId = (new TrafficInfoTable())->insertGetId($data);

        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            $type = 9;
            if($data['Infokind'] == 8){//拼车
                $type = 15;
            }
            (new StorageTable())->exePostSendCountU($type);
        }
        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'traffic_info');
            $urlLinkInfo = $this->secondurl.$tempId.'x.html';
            $postClassName = "车辆买卖";
            $logData['SiteId'] = $this->site_id;
            $logData['title'] = $title;
            $logData['classname'] = $postClassName;
            $logData['url'] = $urlLinkInfo;
            $logData['tel'] = $data['tel'];
            $logData['ip'] = getIP();
            (new PostSearchTable())->insert($logData);
            unset($data);
            unset($logData);
            return $tempId;
        }

    }

    /**
     * 二手信息格式化提交数据
     * @param string $id 职位id
     */
    public function formatData(array $params)
    {
        if(count($params) == 0) return false;
        foreach ($params as $key => &$val) {

            if($val == 'undefined'){
                $val = '';
            }
            if($val){
                $val = unescape($val);
            }
        }
        if($params['upLoadList']){
            $params['pic'] = rtrim($params['upLoadList'],'|');
            unset($params['upLoadList']);
        }else{
            $params['pic'] = '';
            unset($params['upLoadList']);
        }

        if($params['LicenseYear']){
            $tempData = explode('-', $params['LicenseYear']);
            $params['LicenseYear'] = $tempData[0];
            $params['LicenseMonth'] = $tempData[1];
        }

        $params['Infokind'] = (int)$params['Infokind'];
        $params['ClassId'] = (int)$params['ClassId'];
        $params['brandN'] = (int)$params['brandN'];
        $params['source'] = (int)$params['source'];
        $params['discharge'] = (int)$params['discharge'];
        $params['IsHaveCar'] = (int)$params['IsHaveCar'];
        $params['Pinche'] = (int)$params['Pinche'];
        $params['ZuoWei'] = (int)$params['ZuoWei'];

        return $params;
    }

    /**
     * 发布间隔限制
     * @param string $title 文章名称
     */
    public function check_fabu_interval($title)
    {
        $result = (new TrafficInfoTable())->getByTitle($title);
        return count($result);
    }

    /**
     * 获取二手信息完整度
     * @param array $data
     */
    public function get_info_num($data){
        $infoNum =70;
        if($data['source']){
            $infoNum += 5;
        }
        if($data['Price']){
            $infoNum += 5;
        }
        if(isset($data['linkman'])){
            if($data['linkman']){
                $infoNum += 5;
            }
        }
        if(isset($data['qq'])){
            if($data['qq']){
                $infoNum += 5;
            }
        }
        if($data['pic'] && (strpos($data['pic'], 'gif') !== false || strpos($data['pic'], 'jpg') !== false)){
            $infoNum += 10;
        }
        return $infoNum;
    }
    /**
     * 获取分类id数组
     * reurn array
     */
    public function get_id_data(){
        return [1,9,3,4,7,8];
    }
    /**
     * 获取分类数组
     * @param int $type
     */
    public function get_category_data($type){
        switch($type)
        {
            case 1:
                return "二手汽车";
                break;
            case 9:
                return "二手货车";
                break;
            case 3:
                return "面包车/客车";
                break;
            case 4:
                return "工程车/农用车";
                break;
            case 7:
                return "租赁/汽车配件";
                break;
            case 8:
                return "拼车/顺风车";
                break;
        }
    }
}