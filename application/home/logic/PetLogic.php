<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 13:55
 */

namespace app\home\logic;

use think\facade\Cache;
use app\home\model\PetInfoTable;
use app\home\model\PetKindTable;
use app\home\model\PostWhiteTable;
use app\home\model\PostSearchTable;
use app\home\model\StorageTable;
use app\home\validate\PetValidate;

class PetLogic extends Logic
{
    use \app\home\traits\Common;
    public $postsurl="/post/pet/";

    /**
     * 添加页面展示
     * @param
     * @return
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getHandleCreate(array $params, int $is_login)
    {
        $type_id = $this->getParam($params, 'type_id', 0, 'int');
        $type_id = ($type_id > 6) ? 6 : $type_id;
        $IsPostFufei = 0;
        $tjprice_cid = 9;//宠物
        $tabCls = 8;//宠物
        //dump($findData);exit;
        if($is_login == 1){
            //获取配置
            $data = $this->getSumAndPay($this->site_id, 14, $tjprice_cid, $IsPostFufei);
            if(!is_array($data)){
                return $data;
            }
            $IsPostFufei = $data['IsPostFufei'];
            unset($data['IsPostFufei']);
        }
        //获取宠物类别
        if(Cache::get($this->cachePrefix.'pet_create_findData'.$type_id)){
            $data['findData'] = Cache::get($this->cachePrefix.'pet_create_findData'.$type_id);
        }else{
            $data['findData'] = (new PetKindTable())->getByFid($type_id);
            Cache::set($this->cachePrefix.'pet_create_findData'.$type_id, $data['findData'], 600);
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

        if(Cache::get($this->cachePrefix.'pet_create_recom_adv')){
            $data['recom_adv'] = Cache::get($this->cachePrefix.'pet_create_recom_adv');
        }else{
            $data['recom_adv'] = $this->recommend_adv($tjprice_cid);
            Cache::set($this->cachePrefix.'pet_create_recom_adv', $data['recom_adv'], 600);
        }
        $data['IsPostFufei'] = $IsPostFufei;
        $data['type_id'] = $type_id;
        $data['tjprice_cid'] = $tjprice_cid;

        return $data;
    }

    /**
     * 处理提交数据
     * @access public
     */
    public function getHandleSave(array $params, int $is_login)
    {
        //格式化数据
        $data = $this->formatData($params);
        //dump($data);exit;
        if(!$data){
            return '格式化数据失败';
        }
        //数据校验
        $validate = new PetValidate();
        if(!$validate->checkParams('save',$data)){
            return $validate->getError();
        }
        //验证码校验
        if($data['telmsg'] && $data['telmsg'] != '888888'){
            if(!$this->checkMobileCode($data['tel'], $data['telmsg'])){
                return '验证码错误';
            }
        }
        unset($data['telmsg']);
        $tabCls = 8;
        $ccoochk = 1;//默认不审核，命中违禁词，专审，如果是认证会员，通过审核。宠物频道没有名企概念
        if($is_login == 1 && session('username')){
            //获取配置
            $serverInfo = get_post_fabu_root($this->site_id, 14, session('username'), getIP());
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
            return '请登录后发布信息!';
        }

        if($this->check_tel($data['tel'], session('username'))){
            if($tabCls >=0){
                $service_comptel = $this->getServiceComptel($tabCls);
            }
            return "您的账号被管理员禁发，没有发布信息的权限，如需发布请联系本站！联系电话：$service_comptel";
        }
        $title = $data['title'];
        $info = $data['info'];
        //禁发词库 转审词库
        $key = $title.$info;
        if($key){
            $result = get_check_key($key, $title, 1, $this->site_id, session('uid'), session('username'), 'pet_cat_save', 19);
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
        //敏感词替换
        $data['title'] = $title;
        $data['info'] = $info;
        //发布间隔限制
        $is_interval = $this->check_fabu_interval($title);
        if($is_interval > 0){
            return '您发布信息的速度过快 请休息一会!';
        }

        $tmpInfoNum = $this->get_info_num($data);

        if($tmpInfoNum > 100){
            $tmpInfoNum = 100;
        }

        $data['infoNum'] = $tmpInfoNum;
        $data['siteid'] = $this->site_id;
        $data['username'] = session('username');
        $data['ccoochk'] = $ccoochk;
        $data['ip'] = getIP();

        //付费
        if($isPostFufei == 1){
            $data['isdel'] = 1;
            $data['BuyRelease'] = 1;
        }

        $tempId = (new PetInfoTable())->insertGetId($data);
        //发布计数（1：全职招聘，2：兼职招聘，3房屋出售，4房屋出租，5房屋求购，6：房屋求租，7：店铺转让，8：二手，9：车辆，10：生活，13,：交友，14：宠物，15：拼车）
        if($isPostFufei == 0){
            /*$this->exePostSendCountU($this->site_id, 14);*/
            (new StorageTable())->exePostSendCountU(14);
        }
        if($tempId > 0){
            (new StorageTable())->exeAddLog($tabCls, $tempId, 'pet_info');
            $urlLinkInfo = $this->postsurl.$tempId.'x.html';
            $postClassName = "宠物世界";
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
     * 格式化提交数据
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
        }

        if(isset($params['ChannelId'])){
            $params['infokind'] = $params['ChannelId'];
            unset($params['ChannelId']);
        }
        $params['title'] = unescape($params['title']);
        $params['info'] = unescape($params['info']);
        if(isset($params['linkman'])){
            $params['linkman'] = unescape($params['linkman']);
        }
        if(isset($params['color'])){
            $params['color'] = unescape($params['color']);
        }
        if(isset($params['sex'])){
            $params['sex'] = unescape($params['sex']);
        }
        if(isset($params['dsDate'])){
            $params['dsDate'] = unescape($params['dsDate']);
        }
        if(isset($params['dsdidian'])){
            $params['dsdidian'] = unescape($params['dsdidian']);
        }
        if(isset($params['chouxie'])){
            $params['chouxie'] = unescape($params['chouxie']);
        }
        //$params['tel'] = (int)$params['tel'];
        $params['telmsg'] = (int)$params['telmsg'];


        return $params;
    }

    /**
     * 白名单检查
     * @param string $strChkUser 城市通用户名
     * @param string $channel 0招聘条数 1房产条数 2二手条数 3车辆条数 4生活条数 5商业条数 6培训条数 7交友条数 8宠物条数
     */
    public function checkWhite(string $strChkUser, int $channel)
    {
        $checkWhite = false;
        $result = (new PostWhiteTable())->getWhite($strChkUser, $channel);
        if(count($result) > 0){
            $checkWhite = true;
        }
        return $checkWhite;
    }
    /**
     * 发布间隔限制
     * @param string $title 文章名称
     */
    public function check_fabu_interval($title)
    {
        $result = (new PetInfoTable())->getByTitle($title);
        return count($result);
    }
    /**
     * 获取完整度
     * @param array $data 文章名称
     */
    public function get_info_num($data){
        $infoNum = 0;

        if($data['infokind'] > 0){
            $infoNum += 1;
        }
        if($data['source'] > 0){
            $infoNum += 1;
        }
        if(mb_strlen($data['title'],'utf8') > 0){
            $infoNum += 1;
        }

        if($data['price'] >=0){
            $infoNum += 1;
        }

        if(mb_strlen($data['info'],'utf8') > 0){
            $infoNum += 1;
        }

        if(mb_strlen($data['linkman'],'utf8') > 0){
            $infoNum += 1;
        }
        if(mb_strlen($data['tel'],'utf8') > 0){
            $infoNum += 1;
        }
        if(mb_strlen($data['qq'],'utf8') > 0){
            $infoNum += 1;
        }
        $data['allCountInfo'] = 10;
        switch($data['infokind'])
        {
            case 1:
                if($data['gongxu'] ==1){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==2){
                    $infoNum += 4;
                }elseif($data['gongxu'] ==3){
                    $infoNum += 6;
                }elseif($data['gongxu'] ==4){
                    $infoNum += 6;
                }

                if($data['hclass'] > 0 && $data['gongxu'] <= 2){
                    $infoNum += 1;
                }
                $data['infoNum'] = $infoNum;
                $data['allCountInfo'] = 16;
                ceil(($data['infoNum']/$data['allCountInfo'])*100);
                return ceil(($data['infoNum']/$data['allCountInfo'])*100);
                break;
            case 2:
                if($data['gongxu'] ==1){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==2){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==3){
                    $infoNum += 3;
                }elseif($data['gongxu'] ==4){
                    $infoNum += 3;
                }

                if($data['hclass'] > 0 && $data['gongxu'] <= 2){
                    $infoNum += 1;
                }
                $data['infoNum'] = $infoNum;
                $data['allCountInfo'] = 12;
                ceil(($data['infoNum']/$data['allCountInfo'])*100);
                return ceil(($data['infoNum']/$data['allCountInfo'])*100);
                break;
            case 3:
                if($data['gongxu'] ==1){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==2){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==3){
                    $infoNum += 3;
                }elseif($data['gongxu'] ==4){
                    $infoNum += 3;
                }

                if($data['hclass'] > 0 && $data['gongxu'] <= 2){
                    $infoNum += 1;
                }
                $data['infoNum'] = $infoNum;
                $data['allCountInfo'] = 12;
                ceil(($data['infoNum']/$data['allCountInfo'])*100);
                return ceil(($data['infoNum']/$data['allCountInfo'])*100);
                break;
            case 4:
                if($data['gongxu'] ==1){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==2){
                    $infoNum += 1;
                }elseif($data['gongxu'] ==3){
                    $infoNum += 3;
                }elseif($data['gongxu'] ==4){
                    $infoNum += 3;
                }

                if($data['hclass'] > 0 && $data['gongxu'] <= 2){
                    $infoNum += 1;
                }
                $data['infoNum'] = $infoNum;
                $data['allCountInfo'] = 12;
                ceil(($data['infoNum']/$data['allCountInfo'])*100);
                return ceil(($data['infoNum']/$data['allCountInfo'])*100);
                break;
            case 5:
                if($data['hclass'] > 0 && $data['gongxu'] <= 2){
                    $infoNum += 1;
                }
                $data['infoNum'] = $infoNum;
                $data['allCountInfo'] = 11;
                ceil(($data['infoNum']/$data['allCountInfo'])*100);
                return ceil(($data['infoNum']/$data['allCountInfo'])*100);
                break;
            default:
                $data['infoNum'] = $infoNum;
                return ceil(($data['infoNum']/$data['allCountInfo'])*100);
        }
    }
}