<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class FwxqTable extends Table
{
    // 分类信息小区表
    protected $name = 'Fwxq';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 是否配置小区
     * @return  array
     */
    public function getKindVillage()
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id]
            ];
            $entityValue = $this->getReadDb()->field(['id'])->where($where)->count();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 根据小区名称获取小区id
     * @param string $data
     * @return  array
     */
    public function getVillageId(string $title)
    {
        try{
            $where = [
                ['Siteid', '=', $this->site_id],
                ['title', '=', $title]
            ];
            $id = $this->getReadDb()->where($where)->value('id');
            return $id;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
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
    /**
     * 计数
     * @param int $id
     * @param string 更新的字段
     * @return int
     */
    public function setInc(int $id, string $field):int{
        try{
            $where = [
                ['id', '=', $id],
            ];
            return $this->getWriteDb()->where($where)->setInc($field) > 0;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}