<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/6/006
 * Time: 11:47
 */
namespace app\home\traits;

use app\home\model\PostSetupTable;
use app\home\model\Site3e21Table;
use app\home\model\PostChkTable;

trait CompTelqq
{
    /**
     * 获取联系电话
     * @param int $tabCls
     * @Tabcls: 0：招聘 1：房产 2：二手 3：车辆 4：生活 7：交友 8：宠物
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


}