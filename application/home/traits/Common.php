<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/6/006
 * Time: 11:47
 */
namespace app\home\traits;

use app\home\model\PostPayTable;
use app\home\model\PostTjPriceTable;
use app\home\model\PostFabuPriceTable;
use app\home\model\CheckTelTable;
use app\home\model\PostSetupTable;
use app\home\model\Site3e21Table;
use app\home\model\ChkqqTable;
use app\home\model\PostChkTable;
use app\home\model\PostWhiteTable;
use app\home\model\PostBdMobileTable;

trait Common
{
    /**
     * 获取置顶推荐;
     * @param string $id 职位id;
     * @return array;
     */
    protected function recommend_adv(int $tjprice_cid)
    {
        $posttjpay = 0;//支付开关默认关闭
        $result = (new PostPayTable())->getByPrice();
        if(count($result) > 0){
            $posttjpay = $result['PostTjPay'];
        }
        if($posttjpay == 1 && $this->site_id){
            $arra_tjprice = (new PostTjPriceTable())->getRecommendList($tjprice_cid);
            if(count($arra_tjprice) == 0){
                $arra_tjprice = null;
                $posttjpay=0; //没有得到具体的推荐价格，则仍然认为推荐开关关闭
            }
            $data['arra_tjprice'] = $arra_tjprice;
        }
        $data['posttjpay'] = $posttjpay;
        return $data;
    }
    /**
     * 获取发布数量和是否付费
     * @param int $site_id;
     * @param int $sourceId;
     * @param int $tjprice_cid;
     * @param int $IsPostFufei;
     * @return array
     */
    protected function getSumAndPay(int $site_id, int $sourceId, int $tjprice_cid, int $IsPostFufei)
    {
        //获取配置
        $serverInfo = get_post_fabu_root($site_id, $sourceId, session('username'), getIP());
        if(!$serverInfo){
            return '配置读取失败!';
        }
        $todayNum = $serverInfo['todayNum'];//配置的今日发布数量
        $able_sumNum = $serverInfo['able_sumNum'];//剩余发布总量
        $able_todayNum = $serverInfo['able_todayNum'];//
        $data['pay_price'] = 0;
        if($able_sumNum > 0 && $able_todayNum > 0){
            if($able_sumNum < $able_todayNum){
                $able_todayNum = $able_sumNum;
            }
        }else{
            //支付费用
            $resule = (new PostFabuPriceTable())->getByChannel($tjprice_cid);
            if($resule){
                $data['pay_price'] = number_format($resule, 2);
            }
            $IsPostFufei = 1;
        }
        $data['IsPostFufei'] = $IsPostFufei;
        $data['able_sumNum'] = $able_sumNum;
        $data['todayNum'] = $todayNum;
        $data['able_todayNum'] = $able_todayNum;

        return $data;
    }
    /**
     * 电话是否禁发
     * @param int $tel 站点ID
     * @param string $username
     */
    protected function check_tel($tel, $username)
    {
        $tempTel = str_replace('undefined', '', $tel."");
        if(!$tempTel){
            $checkTel = false;
            return $checkTel;
        }
        $result = (new CheckTelTable())->getList($tel, $username);
        if(count($result) > 0){//电话禁发
            $checkTel = true;
        }else{
            $checkTel = false;
        }
        return $checkTel;
    }
    /**
     * qq是否禁发
     * @param string $qq qq号
     */
    protected function check_qq($qq)
    {
        $result = (new ChkqqTable())->getList($qq);
        if(count($result) > 0){//电话禁发
            $checkTel = true;
        }else{
            $checkTel = false;
        }
        return $checkTel;
    }
    /**
     * 获取联系电话
     * @param int $tabCls
     * @Tabcls:0：招聘 1：房产 2：二手 3：车辆 4：生活 7：交友 8：宠物
     */
    protected function getServiceComptel(int $tabCls)
    {
        $result = (new PostSetupTable())->getServicesTel($tabCls);
        if(count($result) > 0){
            if(!$result['ServicesTel'] || !$result['ServicesQQ']){
                $resChk = (new PostChkTable())->getServicesTel();
                if(count($resChk) > 0){
                    if(!$resChk['ServicesTel'] || !$resChk['ServicesQQ']){
                        $resSite = (new Site3e21Table())->getSiteTel();
                        if(count($resSite) > 0){
                            if(!$result['ServicesTel']){
                                if($resChk['ServicesTel']){
                                    $result['ServicesTel'] = $resChk['ServicesTel'];
                                }else{
                                    $result['ServicesTel'] = $resSite['tel'];
                                }
                            }
                            if(!$result['ServicesQQ']){
                                if($resChk['ServicesQQ']){
                                    $result['ServicesQQ'] = $resChk['ServicesQQ'];
                                }else{
                                    $result['ServicesQQ'] = $resSite['qq'];
                                }
                            }
                        }
                    }
                }
            }

        }
        return $result;
    }
    /**
     * 白名单检查
     * @param string $strChkUser 城市通用户名
     * @param int $channel 0招聘条数 1房产条数 2二手条数 3车辆条数 4生活条数 5商业条数 6培训条数 7交友条数 8宠物条数
     */
    protected function checkWhite(string $strChkUser, int $channel)
    {
        $checkWhite = false;
        $result = (new PostWhiteTable())->getWhite($strChkUser, $channel);
        if(count($result) > 0){
            $checkWhite = true;
        }
        return $checkWhite;
    }
    /**
     * 校验验证码
     * @param int $tel;
     * @param int $code;
     * @return bool
     */
    protected function checkMobileCode(int $tel, int $code)
    {
        $msg = false;
        $result = (new PostBdMobileTable())->checkTelCode($tel);
        if(count($result) == 0) return $msg;
        if($result['code'] == $code){
            $msg = $result['id'];
        }else{
            $msg = false;
        }
        return $msg;
    }

}