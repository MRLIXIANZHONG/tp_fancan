<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class Area3e21Table extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'area3e21';
    //没有使用id作为主键名
    protected $pk = 'areaid';


    /**
     * 获取区域
     * @param string $areaId 区域ID
     * @return  array
     */
    public function getByAreaId(string $areaId)
    {
        try{
            $entityValue = $this->getUserQuery("select top 1 area,lishu from $this->name(NOLOCK) where areaid = $areaId ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}