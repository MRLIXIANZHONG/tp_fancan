<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class CheckTelTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'CheckTel';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 查询电话禁发
     * @param string $tel 电话
     * @param string $username 账号
     * @return  array
     */
    public function getList(string $tel, string $username)
    {
        try{
            $whereOr = [
                ['Tel', '=', $tel],
                ['Tel', '=', $username],
            ];
            $entityValue = $this->getReadDb()->field(['id'])->whereOr($whereOr)->find();
            return $entityValue;
        }catch (DbException $ex){
            return null;
        }
    }
}