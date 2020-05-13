<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class LiveKindNewTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'live_kind_new';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取站点类别
     * @param int $fid 父ID
     * @return  array
     */
    public function getByFid(int $fid)
    {
        try{
            $where = [
                ['fid', '=', $fid],
                ['isdel', '=', 0],
            ];
            $entityValue = $this->getReadDb()->field(['viewlev', 'id', 'kindname', 'kindNextTitle'=>'kindTitle', 'kindNextSelect'=>'kindSelect'])->where($where)->order(['viewlev', 'id'])->select();
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
}