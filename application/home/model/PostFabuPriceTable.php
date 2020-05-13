<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostFabuPriceTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'post_fabu_price';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 通过 $channel类别支付费用
     * @param int $channel 类别
     * @return  string
     */
    public function getByChannel(int $channel)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['isdel', '=', 0],
                ['channel', '=', $channel]
            ];
            $entityValue = $this->getReadDb()->where($where)->value('price');
            return $entityValue;
        }catch (DbException $ex){
            return null;
        }
    }
}