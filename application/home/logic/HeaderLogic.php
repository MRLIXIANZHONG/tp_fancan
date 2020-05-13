<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/15/015
 * Time: 10:31
 */

namespace app\home\logic;

use app\home\model\ZphTable;
use think\facade\Config;
use app\home\model\SiteTopUrlTable;
use app\home\model\StorageTable;

class HeaderLogic extends Logic
{
    use \app\home\traits\Common;

    /**
     * 获取用户未读消息
     * @param $siteId 站点ID;
     * @return array
     */
    public function getNoneMsg($userName, $siteId, $strcate)
    {
        if($this->redisCluster->get($this->cachePrefix.'userNoRead_userName_'.$userName.'siteId_'.$siteId) != null){
            $num = $this->redisCluster->get($this->cachePrefix.'userNoRead_userName_'.$userName.'siteId_'.$siteId);
        }else{
            $result = (new StorageTable())->exeUserNoRead($userName, $siteId, $strcate);
            $num = 0;
            if(count($result) >0){
                foreach ($result as  $val ) {
                    $num = $val;
                }
            }
            $this->redisCluster->setex($this->cachePrefix.'userNoRead_userName_'.$userName.'siteId_'.$siteId, 180, $num);
        }

        return $num;
    }
    /**
     * 获取站点信息
     */
    public function getSiteInfo($doMain)
    {
        $result = $this->getCacheSiteData($doMain);
        return $result;
    }
    /**
     * 获取顶部数据
     */
    public function getHeaderData($siteId)
    {
        if($this->redisCluster->get($this->cachePrefix.'headerData_'.$siteId) != null){
            $data = $this->redisCluster->get($this->cachePrefix.'headerData_'.$siteId);
            $data = json_decode($data, true);
        }else{
            if(!$siteId){
                LogRecord('未获取到站点信息', request()->url(true), 'post/job/'.$siteId.'_'.cookie('site_id'), 'PC移植PHP');
            }
            $data['top_info'] = (new SiteTopUrlTable())->getByFid($siteId,0, 0, '=');
            if (!$data['top_info']){
                $data['top_info'] = (new SiteTopUrlTable())->getByFid(1, 0, 0, '=');
                $data['top_info2'] = (new SiteTopUrlTable())->getByFid(1, 0, 0, '>');
            }else{
                $data['top_info2'] = (new SiteTopUrlTable())->getByFid($siteId, 0, 0, '>');
            }
            $this->redisCluster->setex($this->cachePrefix.'headerData_'.$siteId, 900, json_encode($data));
        }
        return $data;
    }
    /**
     * 获取缓存中的siteId相关信息
     * @param string $url
     * @return array
     */
    public function getCacheSiteData($url)
    {
        //$url = strRepeat(['m.','.ccoo.cn'], '', str_replace('www','m', $url));
        $url = str_replace('www','m', $url);
        $result = Config::get('webConfig.');
        if(count($result) > 0){
            if(isset($result[$url])){
                $data['siteid']    = $result[$url]['siteID'];
                $data['areatitle'] = $result[$url]['areaName'];
                $data['site_name'] = $result[$url]['wapSiteName'];
                return $data;
            }
        }
        return $result;
    }
    
    //获取是否开启招聘会
    public function getZphNum($siteId)
    {
        if($this->redisCluster->get($this->cachePrefix.'zphNum_'.$siteId) != null){
            $data = $this->redisCluster->get($this->cachePrefix.'zphNum_'.$siteId);
        }else{
            $request['version'] = "4.6";
            $request['Param'] = '"siteId":'.$siteId;
            $request['Method'] = "PHSocket_GetPCJobZHPCountData";//需传
            $request['customerID']=8004;
            $request['ApiName'] = "zhaopinapiphp";//"CcooCityIOS";
            $request['sign'] = '32asu88aseLfv+k0siQ+o7WjDbcdewhal7c1zsgWebA=';
            $arr = GetAppServerApi($request);
            if ($arr){
                $data=$arr['ServerInfo'];
            }else{
                $data=0;
            }
            $this->redisCluster->setex($this->cachePrefix.'zphNum_'.$siteId, 180, $data);
        }
        return $data;
    }
}