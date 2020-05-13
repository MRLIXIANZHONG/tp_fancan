<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class ChkqqTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'chk_qq';
    //没有使用id作为主键名
    protected $pk = 'qq';

    /**
     * 查询qq禁发
     * @param string $val 值
     * @return  array
     */
    public function getList(string $val)
    {
        try{
            $where = [
                ['qq', '=', $val]
            ];
            $entityValue = $this->getReadDb()->field(['qq'])->where($where)->find();
            return $entityValue;
        }catch (DbException $ex){
            return null;
        }
    }
}