<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostQiYeKuTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'post_qiyeku';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取企业信息
     * @param string $username 账号名称
     * @param int $tabCls 类别
     * @return  array
     */
    public function getInfo(string $username, int $tabCls)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['username', '=', $username],
                ['tabCls', '=', $tabCls]
            ];

            $entityValue = $this->getReadDb()->field(['compname', 'oLinkMan'=>'linkman', 'oLinkTel'=>'tel' , 'oLinkEmail'=>'email', 'oLinkQq'=>'qq'])->where($where)->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取企业信息
     * @param string $username 账号名称
     * @param int $tabCls 类别
     * @return  array
     */
    public function getByName(string $username)
    {
        try{
            $entityValue = $this->getReadQuery("select top 1 compname from $this->name(NOLOCK) where siteid=$this->site_id and username = '$username' ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return null;
        }
    }
}