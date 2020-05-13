<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class WapSmsTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'wap_sms';
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
                ['siteid', '=', $this->site_id]
            ];
            $entityValue = $this->getUserDb()
                ->field(['isclose', 'wap_weima'])
                ->where($where)
                ->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}