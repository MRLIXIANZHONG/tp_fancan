<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class HomeSheBeiTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'Home_SheBei';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取房屋特色
     * @return  array
     */
    public function getHouseOnly(int $istype)
    {
        try{
            $where = [
                ['istype', '=', $istype]
            ];
            $entityValue = $this->getReadDb()->field(['id', 'sName'=>'name'])->where($where)->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}