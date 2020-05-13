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
use app\home\model\HouseZoneTable;
use app\home\model\PostSetupTable;
use app\home\model\PostSearchTable;
use app\home\model\ErshouSjTable;
use app\home\model\EsKindTable;
use app\home\model\IdleInfoTable;
use app\home\model\SmsgTable;
use app\home\validate\SecondHandValidate;

class SecondHandLogic extends Logic
{
    use \app\home\traits\Common;

    public $secondurl="/post/ershou/";

    public function getCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');

        if(!in_array($type_id, $this->get_id_data())){
            return '参数错误';
        }

        if($type_id == 9){
            return $this->getSecondRecoveryCreate($type_id, $is_login);
        }else{
            return $this->getSecondCreate($type_id, $is_login);
        }
    }
    /**
     * 添加页面展示
     * @param
     * @return
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getSecondCreate(int $type_id, int $is_login)
    {

        $IsPostFufei = 0;
        $tjprice_cid = 3;//二手
        $tabCls = 2;//二手
        $strTempLink = 0;//是否是商家
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 8, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
            //当前账号是否是二手商家
            $resData = (new ErshouSjTable())->getInfo(session('username'));
            if(count($resData) > 0){
                $strTempLink = 1;
            }
        }
        //获取类别
        if(Cache::get($this->cachePrefix.'second_hand_findData_'.$type_id)){
            $data['findData'] = Cache::get($this->cachePrefix.'second_hand_findData_'.$type_id);
        }else{
            $data['findData'] = (new EsKindTable())->getByPid(1, $type_id);
            Cache::set($this->cachePrefix.'second_hand_findData_'.$type_id, $data['findData'], 600);
        }

        //获取三级类别
        $temp = [];
        if(Cache::get($this->cachePrefix.'second_hand_chirData_'.$type_id)){
            $data['chirData'] = Cache::get($this->cachePrefix.'second_hand_chirData_'.$type_id);
        }else{
            $data['chirData'] = (new EsKindTable())->getByPidLevel(2, $type_id);
            if(count($data['chirData']) > 0){
                foreach ($data['chirData'] as  $key=> $val) {
                    foreach ($val as  $k=> $v) {
                        $temp[$key][] = $v;
                    }

                }
            }
            Cache::set($this->cachePrefix.'second_hand_chirData_'.$type_id, $temp, 600);
            $data['chirData'] = $temp;
        }
        $data['chirData'] = json_encode($data['chirData'], JSON_UNESCAPED_UNICODE);
        //dump($data['chirData']);exit;
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

        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'second_hand_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'second_hand_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'second_hand_create_recom_adv', $data['recom_adv'], 600);
        }
        $data['strTempTitle']['kindname'] = $this->get_category_data($type_id);
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['strTempLink'] = $strTempLink;
        $data['tipName'] = '二手';
        $data['topName'] = '二手信息';

        unset($resData);
        return $data;
    }
    /**
     * 求购回收添加页面展示
     * @param
     * @return
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getSecondRecoveryCreate(int $type_id, int $is_login)
    {

        $IsPostFufei = 0;
        $tjprice_cid = 3;//二手
        $tabCls = 2;//二手
        $strTempLink = 0;//是否是商家
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 8, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
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

        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'second_hand_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'second_hand_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'second_hand_create_recom_adv', $data['recom_adv'], 600);
        }
        $data['strTempTitle']['kindname'] = $this->get_category_data($type_id);
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['strTempLink'] = $strTempLink;
        $data['tipName'] = '二手';
        $data['topName'] = '二手信息';

        unset($resData);
        return $data;
    }
    /**
     * 二手信息处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getSecondSave(array $params, int $is_login)
    {
        $tabCls = 2;
        //格式化数据
        $data = $this->formatData($params);
        if(!is_array($data)){
            return $data;
        }
        //当前账号是否是二手商家
        $resData = (new ErshouSjTable())->getInfo(session('username'));
        if(count($resData) > 0){
            $data['linkman'] = $resData['linkman'];
            $data['tel'] = $resData['tel'];
            $data['qq'] = $resData['qq'];
        }
        unset($resData);
        //数据校验
        $validate = new SecondHandValidate();
        if(!$validate->checkParams('second_save',$data)){
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
            $serverInfo = get_post_fabu_root($this->site_id, 8, session('username'), getIP());
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
        if($this->check_qq($data['qq'])){
            return 10;
        }

        $title = $data['Title'];
        $info = $data['info'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'second_save', 19);
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
        $data['Ccoochk'] = $ccoochk;
        $data['ip'] = getIP();
        //付费
        $data['isdel'] = 0;
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }

        //调用存储过程发布信息
        //$tempId = (new StorageTable())->exeEsInfoIUD($data);
        $tempId = (new IdleInfoTable())->insertGetId($data);

        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            (new StorageTable())->exePostSendCountU(8);
        }
        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'idle_info');
            $urlLinkInfo = $this->secondurl.$tempId.'x.html';
            $postClassName = "二手信息";
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
     * 二手信息求购回收处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getRecoverySecondSave(array $params, int $is_login)
    {
        $tabCls = 2;
        //格式化数据
        $data = $this->formatRecoveryData($params);
        if(!is_array($data)){
            return $data;
        }
        //数据校验
        $validate = new SecondHandValidate();
        if(!$validate->checkParams('second_recovery_save',$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg']){
            if(!$this->checkMobileCode($data['oTel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, 8, session('username'), getIP());
            if(!$serverInfo){
                return '配置读取失败!';
            }
            $able_sumNum = $serverInfo['able_sumNum'];
            $able_todayNum = $serverInfo['able_todayNum'];
            if($able_sumNum > 0 && $able_todayNum > 0){
                $isPostFufei=0;//付费发布，0为不付费 1为付费发布
            }else{
                $isPostFufei=1;
                return 6;
            }
        }else{
            return '请登录后发布信息!';//请登录后
        }
        //是否禁发
        if($this->check_tel($data['oTel'], session('username'))){
            return 4;
        }
        //qq禁发
        if($this->check_qq($data['oQQ'])){
            return 4;
        }

        $title = $data['oTitle'];
        $info = $data['oInfo'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'recovery_second_save', 19);
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
        $data['oTitle'] = $title;
        $data['oInfo'] = $info;
        //发布间隔限制
        $is_interval = $this->check_fabu_recovery_interval($title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }
        //完整度
        $tmpInfoNum = $this->get_info_recovery_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }
        $data['infoNum'] = $tmpInfoNum;
        $data['SiteId'] = $this->site_id;
        $data['UserName'] = session('username');
        $data['IsChk'] = $ccoochk;
        $data['IP'] = getIP();
        //付费
        $data['isdel'] = 0;
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }

        $tempId = (new SmsgTable())->insertGetId($data);
        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            (new StorageTable())->exePostSendCountU(8);
        }
        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'smsg');
            $urlLinkInfo = $this->secondurl.$tempId.'x.html';
            $postClassName = "二手信息";
            $logData['SiteId'] = $this->site_id;
            $logData['title'] = $title;
            $logData['classname'] = $postClassName;
            $logData['url'] = $urlLinkInfo;
            $logData['tel'] = $data['oTel'];
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
            $params['Pic'] = rtrim($params['upLoadList'],'|');
            unset($params['upLoadList']);
        }else{
            $params['Pic'] = '';
            unset($params['upLoadList']);
        }

        $params['areaid'] = (int)$params['areaid'];
        $params['ChannelId'] = (int)$params['ChannelId'];
        $params['ClassId'] = (int)$params['ClassId'];
        $params['oTherId'] = (int)$params['oTherId'];
        $params['Chengse'] = (int)$params['Chengse'];
        $params['IsSource'] = (int)$params['IsSource'];
        $params['IsExchange'] = (int)$params['IsExchange'];
        if($params['IsExchange'] == 1){
            if(!$params['HtmlExchange']){
                return '您已选择交换物品,交换说明是您的必填项!';
            }
        }
        $params['CPUpinpai'] = (int)$params['CPUpinpai'];
        $params['CPUheshu'] = (int)$params['CPUheshu'];
        $params['neicun'] = (int)$params['neicun'];
        $params['yingpan'] = (int)$params['yingpan'];
        $params['pingmuchicun'] = (int)$params['pingmuchicun'];
        $params['xianka'] = (int)$params['xianka'];
        $params['smallclassid'] = (int)$params['smallclassid'];
        $params['smallclassidF'] = (int)$params['smallclassidF'];
        if($params['smallclassid'] == 0){
            $params['smallclassid'] = $params['smallclassidF'];
        }
        $params['FourthId'] = (int)$params['FourthId'];
        $params['rongji'] = (int)$params['rongji'];
        $params['rongjiF'] = (int)$params['rongjiF'];
        if($params['rongji'] == 0){
            $params['rongji'] = $params['rongjiF'];
        }
        unset($params['smallclassidF']);
        unset($params['rongjiF']);
        return $params;
    }
    /**
     * 二手信息求购回收格式化提交数据
     * @param string $id 职位id
     */
    public function formatRecoveryData(array $params)
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


        return $params;
    }
    /**
     * 发布间隔限制
     * @param string $title 文章名称
     */
    public function check_fabu_interval($title)
    {
        $result = (new IdleInfoTable())->getByTitle($title);
        return count($result);
    }
    /**
     * 发布间隔限制
     * @param string $title 文章名称
     */
    public function check_fabu_recovery_interval($title)
    {
        $result = (new SmsgTable())->getByTitle($title);
        return count($result);
    }
    /**
     * 获取二手信息完整度
     * @param array $data
     */
    public function get_info_num($data){
        $infoNum =80;
        if($data['HtmlArea']){
            $infoNum += 5;
        }
        if($data['Price']){
            $infoNum += 5;
        }
        if($data['HtmlExchange']){
            $infoNum += 5;
        }
        if($data['Pic'] && (strpos($data['Pic'], 'gif') !== false || strpos($data['Pic'], 'jpg') !== false)){
            $infoNum += 10;
        }
        return $infoNum;
    }
    /**
     * 获取二手信息完整度
     * @param array $data
     */
    public function get_info_recovery_num($data){
        $infoNum =80;
        if($data['oLinkMan']){
            $infoNum += 5;
        }
        if($data['oQQ']){
            $infoNum += 5;
        }
        if($data['pic'] && (strpos($data['pic'], 'gif') !== false || strpos($data['pic'], 'jpg') !== false)){
            $infoNum += 10;
        }
        return $infoNum;
    }
    /**
     * 获取分类数组
     * @param int $type
     */
    public function get_category_data($type){
        switch($type)
        {
            case 1:
                return "二手手机";
                break;
            case 2:
                return "虚拟物品";
                break;
            case 3:
                return "电脑数码";
                break;
            case 4:
                return "二手家电";
                break;
            case 6:
                return "二手家具";
                break;
            case 7:
                return "办公设备";
                break;
            case 8:
                return "其他二手";
                break;
            case 9:
                return "求购回收";
                break;
            case 10:
                return "家居日常";
                break;
            case 11:
                return "礼品收藏";
                break;
            case 12:
                return "服装鞋包";
                break;
            case 13:
                return "美容保健";
                break;
            case 14:
                return "母婴儿童";
                break;
            case 15:
                return "文体户外";
                break;
            case 16:
                return "图书音像";
                break;
            case 20:
                return "交通工具";
                break;
        }
    }
    /**
     * 获取分类id数组
     * reurn array
     */
    public function get_id_data(){
        return [1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,20];
    }
}