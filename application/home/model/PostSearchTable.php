<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class PostSearchTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'Post_Search';
    //没有使用id作为主键名
    protected $pk = 'id';
    // 设置当前模型的数据库连接
    /*protected $connection = ();*/
    /**
     * 新增
     * @param array $data
     * @return bool
     */
    public function insert(array $data){
        return $this->getWriteDb()->insert($data) > 0;
    }
    /**
     * 新增并获取主键
     * @param array $data
     * @return int
     */
    public function insertGetId(array $data):int{

        return $this->getWriteDb()->insertGetId($data);
    }

}