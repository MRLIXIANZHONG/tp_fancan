<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class WapChannelEwmTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'wap_channel_ewm';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取数据
     * @return  array
     */
    public function getBySiteId()
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['wpinfo', '<>', '']
            ];
            $entityValue = $this->getUserDb()
                ->field(['cid', 'isclose', 'wpinfo', 'wpcode', 'wxinfo', 'wxcode'])
                ->where($where)
                ->order(['cid'])
                ->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取数据
     * @return  array
     */
    public function getByCid(int $cid)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['cid', '=', $cid]
            ];
            $entityValue = $this->getUserDb()
                ->field(['wxcode'])
                ->where($where)
                ->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}