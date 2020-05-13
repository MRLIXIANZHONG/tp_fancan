<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostPayTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'postpay';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 支付开关是否开启
     * @param int $channel 类别
     * @return  array
     */
    public function getByPrice()
    {
        try{
            $entityValue = $this->getReadQuery("select top 1 PostTjPay from $this->name(NOLOCK) where siteid = $this->site_id ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return null;
        }
    }
}