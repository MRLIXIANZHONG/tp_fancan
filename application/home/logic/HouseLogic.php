<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/5/005
 * Time: 16:00
 */

namespace app\home\logic;

use think\facade\Cache;
use app\home\model\HomeChuDuiTable;
use app\home\model\HomeQiuGouTable;
use app\home\model\HomeZjTable;
use app\home\model\HouseZoneTable;
use app\home\model\StorageTable;
use app\home\model\PostSetupTable;
use app\home\model\PostSearchTable;
use app\home\model\HomeZxTable;
use app\home\model\FwxqTable;
use app\home\model\HomeSheBeiTable;
use app\home\model\HomeChuShouTable;
use app\home\model\HomeChuZuTable;
use app\home\model\PostWhiteTable;
use app\home\model\HomeQiuZuTable;
use app\home\validate\HouseValidate;

class HouseLogic extends Logic
{
    use \app\home\traits\Common;

    public $postsurl="/post/fangwu/chudui/";
    public $houseSellUrl="/post/fangwu/chushou/";
    public $houseLeaseUrl="/post/fangwu/chuzu/";
    public $houseBuyUrl="/post/fangwu/qiugou/";
    public $houseBuyZuUrl="/post/fangwu/qiuzu/";

    /**
     * 房产交易 发布页面逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getCreate(array $params, int $is_login){
        $type_id = $this->getParam($params, 'type_id', 0, 'int');
        $type_id = ($type_id > 5) ? 5 : $type_id;
        if($type_id == 1){
            return $this->getHouseSellCreate($params, $is_login);
        }elseif($type_id == 2){
            return $this->getHouseLeaseCreate($params, $is_login);
        }elseif($type_id == 3 || $type_id == 4){
            return $this->getHouseBuyCreate($params, $is_login);
        }elseif($type_id == 5){
            return $this->getShopCreate($params, $is_login);
        }
    }
    /**
     * 房产交易 店铺转让 发布页面逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getShopCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');
        $type_id = ($type_id > 5) ? 5 : $type_id;
        $IsPostFufei = 0;
        $tjprice_cid = 2;//房产
        $strTempLink = 0;
        $is_open = 0;
        $tabCls = 1;//房产
        if($is_login == 1){
            $result = (new HomeZjTable())->getIsZj();
            if(count($result) > 0){
                $strTempLink = 1;
                if(time() < strtotime($result['cmdtime'])){
                    $is_open = 1;
                }
            }

        }
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 7, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
        }
        //获取地址数据
        if(Cache::get($this->cachePrefix.'house_shop_create_zone_data')){
            $data['zone_data'] = Cache::get($this->cachePrefix.'house_shop_create_zone_data');
        }else{
            $data['zone_data'] = (new HouseZoneTable())->getHouseZone();
            Cache::set($this->cachePrefix.'house_shop_create_zone_data', $data['zone_data'], 600);
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
        $data['strTempLink'] = $strTempLink;
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['is_login'] = $is_login;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['is_open'] = $is_open;
        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'house_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'house_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'house_create_recom_adv', $data['recom_adv'], 600);
        }
        return $data;
    }
    /**
     * 房产交易 店铺转让 处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getShopSave(array $params, int $is_login)
    {
        $tabCls = 1;
        //格式化数据
        $data = $this->formatData($params);
        if(!$data){
            return '格式化数据失败';
        }
        //数据校验
        $validate = new HouseValidate();
        if(!$validate->checkParams('shop_save',$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg']){
            if(!$this->checkMobileCode($data['Tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, 7, session('username'), getIP());
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
            return 2;//请登录后
        }
        //是否禁发
        if($this->check_tel($data['Tel'], session('username'))){
            return 1;
        }
        //是否是中介
        $result = (new HomeZjTable())->getIsZj();
        $is_zj = false;
        $ZjId = 0;
        if(count($result) > 0){
            $data['linkman'] = $result['oLinkP'];
            $data['Tel'] = $result['oTel'];
            $data['Email'] = $result['oEmail'];
            $data['qq'] = $result['oQQ'];
            $data['IsZj'] = 1;
            $ZjId = $result['ZjId'];
            if($ZjId == 0){
                $ZjId = $result['id'];
            }
            $data['ZjId'] = $ZjId;
            $is_zj = true;
            $upId = $result['id'];
        }

        $title = $data['oTitle'];
        $info = $data['oInfo'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'house_shop_save', 19);
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
        $is_interval = $this->check_fabu_interval((int)$data['type_id'], $title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }
        unset($data['type_id']);
        //完整度
        $tmpInfoNum = $this->get_info_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }
        $data['infoNum'] = $tmpInfoNum;
        $data['Siteid'] = $this->site_id;
        $data['UserName'] = session('username');
        $data['IsChk'] = $ccoochk;
        $data['Ip'] = getIP();

        //付费
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }
        $tempId = (new HomeChuDuiTable())->insertGetId($data);
        //中介就更新发布条数
        if($is_zj && $tempId){
            $count = (new HomeChuDuiTable())->getListCount(1, 0, $ZjId, 1);
            $upData['id'] = $upId;
            $upData['cdcount'] = $count;
            (new HomeZjTable())->update($upData);
        }
        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            (new StorageTable())->exePostSendCountU(7);
        }

        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'Home_Chudui');
            $urlLinkInfo = $this->postsurl.$tempId.'x.html';
            $postClassName = "店铺转让";
            $logData['SiteId'] = $this->site_id;
            $logData['title'] = $title;
            $logData['classname'] = $postClassName;
            $logData['url'] = $urlLinkInfo;
            $logData['tel'] = $data['Tel'];
            $logData['ip'] = getIP();
            (new PostSearchTable())->insert($logData);
            unset($data);
            unset($logData);
            return $tempId;
        }

    }
    /**
     * 房产交易 房屋出售 发布页面逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHouseSellCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');
        $type_id = ($type_id > 5) ? 5 : $type_id;
        $IsPostFufei = 0;
        $tjprice_cid = 2;//房产
        $strTempLink = 0;
        $is_open = 0;
        $tabCls = 1;//房产
        if($is_login == 1){
            //是否开通过中介
            $result = (new HomeZjTable())->getIsZj();
            if(count($result) > 0){
                $strTempLink = 1;
                if(time() < strtotime($result['cmdtime'])){
                    $is_open = 1;
                }
            }

        }
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 3, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
        }
        //是否显示小区
        $isShowXq = true;
        $count = (new FwxqTable())->getKindVillage();
        if($count == 0){
            $isShowXq = false;
        }
        $data['isShowXq'] = $isShowXq;
        //获取建筑年份
        $data['home_year'] = get_home_year();
        //获取装修要求
        if(Cache::get($this->cachePrefix.'house_create_home_zx')){
            $data['home_zx'] = Cache::get($this->cachePrefix.'house_create_home_zx');
        }else{
            $data['home_zx'] = (new HomeZxTable())->getHouseZx();
            Cache::set($this->cachePrefix.'house_create_home_zx', $data['home_zx'], 600);
        }
        //获取所在区域
        if(Cache::get($this->cachePrefix.'house_create_zone_data')){
            $data['zone_data'] = Cache::get($this->cachePrefix.'house_create_zone_data');
        }else{
            $data['zone_data'] = (new HouseZoneTable())->getHouseZone();
            Cache::set($this->cachePrefix.'house_create_zone_data', $data['zone_data'], 600);
        }
        //获取房屋特色
        if(Cache::get($this->cachePrefix.'house_sell_create_house_only')){
            $data['house_only'] = Cache::get($this->cachePrefix.'house_sell_create_house_only');
        }else{
            $data['house_only'] = (new HomeSheBeiTable())->getHouseOnly(1);
            Cache::set($this->cachePrefix.'house_sell_create_house_only', $data['house_only'], 600);
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
        $data['strTempLink'] = $strTempLink;
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['is_login'] = $is_login;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['is_open'] = $is_open;
        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'house_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'house_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'house_create_recom_adv', $data['recom_adv'], 600);
        }
        return $data;
    }
    /**
     * 房产交易 房屋出售 处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHouseSellSave(array $params, int $is_login)
    {
        $tabCls = 1;
        //格式化数据
        $data = $this->formatSellData($params);
        if(!$data){
            return '格式化数据失败';
        }
        //数据校验
        $validate = new HouseValidate();
        if(!$validate->checkParams('sell_save',$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg']){
            if(!$this->checkMobileCode($data['Tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, 3, session('username'), getIP());
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
            return 2;//请登录后
        }
        //是否禁发
        if($this->check_tel($data['Tel'], session('username'))){
            return 3;
        }
        //是否是中介
        $result = (new HomeZjTable())->getIsZj();
        $is_zj = false;
        $ZjId = 0;
        if(count($result) > 0){
            $data['linkman'] = $result['oLinkP'];
            $data['Tel'] = $result['oTel'];
            $data['Email'] = $result['oEmail'];
            $data['qq'] = $result['oQQ'];
            $data['IsZj'] = 1;
            $ZjId = $result['ZjId'];
            if($ZjId == 0){
                $ZjId = $result['id'];
            }
            $data['ZjId'] = $ZjId;
            $is_zj = true;
            $upId = $result['id'];
        }

        $title = $data['oTitle'];
        $info = $data['oInfo'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'house_sell_save', 19);
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
        $is_interval = $this->check_fabu_interval((int)$data['type_id'], $title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }
        unset($data['type_id']);
        //完整度
        $tmpInfoNum = $this->get_sell_info_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }
        //判断小区id
        if($data['xqId'] == 0 && $data['areazone']){
            $data['xqId'] = (new FwxqTable())->getVillageId($data['areazone']);
        }
        $data['infoNum'] = $tmpInfoNum;
        $data['Siteid'] = $this->site_id;
        $data['UserName'] = session('username');
        $data['IsChk'] = $ccoochk;
        $data['Ip'] = getIP();
        $data['xqId'] = (int)$data['xqId'];
        //付费
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }
        $tempId = (new HomeChuShouTable())->insertGetId($data);
        //中介就更新发布条数
        if($is_zj && $tempId){
            $count = (new HomeChuShouTable())->getListCount(1, 0, $ZjId, 1);
            $upData['id'] = $upId;
            $upData['cscount'] = $count;
            (new HomeZjTable())->update($upData);
        }
        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            (new StorageTable())->exePostSendCountU(3);
        }
        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'Home_ChuShou');
            //出售统计
            if($data['xqId'] != 0){
                (new FwxqTable())->setInc($data['xqId'], 'cscount');
            }
            $urlLinkInfo = $this->houseSellUrl.$tempId.'x.html';
            $postClassName = "房屋出售";
            $logData['SiteId'] = $this->site_id;
            $logData['title'] = $title;
            $logData['classname'] = $postClassName;
            $logData['url'] = $urlLinkInfo;
            $logData['tel'] = $data['Tel'];
            $logData['ip'] = getIP();
            (new PostSearchTable())->insert($logData);
            unset($data);
            unset($logData);
            return $tempId;
        }

    }
    /**
     * 房产交易 房屋出租 发布页面逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHouseLeaseCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');
        $type_id = ($type_id > 5) ? 5 : $type_id;
        $IsPostFufei = 0;
        $tjprice_cid = 2;//房产
        $strTempLink = 0;
        $is_open = 0;
        $tabCls = 1;//房产
        if($is_login == 1){
            //是否开通过中介
            $result = (new HomeZjTable())->getIsZj();
            if(count($result) > 0){
                $strTempLink = 1;
                if(time() < strtotime($result['cmdtime'])){
                    $is_open = 1;
                }
            }

        }
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 4, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
        }
        //是否显示小区
        $isShowXq = true;
        $count = (new FwxqTable())->getKindVillage();
        if($count == 0){
            $isShowXq = false;
        }
        $data['isShowXq'] = $isShowXq;

        //获取装修要求
        if(Cache::get($this->cachePrefix.'house_create_home_zx')){
            $data['home_zx'] = Cache::get($this->cachePrefix.'house_create_home_zx');
        }else{
            $data['home_zx'] = (new HomeZxTable())->getHouseZx();
            Cache::set($this->cachePrefix.'house_create_home_zx', $data['home_zx'], 600);
        }
        //获取所在区域
        if(Cache::get($this->cachePrefix.'house_create_zone_data')){
            $data['zone_data'] = Cache::get($this->cachePrefix.'house_create_zone_data');
        }else{
            $data['zone_data'] = (new HouseZoneTable())->getHouseZone();
            Cache::set($this->cachePrefix.'house_create_zone_data', $data['zone_data'], 600);
        }
        //获取配套设施
        if(Cache::get($this->cachePrefix.'house_create_house_only')){
            $data['house_only'] = Cache::get($this->cachePrefix.'house_create_house_only');
        }else{
            $data['house_only'] = (new HomeSheBeiTable())->getHouseOnly(0);
            Cache::set($this->cachePrefix.'house_create_house_only', $data['house_only'], 600);
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
        $data['strTempLink'] = $strTempLink;
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['is_login'] = $is_login;
        $data['tjprice_cid'] = $tjprice_cid;
        $data['is_open'] = $is_open;
        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'house_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'house_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'house_create_recom_adv', $data['recom_adv'], 600);
        }
        return $data;
    }
    /**
     * 房产交易 房屋出租 处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHouseLeaseSave(array $params, int $is_login)
    {
        $tabCls = 1;
        //格式化数据
        $data = $this->formatSellData($params);
        if(!$data){
            return '格式化数据失败';
        }
        //数据校验
        $validate = new HouseValidate();
        if(!$validate->checkParams('lease_save',$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg']){
            if(!$this->checkMobileCode($data['Tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, 4, session('username'), getIP());
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
            return 2;//请登录后
        }
        //是否禁发
        if($this->check_tel($data['Tel'], session('username'))){
            return 3;
        }
        //是否是中介
        $result = (new HomeZjTable())->getIsZj();
        $is_zj = false;
        $ZjId = 0;
        if(count($result) > 0){
            $data['linkman'] = $result['oLinkP'];
            $data['Tel'] = $result['oTel'];
            $data['Email'] = $result['oEmail'];
            $data['qq'] = $result['oQQ'];
            $data['IsZj'] = 1;
            $ZjId = $result['ZjId'];
            if($ZjId == 0){
                $ZjId = $result['id'];
            }
            $data['ZjId'] = $ZjId;
            $is_zj = true;
            $upId = $result['id'];
        }

        $title = $data['oTitle'];
        $info = $data['oInfo'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'house_lease_save', 19);
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
        $is_interval = $this->check_fabu_interval((int)$data['type_id'], $title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }
        unset($data['type_id']);
        //完整度
        $tmpInfoNum = $this->get_sell_info_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }
        //判断小区id
        if($data['xqId'] == 0 && $data['areazone']){
            $data['xqId'] = (new FwxqTable())->getVillageId($data['areazone']);
        }
        $data['Htype'] = get_h_type(0, $data['HXshi'], $data['HXting']);
        $data['infoNum'] = $tmpInfoNum;
        $data['Siteid'] = $this->site_id;
        $data['UserName'] = session('username');
        $data['IsChk'] = $ccoochk;
        $data['Ip'] = getIP();
        $data['xqId'] = (int)$data['xqId'];
        //付费
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }
        //dump($data);exit;
        $tempId = (new HomeChuZuTable())->insertGetId($data);
        //中介就更新发布条数
        if($is_zj && $tempId){
            $count = (new HomeChuZuTable())->getListCount(1, 0, $ZjId, 1);
            $upData['id'] = $upId;
            $upData['czcount'] = $count;
            (new HomeZjTable())->update($upData);
        }
        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            (new StorageTable())->exePostSendCountU(4);
        }
        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'Home_ChuZu');
            //出售统计
            if($data['xqId'] != 0){
                (new FwxqTable())->setInc($data['xqId'], 'czcount');
            }
            $urlLinkInfo = $this->houseLeaseUrl.$tempId.'x.html';
            $postClassName = "房屋出租";
            $logData['SiteId'] = $this->site_id;
            $logData['title'] = $title;
            $logData['classname'] = $postClassName;
            $logData['url'] = $urlLinkInfo;
            $logData['tel'] = $data['Tel'];
            $logData['ip'] = getIP();
            (new PostSearchTable())->insert($logData);
            unset($data);
            unset($logData);
            return $tempId;
        }

    }
    /**
     * 房产交易 房屋求购 发布页面逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHouseBuyCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');
        $type_id = ($type_id > 5) ? 5 : $type_id;
        $IsPostFufei = 0;
        $tjprice_cid = 2;//房产
        $strTempLink = 0;
        $tabCls = 1;//房产

        //获取期望价格
        $data['home_money'] = get_home_money();
        //获取装修要求
        if(Cache::get($this->cachePrefix.'house_create_home_zx')){
            $data['home_zx'] = Cache::get($this->cachePrefix.'house_create_home_zx');
        }else{
            $data['home_zx'] = (new HomeZxTable())->getHouseZx();
            Cache::set($this->cachePrefix.'house_create_home_zx', $data['home_zx'], 600);
        }
        //获取所在区域
        if(Cache::get($this->cachePrefix.'house_create_zone_data')){
            $data['zone_data'] = Cache::get($this->cachePrefix.'house_create_zone_data');
        }else{
            $data['zone_data'] = (new HouseZoneTable())->getHouseZone();
            Cache::set($this->cachePrefix.'house_create_zone_data', $data['zone_data'], 600);
        }
        //获取配套设施
        if($type_id == 4){
            if(Cache::get($this->cachePrefix.'house_create_house_only')){
                $data['house_only'] = Cache::get($this->cachePrefix.'house_create_house_only');
            }else{
                $data['house_only'] = (new HomeSheBeiTable())->getHouseOnly(0);
                Cache::set($this->cachePrefix.'house_create_house_only', $data['house_only'], 600);
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
        $data['strTempLink'] = $strTempLink;
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['is_login'] = $is_login;
        $data['tjprice_cid'] = $tjprice_cid;
        //置顶推荐数据
        if(Cache::get($this->cachePrefix.'house_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'house_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'house_create_recom_adv', $data['recom_adv'], 600);
        }
        return $data;
    }
    /**
     * 房产交易 房屋求购 处理提交逻辑
     * @param array $params
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHouseBuySave(array $params, int $is_login)
    {
        $tabCls = 1;
        $classID = 5;//求购
        //格式化数据
        $data = $this->formatBuyData($params);
        if(!$data){
            return '格式化数据失败';
        }
        $type_id = $data['type_id'];
        if($type_id == 4){
            $classID = 6;//求租
        }
        //数据校验
        $validate = new HouseValidate();
        if(!$validate->checkParams('buy_save',$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg']){
            if(!$this->checkMobileCode($data['Tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, $classID, session('username'), getIP());
            if(!$serverInfo){
                return '配置读取失败!';
            }
            $able_sumNum = $serverInfo['able_sumNum'];
            $able_todayNum = $serverInfo['able_todayNum'];
            //检查白名单和每天发布的数量
            $whiteData = (new PostWhiteTable())->getWhite(session('username'), $tabCls);
            if(count($whiteData) > 0){
                $able_todayNum = $whiteData['oNum'];
            }
            //获取当前账号每天发布的数量
            if($type_id == 3){
                $result = (new HomeQiuGouTable())->getListCount();
            }elseif($type_id == 4){
                $result = (new HomeQiuZuTable())->getListCount();
            }


            $uCountNum = $result[0]['num'];
            if($able_todayNum <= 0){
                return '本站暂时关闭免费发布信息功能，如有需求请联系本站!';
            }elseif($uCountNum >= $able_todayNum){
                return "你今天的发贴数量已超过了{$able_todayNum}条,如需帮助请联系本站!";
            }

        }else{
            return 2;//请登录后
        }
        //是否禁发
        if($this->check_tel($data['Tel'], session('username'))){
            return 1;
        }

        $title = $data['oTitle'];
        $info = $data['oInfo'];
        //禁发词库 转审词库 认证用户
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'house_buy_save', 19);
            if($result['code'] == 1001){
                return '您发布的信息可能涉及非法信息,请重新发布!';
            }elseif($result['code'] == 1002){//转审
                // 检查白名单 认证会员不审核
                if(count($whiteData) == 0){
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
        $is_interval = $this->check_fabu_interval((int)$type_id, $title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }
        unset($data['type_id']);
        //完整度
        $tmpInfoNum = $this->get_buy_info_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }

        $data['Htype'] = get_h_type(0, $data['HXshi'], $data['HXting']);
        $data['infoNum'] = $tmpInfoNum;
        $data['Siteid'] = $this->site_id;
        $data['UserName'] = session('username');
        $data['IsChk'] = $ccoochk;
        $data['Ip'] = getIP();
        //dump($data);exit;
        $tabName = 'Home_QiuGou';
        if($type_id == 3){
            $tempId = (new HomeQiuGouTable())->insertGetId($data);
        }elseif($type_id == 4){
            $tabName = 'Home_QiuZu';
            $tempId = (new HomeQiuZuTable())->insertGetId($data);
        }


        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, $tabName);

            $urlLinkInfo = $this->houseBuyUrl.$tempId.'x.html';
            if($type_id == 4){
                $urlLinkInfo = $this->houseBuyZuUrl.$tempId.'x.html';
            }
            $postClassName = "房屋求购";
            if($type_id == 4){
                $postClassName = "房屋求租";
            }
            $logData['SiteId'] = $this->site_id;
            $logData['title'] = $title;
            $logData['classname'] = $postClassName;
            $logData['url'] = $urlLinkInfo;
            $logData['tel'] = $data['Tel'];
            $logData['ip'] = getIP();
            (new PostSearchTable())->insert($logData);
            unset($data);
            unset($logData);
            return $tempId;
        }

    }
    /**
     * 店铺转让格式化提交数据
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
            $params['Pic'] = rtrim(unescape($params['upLoadList']),'|');
            unset($params['upLoadList']);
        }else{
            $params['Pic'] = '';
            unset($params['upLoadList']);
        }

        if($params['nid']){
            $params['oMap'] = unescape($params['nid']);
            unset($params['nid']);
        }else{
            $params['oMap'] = '';
            unset($params['nid']);
        }

        $params['oTitle'] = unescape($params['oTitle']);
        $params['address'] = unescape($params['address']);
        $params['oInfo'] = unescape($params['oInfo']);
        $params['SellingPoint'] = unescape($params['SellingPoint']);
        $params['ServiceInfo'] = unescape($params['ServiceInfo']);
        $params['linkman'] = unescape($params['linkman']);
        //$params['Tel'] = (int)$params['Tel'];
        $params['moneynumzu'] = $params['MoneyNumzu'];
        $params['IsCmd'] = (int)$params['iscmd'];

        unset($params['MoneyNumzu']);
        unset($params['iscmd']);
        return $params;
    }
    /**
     * 房屋出售格式化提交数据
     * @param string $id 职位id
     */
    public function formatSellData(array $params)
    {
        if(count($params) == 0) return false;
        foreach ($params as $key => &$val) {
            if($val == 'undefined'){
                $val = '';
            }
        }
        if($params['upLoadList']){
            $params['Pic'] = rtrim(unescape($params['upLoadList']),'|');
            unset($params['upLoadList']);
        }else{
            $params['Pic'] = '';
            unset($params['upLoadList']);
        }

        if($params['nid']){
            $params['oMap'] = unescape($params['nid']);
            unset($params['nid']);
        }else{
            $params['oMap'] = '';
            unset($params['nid']);
        }

        if($params['LouNum'] > $params['IsLouNum']){
            $params['LouNum'] = $params['IsLouNum'];
        }

        $params['oTitle'] = unescape($params['oTitle']);
        $params['address'] = unescape($params['address']);
        $params['areazone'] = unescape($params['areazone']);
        $params['oInfo'] = unescape($params['oInfo']);

        $params['direction'] = unescape($params['direction']);
        $params['wupin'] = str_replace(',', '|', unescape($params['wupin']));
        $params['linkman'] = unescape($params['linkman']);
        $params['areaId'] = (int)$params['areaId'];
        $params['xqId'] = (int)$params['xqId'];
        //$params['Tel'] = (int)$params['Tel'];

        $params['HXshi'] = (int)$params['HXshi'];
        $params['HXting'] = (int)$params['HXting'];
        $params['HXwei'] = (int)$params['HXwei'];
        $params['LouNum'] = (int)$params['LouNum'];
        $params['IsLouNum'] = (int)$params['IsLouNum'];
        $params['zhuangxiu'] = (int)$params['zhuangxiu'];
        $params['zhuangxiu'] = (int)$params['zhuangxiu'];
        $params['ServiceInfo'] = unescape($params['ServiceInfo']);

        if(isset($params['Isdaikuan'])){
            $params['Isdaikuan'] = (int)unescape($params['Isdaikuan']);
        }
        if(isset($params['PropertyRight'])){
            $params['PropertyRight'] = (int)$params['PropertyRight'];
        }
        if(isset($params['IsElevator'])){
            $params['IsElevator'] = (int)$params['IsElevator'];
        }
        if(isset($params['SellingPoint'])){
            $params['SellingPoint'] = unescape($params['SellingPoint']);
        }
        if(isset($params['SaleMentality'])){
            $params['SaleMentality'] = unescape($params['SaleMentality']);
        }
        if($params['type_id'] == 1){
            unset($params['MoneyType']);
        }

        return $params;
    }
    /**
     * 房屋出售格式化提交数据
     * @param string $id 职位id
     */
    public function formatBuyData(array $params)
    {
        if(count($params) == 0) return false;
        foreach ($params as $key => &$val) {
            if($val == 'undefined'){
                $val = '';
            }
        }


        $params['oTitle'] = unescape($params['oTitle']);
        $params['address'] = unescape($params['address']);
        $params['areaId'] = (int)$params['areaId'];
        $params['HXshi'] = (int)$params['HXshi'];
        $params['HXting'] = (int)$params['HXting'];
        $params['HXwei'] = (int)$params['HXwei'];
        $params['zhuangxiu'] = (int)$params['zhuangxiu'];
        $params['wupin'] = str_replace(',', '|', unescape($params['wupin']));
        $params['oInfo'] = unescape($params['oInfo']);
        $params['linkman'] = unescape($params['linkman']);
        //$params['Tel'] = (int)$params['Tel'];

        return $params;
    }
    /**
     * 获取店铺转让完整度
     * @param array $data 文章名称
     */
    public function get_info_num($data){
        $infoNum = 50;
        if($data['areaId']){
            $infoNum += 5;
        }
        if(isset($data['areazone'])){
            if($data['areazone']){
                $infoNum += 5;
            }
        }
        if($data['Mianji']){
            $infoNum += 5;
        }
        if($data['MoneyNum']){
            $infoNum += 5;
        }
        if($data['moneynumzu']){
            $infoNum += 5;
        }
        if(isset($data['qq'])){
            if($data['qq']){
                $infoNum += 5;
            }
        }
        if(isset($data['oMap'])){
            if($data['oMap']){
                $infoNum += 5;
            }
        }
        if(isset($data['Pic'])){
            if($data['Pic']){
                $infoNum += 5;
            }
        }
        return $infoNum;
    }
    /**
     * 获取房屋出售完整度
     * @param array $data 文章名称
     */
    public function get_sell_info_num($data){
        $infoNum = 50;
        if($data['areaId']){
            $infoNum += 5;
        }
        if(isset($data['areazone'])){
            if($data['areazone']){
                $infoNum += 5;
            }
        }
        if($data['Mianji']){
            $infoNum += 5;
        }
        if($data['MoneyNum']){
            $infoNum += 5;
        }
        if($data['LouNum']){
            $infoNum += 5;
        }
        if($data['IsLouNum']){
            $infoNum += 5;
        }
        if($data['direction']){
            $infoNum += 5;
        }
        if(isset($data['wupin'])){
            if($data['wupin']){
                $infoNum += 5;
            }
        }
        if(isset($data['oMap'])){
            if($data['oMap']){
                $infoNum += 5;
            }
        }
        if(isset($data['Pic'])){
            if($data['Pic']){
                $infoNum += 5;
            }
        }
        return $infoNum;
    }
    /**
     * 获取房屋出售完整度
     * @param array $data
     */
    public function get_buy_info_num($data){
        $infoNum = 75;
        if($data['Mianji']){
            $infoNum += 5;
        }
        if($data['wupin']){
            $infoNum += 5;
        }
        if($data['linkman']){
            $infoNum += 5;
        }
        if($data['qq']){
            $infoNum += 5;
        }
        if($data['Email']){
            $infoNum += 5;
        }

        return $infoNum;
    }
    /**
     * 发布间隔限制
     * @param int $type 类型id
     * @param string $title 文章名称
     */
    public function check_fabu_interval(int $type,string $title)
    {
        if($type == 1){
            $result = (new HomeChuShouTable())->getByTitle($title);
        }elseif($type == 2){
            $result = (new HomeChuZuTable())->getByTitle($title);
        }elseif($type == 3){
            $result = (new HomeQiuGouTable())->getByTitle($title);
        }elseif($type == 4){
            $result = (new HomeQiuZuTable())->getByTitle($title);
        }elseif($type == 5){
            $result = (new HomeChuDuiTable())->getByTitle($title);
        }

        return count($result);
    }

}