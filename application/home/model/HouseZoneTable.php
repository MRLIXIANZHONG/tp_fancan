<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class HouseZoneTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'house_zone';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取字段值
     * @param string $id ID
     * @return  string
     */
    public function getByIdValue(string $id, string $field)
    {
        try{
            $entityList = $this->getReadQuery("select top 1 $field as keyWorld from $this->name(NOLOCK) where id = $id ");
            if(count($entityList) > 0){
                return $entityList[0]['keyWorld'];
            }
            return null;
        }catch (DbException $ex){
            return null;
        }
    }
    /**
     * 获取站点所在区域
     * @return  array
     */
    public function getHouseZone()
    {
        try{
            $entityValue = $this->getReadQuery("select view_lev, id, zonename from $this->name(NOLOCK) where siteid = $this->site_id order by view_lev,id");
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}