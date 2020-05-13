<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class JobUserTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'job_user';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取职位申请记录
     * @param  $username 账号名称
     * @param  $classId 职位ID
     * @return  array
     */
    public function getByUserName($username, $classId)
    {
        try{
            $entityValue = $this->getReadQuery("select top 1 addTime from $this->name(NOLOCK) where siteid = $this->site_id and username = '$username' and classId = $classId order by id desc ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}