<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostShouCangTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'post_shoucang';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 是否收藏
     * @param  $tid 父ID
     * @param  $username 用户名
     * @param  $tname 表名
     * @return  array
     */
    public function getByTid($tid, $username, $tname)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['username', '=', $username],
                ['tid', '=', $tid],
                ['tname', '=', $tname]
            ];
            $entityValue = $this->getReadDb()->field(['id'])->where($where)->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }

}