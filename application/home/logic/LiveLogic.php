<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/15/015
 * Time: 10:31
 */

namespace app\home\logic;

use think\facade\Cache;
use app\home\model\LiveInfoTable;
use app\home\model\PetInfoTable;
use app\home\model\HouseZoneTable;
use app\home\model\LiveKindNewTable;
use app\home\model\PostSetupTable;
use app\home\model\PostQiYeKuTable;
use app\home\model\PostSearchTable;
use app\home\model\StorageTable;
use app\home\validate\LiveValidate;

class LiveLogic extends Logic
{
    use \app\home\traits\Common;

    public $liveurl="/post/shenghuo/";

    /**
     * 添加页面展示
     * @param
     * @return
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');

        if(Cache::get($this->cachePrefix.'live_findData_one')){
            $typeData = Cache::get($this->cachePrefix.'live_findData_one');
        }else{
            $typeData = (new LiveKindNewTable())->getByFid(0);
            $typeData = get_arr_column($typeData, 'id');
            Cache::set($this->cachePrefix.'live_findData_one', $typeData, 600);
        }

        if(!in_array($type_id, $typeData)){
            return '参数错误';
        }

        $IsPostFufei = 0;
        $tjprice_cid = 5;//生活
        $tabCls = 4;//生活
        $PostPower = 0;

        //dump($findData);exit;
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 10, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
            //当前账号是否是企业
            $resData = (new PostQiYeKuTable())->getInfo(session('username'), $tabCls);
            if(count($resData) > 0){
                $data['compname'] = $resData['compname'];
                $data['linkman'] = $resData['linkman'];
                $data['tel'] = $resData['tel'];
                $data['qq'] = $resData['qq'];
            }
        }
        //获取一级分类名称
        if(Cache::get($this->cachePrefix.'live_strTempTitle_'.$type_id)){
            $data['strTempTitle'] = Cache::get($this->cachePrefix.'live_strTempTitle_'.$type_id);
        }else{
            $data['strTempTitle'] = (new LiveKindNewTable())->getById($type_id);
            Cache::set($this->cachePrefix.'live_strTempTitle_'.$type_id, $data['strTempTitle'], 600);
        }
        //二级类别 fid 0 一级类别
        if(Cache::get($this->cachePrefix.'live_findData_'.$type_id)){
            $data['findData'] = Cache::get($this->cachePrefix.'live_findData_'.$type_id);
        }else{
            $data['findData'] = (new LiveKindNewTable())->getByFid($type_id);
            Cache::set($this->cachePrefix.'live_findData_'.$type_id, $data['findData'], 600);
        }
        //获取所在区域
        if(Cache::get($this->cachePrefix.'house_create_zone_data')){
            $data['zone_data'] = Cache::get($this->cachePrefix.'house_create_zone_data');
        }else{
            $data['zone_data'] = (new HouseZoneTable())->getHouseZone();
            Cache::set($this->cachePrefix.'house_create_zone_data', $data['zone_data'], 600);
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
        //是否可以付费发布信息
        $result = (new PostSetupTable())->getSetup($tabCls);
        if(count($result) > 0){
            $PostPower = $result['PostPower'];
        }

        //置顶推荐数据
         if(Cache::get($this->cachePrefix.'live_create_recom_adv')){
             $data['recom_adv'] = Cache::get($this->cachePrefix.'live_create_recom_adv');
         }else{
             $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
             Cache::set($this->cachePrefix.'live_create_recom_adv', $data['recom_adv'], 600);
         }
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['PostPower'] = $PostPower;
        $data['tipName'] = '生活';
        $data['topName'] = '生活服务';
        unset($resData);
        return $data;
    }
    /**
     * 生活服务处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getLiveSave(array $params, int $is_login)
    {
        $tabCls = 4;
        //格式化数据
        $data = $this->formatData($params);
        if(!$data){
            return '格式化数据失败';
        }
        //数据校验
        $validate = new LiveValidate();
        $checkScene = 'personal_save';
        if($data['DX_1'] == 2){
            $checkScene = 'company_save';
        }else{
            $data['sh_CompName'] = '';//个人没有公司名称
        }
        if(!$validate->checkParams($checkScene,$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg']){
            if(!$this->checkMobileCode($data['tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, 10, session('username'), getIP());
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
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'live_save', 19);
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

        $userAgent = (new StorageTable())->exeReleaseCheck($data['Title'], replace_title($data['Title']), 'live_info', $data['tel'], $_SERVER['HTTP_USER_AGENT']);
        if(count($userAgent) > 0){
            if($userAgent['code'] == 1001){
                $ccoochk = 0;
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
        $data['source'] = $data['DX_1'];
        $data['ip'] = getIP();
        unset($data['DX_1']);
        //付费
        $data['isdel'] = 0;
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }

        //调用存储过程发布信息
        $tempId = (new StorageTable())->exeLiveInfoIUD($data);
        if($tempId == 1008){
            return '存储过程执行失败';
        }
        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        //存储过程已经调用了
        /*if($isPostFufei == 0){
            (new StorageTable())->exePostSendCountU(10);
        }*/

        if($tempId > 0 && $tempId != 1008){
            $urlLinkInfo = $this->liveurl.$tempId.'x.html';
            $postClassName = "生活服务";
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
     * 服务生活格式化提交数据
     * @param string $id 职位id
     */
    public function formatData(array $params)
    {
        if(count($params) == 0) return false;
        foreach ($params as $key => &$val) {
            if($val == 'undefined'){
                $val = '';
            }
        }
        if($params['upLoadList']){
            $params['pic'] = rtrim(unescape($params['upLoadList']),'|');
            unset($params['upLoadList']);
        }else{
            $params['pic'] = '';
            unset($params['upLoadList']);
        }


        $params['Title'] = unescape($params['Title']);
        $params['sh_CompName'] = unescape($params['sh_CompName']);
        $params['address'] = unescape($params['address']);
        $params['info'] = unescape($params['info']);
        $params['linkman'] = unescape($params['linkman']);
        $params['ClassId2_C'] = unescape($params['ClassId2_C']);

        $params['tel'] = (int)$params['tel'];
        $params['areaId'] = (int)$params['areaId'];
        $params['DX_1'] = (int)$params['DX_1'];
        $params['Infokind'] = (int)$params['Infokind'];
        $params['ClassId'] = (int)$params['ClassId'];

        if($params['ClassId2_S'] > 0){

        }else{

        }


        return $params;
    }
    /**
     * 发布间隔限制
     * @param string $title 文章名称
     */
    public function check_fabu_interval($title)
    {
        $result = (new LiveInfoTable())->getByTitle($title);
        return count($result);
    }
    /**
     * 获取生活服务完整度
     * @param array $data
     */
    public function get_info_num($data){
        $infoNum =50;
        if($data['sh_CompName']){
            $infoNum += 10;
        }
        if($data['linkman']){
            $infoNum += 10;
        }
        if($data['email']){
            $infoNum += 10;
        }
        if($data['qq']){
            $infoNum += 10;
        }
        if($data['pic'] && (strpos($data['pic'], 'gif') !== false || strpos($data['pic'], 'jpg') !== false)){
            $infoNum += 10;
        }

        return $infoNum;
    }
}