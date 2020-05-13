<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class HomeZxTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'Home_Zx';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 装修要求
     * @return  array
     */
    public function getHouseZx()
    {
        try{
            $entityValue = $this->getReadDb()->field(['id', 'sName'=>'name'])->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}