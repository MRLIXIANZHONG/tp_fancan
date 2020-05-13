<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class EsKindTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'es_kind';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取站点类别
     * @param int $kindlev 父ID级别
     * @param int $fid 父ID
     * @return  array
     */
    public function getByPid(int $kindlev,int $pid)
    {
        try{
            $where = [
                ['kindlev', '=', $kindlev],
                ['pid', '=', $pid],
            ];
            $entityValue = $this->getReadDb()->field(['id', 'kindname', 'pid'])->where($where)->order(['id'])->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取类别名称
     * @param int $fid 父ID
     * @return  array
     */
    public function getById(int $fid)
    {
        try{
            $where = [
                ['id', '=', $fid]
            ];
            $entityValue = $this->getReadDb()->field(['kindname'])->where($where)->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取三级类别
     * @param int $kindlev
     * @param int $pid
     * @return bool
     */
    public function getByPidLevel(int $kindlev, int $pid)
    {
        try{
            $entityList = $this->getReadQuery("select kindname,pid,id from $this->name where  kindlev=$kindlev and pid in (select id from es_kind where kindlev=1 and pid=$pid) order by id asc ");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}