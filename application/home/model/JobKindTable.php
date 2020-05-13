<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class JobKindTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'job_kind';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取站点类别
     * @param int $fid 父ID
     * @return  array
     */
    public function getById(int $id)
    {
        try{
            $entityList = $this->getReadQuery("SELECT id as k_id, kindname FROM $this->name(NOLOCK) WHERE id=$id");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }

}