<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class CarKindTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'car_kind';
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
                ['pid', '=', $fid]
            ];
            $entityValue = $this->getReadDb()->field(['kindid', 'kindname'])->where($where)->order(['kindid'])->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }

}