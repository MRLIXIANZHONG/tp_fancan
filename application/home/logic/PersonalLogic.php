<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2020/1/2/002
 * Time: 18:29
 */

namespace app\home\logic;
use app\home\model\PostQiYeKuTable;
use app\home\model\HomeZjTable;
use app\home\model\ErshouSjTable;
use app\home\model\CheliangTable;
use app\home\model\HomeSellTable;
use app\home\model\PostWhiteTable;

class PersonalLogic extends Logic
{
    /**
     * 我的发布列表
     */
    public function getReleaseList(array $params, int $is_login)
    {
        if($is_login != 1){
            return '';
        }
        $data['vo'] = $this->getListData($this->site_id, session('username'), 0, 1, 20);
        dump($data);
        $data['menu'] = $this->getMenuAuth();

        return $data;
    }
    /**
     * 个人中心--我的发布列表接口
     * @param int $siteId 站点ID
     * @param string $userName 用户名
     * @param int $theirType 类型
     * @param int $curPage 当前页码
     * @param int $pageSize 每页条数
     */
    function getListData($siteId, $userName, $theirType, $curPage, $pageSize)
    {
        $request['Param']= '"siteID":'.$siteId .',"userName":"'.$userName.'","theirType":"'.$theirType.'","curPage":"'.$curPage.'","pageSize":"'.$pageSize.'" ';
        $request['Method']="PHSocket_GetMyInfoList";
        $request['version']="5.6";
        $request['appName'] = "CcooCity";
        $request['ApiName'] = "appnewv5";
        $request['customerID'] = 8003;
        $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
        $info=GetAppServerApi($request);
        if($info['MessageList']['code'] == 1000){
            return $info['ServerInfo'];
        }
        return null;
    }
    /**
     * 左侧菜单权限
     */
    function getMenuAuth()
    {
        //是否企业
        $result = (new PostQiYeKuTable())->getByName(session('username'));
        $data['u_isrzorgs'] = 0;
        if(count($result) >0){
            $data['u_isrzorgs'] = 1;
        }
        //房产中介
        $result = (new HomeZjTable())->getIsZj();
        $data['u_iszj'] = 0;//中介
        $data['u_isagent'] = 0;//经纪人
        if(count($result) > 0){
            if(time() < strtotime($result['cmdtime'])){
                if(($result['iscmd'] == 0 && $result['ZjId'] > 0) or ($result['iscmd'] == 1 && $result['ZjId'] == 0)){
                    $data['u_iszj'] = 1;
                }
                if($result['AgentType'] == 1){
                    $data['u_isagent'] = 1;
                }

            }
        }
        //二手商家
        $result = (new ErshouSjTable())->getInfo(session('username'));
        $data['u_isessj'] = 0;
        if(count($result) > 0){
            $data['u_isessj'] = 1;
        }
        //车行
        $result = (new CheliangTable())->getInfo(session('username'));
        $data['u_isch'] = 0;
        if(count($result) > 0){
            $data['u_isch'] = 1;
        }
        //新楼盘
        $result = (new HomeSellTable())->getInfo(session('username'));
        $data['u_xlp'] = 0;
        if(count($result) > 0){
            $data['u_xlp'] = 1;
        }
        //生活频道
        $result = (new PostWhiteTable())->getInfo(session('username'), 4);
        $data['u_shenghuo'] = 0;
        if(count($result) > 0){
            $data['u_shenghuo'] = 1;
        }
        return $data;
    }
}