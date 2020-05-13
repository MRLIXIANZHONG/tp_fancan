<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class JobOlrzTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'Job_Olrz';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取站点类别
     * @param  $username 账号名称
     * @param  $isChk 审核
     * @return  array
     */
    public function getByUserName($username, $isChk)
    {
        try{
            $entityValue = $this->getReadQuery("select top 1 CompPic from $this->name(NOLOCK) where SiteId = $this->site_id and UserName = '$username' and ccoochk = $isChk ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}