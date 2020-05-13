<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostSetupTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'post_setup';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取配置联系电话
     * @param int $tabCls 类别
     * @return  array
     */
    public function getServicesTel(int $tabCls)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['tabCls', '=', $tabCls]
                /*['ServicesTel', '<>', '']*/
            ];
            $entityValue = $this->getReadDb()->field(['ServicesTel', 'ServicesQQ'])->where($where)->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取系统是否开启审核
     * @param int $tabCls 类别
     * @return  array
     */
    public function getSetup(int $tabCls)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['tabCls', '=', $tabCls],
            ];
            $entityValue = $this->getReadDb()->field(['ccoochk', 'PostPower'])->where($where)->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}