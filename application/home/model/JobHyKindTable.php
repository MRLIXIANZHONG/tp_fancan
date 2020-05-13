<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class JobHyKindTable extends Table
{
    //招聘行业类别表
    protected $name = 'job_hy_kind';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取招聘类别名称
     * @param string $id ID
     * @return  array
     */
    public function getById(string $id)
    {
        try{
            $entityValue = $this->getReadQuery("Select top 1 kindname From $this->name where (id= $id or oid= $id)");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return null;
        }catch (DbException $ex){
            return null;
        }
    }
}