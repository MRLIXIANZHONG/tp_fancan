<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostChkTable extends Table
{
    // 分类信息小区表
    protected $name = 'PostChk';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 关注度显示配置
     * @return string
     */
    public function getHit()
    {
        try{
            $result = $this->getReadQuery("select top 1 HitType from $this->name(NOLOCK) where SiteId = $this->site_id");
            if(count($result) > 0){
                return  $result[0]['HitType'];
            }
            return $result;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取分类配置联系电话
     * @return  array
     */
    public function getServicesTel()
    {
        try{
            $entityValue = $this->getReadQuery("select top 1 ServicesTel,ServicesQQ from $this->name(NOLOCK) where siteid = $this->site_id");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}