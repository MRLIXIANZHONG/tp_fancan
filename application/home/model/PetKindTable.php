<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class PetKindTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'pet_kind';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 根据id获取宠物类别数据
     * @param int $channel 类别
     * @return  array
     */
    public function getByFid(int $id, int $status = 0)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['fid', '=', $id],
                ['kindlev', '=', $status]
            ];
            if($status){
                $where[1][2] = $status;
            }
            $entityList = $this->getReadDb()->field(['id', 'viewlev', 'kindname'])->where($where)->order('viewlev', 'asc')->select();
            return $entityList;
        }catch (DbException $ex){
            return null;
        }
    }
}