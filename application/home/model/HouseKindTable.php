<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class HouseKindTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'house_kind';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取站点类别
     * @return  array
     */
    public function getHouseKind()
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id]
            ];
            $entityValue = $this->getReadDb()->field(['view_lev', 'id', 'kindname'])->where($where)->order(['view_lev', 'id'])->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}