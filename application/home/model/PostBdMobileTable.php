<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostBdMobileTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'post_bd_mobile';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取手机验证码
     * @param int $channel 类别
     * @return  array
     */
    public function checkTelCode(int $tel)
    {
        try{
            $where = [
                ['tel', '=', $tel],
            ];
            $entityValue = $this->getReadDb()->where($where)->order(['id'=>'desc'])->find();
            return $entityValue;
        }catch (DbException $ex){
            return null;
        }
    }
    /**
     * 新增
     * @param array $data
     * @return bool
     */
    public function insert(array $data)
    {
        return $this->getWriteDb()->insert($data) > 0;
    }
    /**
     * 更新数据
     * @param array $data
     * @return int
     */
    public function update(array $data):int{
        try{
            $id = $data['id'];
            unset($data['id']);
            return $this->getWriteDb()->where('id', '=', $id)->update($data) > 0;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}