<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/15/015
 * Time: 10:31
 */

namespace app\home\logic;

use app\home\model\Dept3e21Table;
use app\home\model\Site3e21Table;
use app\home\model\Area3e21Table;

class FooterLogic extends Logic
{
    use \app\home\traits\Common;


    /**
     * 获取底部数据
     * @return array
     */
    public function getFooterData($siteId)
    {
        if($this->redisCluster->get($this->cachePrefix.'footerData_'.$siteId) != null){
            $data = $this->redisCluster->get($this->cachePrefix.'footerData_'.$siteId);
            $data = json_decode($data, true);
        }else{
            if(!$siteId){
                return 'siteId错误';
            }
            $result = (new Site3e21Table())->getSiteName($siteId);
            if(count($result) >0){
                $data['dept_id'] = $result['companyno'];
                if($result['isview'] == 0){
                    $data['dept_id'] = 1;
                }
                if($result['isjf'] == 0){
                    $data['dept_id'] = 1;
                }
                $data['siteName'] = $result['sitename'];
                $data['siteUrl'] = $result['weburl'];
                $data['baidu'] = $result['web_dir'].'ccoocn';
                $data['sdir'] = 'newccoo';
                if($result['isjf'] == 1 || $siteId == 2520){
                    $data['sdir'] = $result['web_dir'];
                }
                $data['compjf'] = $result['isjf'];
                if($result['isjf'] == 0 && (strtotime($result['viewtime']) > time())){
                    $data['compjf'] == 1;
                }
                $data['areaName'] = $result['areatitle'];
                $area3id = $result['areano'];
                $data['areaID'] = $area3id;
                $data['siteWebType'] = $result['web_type'];
                $data['siteID'] = $siteId;
            }
            $result = (new Area3e21Table())->getByAreaId($area3id);
            if(count($result) >0){
                $area2id = $result['lishu'];
                $data['areaParentID'] = $area2id;
                $result = (new Area3e21Table())->getByAreaId($area2id);
                if(count($result) >0){
                    $area2=trim($result["area"]);
                    $data['areaParentName'] = $area2;
                    $area1id=$result["lishu"];
                    $data['areaPParentID'] = $area1id;
                    $result = (new Area3e21Table())->getByAreaId($area1id);
                    if(count($result) >0){
                        $area1=trim($result["area"]);
                        $data['areaPParentName'] = $area1;
                    }
                }
            }

            $result = (new Dept3e21Table())->getByDeptId($data['dept_id']);
            if(count($result) > 0){
                $data['compName'] = $result['dept_name'];
                $data['compUrl'] = $result['wwwurl'];
                $data['compTel'] = $result['tell'];
                $data['compTel'] = $result['tell'];
                $data['compAddress'] = $result['address'];
                $data['compFax'] = $result['fax'];
                $data['compEmail'] = str_replace('@', '#', $result['email']);
                $data['compPost'] = $result['post'];
                $data['compQQ'] = $result['qq'];
                $data['compPost'] = $result['post'];
                $data['compba'] = $result['ba'];
                $data['compfalv'] = $result['falv'];
            }
            $data['year'] = date('Y', time());
            $this->redisCluster->setex($this->cachePrefix.'footerData_'.$siteId, 900, json_encode($data));
        }
        return $data;
    }
}