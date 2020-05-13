<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class HomeSellTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'house_sell';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 二手商家信息
     * @param string $username 账号名称
     * @return  array
     */
    public function getInfo(string $username)
    {
        try{
            $where = " where siteid = $this->site_id and vipUserName = '$username' ";
            $entityValue = $this->getReadQuery(" Select Top 1 id From $this->name(NOLOCK) $where ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}