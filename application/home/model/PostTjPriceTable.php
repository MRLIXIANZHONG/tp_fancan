<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostTjPriceTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'post_tj_price';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 通过 $channel 获取支付推荐价格
     * @param int $channel 类别
     * @return  string
     */
    public function getRecommendList(int $channel)
    {
        try{
            $where = " where siteid=$this->site_id and Channel = $channel ";
            $orderBy = ' Order By Num, id Desc ';
            $entityList = $this->getReadQuery("select top 5 id,Num,price from $this->name(NOLOCK) $where  $orderBy ");
            return $entityList;
        }catch (DbException $ex){
            return null;
        }
    }
}