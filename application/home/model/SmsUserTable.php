<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class SmsUserTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'sms_user';
    //没有使用id作为主键名
    protected $pk = 'ID';


    /**
     * 获取用户信息
     * @param string $userName 用户名
     * @return  array
     */
    public function getByUserName(string $userName)
    {
        try{
            $entityValue = $this->getSmsQuery("select top 1 username from $this->name(NOLOCK) where username='".$userName."'");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 查询分类
     * @param string $userName 用户名
     * @return bool
     */
    public function getSumName(string $userName)
    {
        try{
            $entityList = $this->getSmsQuery("select top 1 sum(postxtnoread+usernoread+jctsnoread) as notread from sms_user where username='".$userName."'");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 查询所有
     * @param string $userName 用户名
     * @return bool
     */
    public function getSumAllName(string $userName)
    {
        try{
            $entityList = $this->getSmsQuery("select top 1 isnotread as notread from sms_user where   username='".$userName."'");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 查询社区系统
     * @param string $userName 用户名
     * @return bool
     */
    public function getSumCommunity(string $userName)
    {
        try{
            $entityList = $this->getSmsQuery("select top 1 sum(bbsxtnoread+usernoread+jctsnoread) as notread from sms_user where username='".$userName."'");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 新增
     * @param array $data
     * @return bool
     */
    public function insert(array $data){
        return $this->getSmsDb()->insert($data) > 0;
    }
    /**
     * 新增并获取主键
     * @param array $data
     * @return int
     */
    public function insertGetId(array $data):int{

        return $this->getSmsDb()->insertGetId($data);
    }
}