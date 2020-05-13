<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class UsersTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'Users';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取用户信息
     * @param int $tabCls 类别
     * @return  array
     */
    public function getUserInfo($id, $pwd)
    {
        try{
            $where = " where id = $id and pwd = '$pwd' ";
            $fields = ' id,username,pwd,nick,userface,siteid,mobile,regtime,lastTime,logNum ';
            $entityValue = $this->getUserQuery("select top 1 $fields from $this->name(NOLOCK) $where ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }

}