<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class CarBrandTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'car_brand';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 去重找到所有字母简写
     * @param int $fid 父ID
     * @return  array
     */
    public function getByFid(int $fid)
    {
        try{
            $where = [
                ['pid', '=', $fid]
            ];
            $entityValue = $this->getReadDb()->distinct(true)->field(['Ename'])->where($where)->order(['Ename'])->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 去重找到所有字母简写
     * @param int $fid 父ID
     * @return  array
     */
    public function getFidList(int $fid)
    {
        try{
            $where = [
                ['pid', '=', $fid]
            ];
            $entityValue = $this->getReadDb()->field(['kindid', 'kindname', 'Ename', 'indexShow', 'sort'])
                                             ->where($where)
                                             ->order(['Ename', 'indexShow'=>'desc', 'sort'])
                                             ->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 热门品牌
     * @param int $fid 父ID
     * @param int $indexShow 是否首页展示
     * @return  array
     */
    public function getHotList(int $fid, int $indexShow)
    {
        try{
            $where = [
                ['pid', '=', $fid],
                ['indexShow', '=', $indexShow]
            ];
            $entityValue = $this->getReadDb()->field(['kindid', 'kindname', 'Ename', 'indexShow', 'sort'])
                ->where($where)
                ->order(['Ename', 'indexShow'=>'desc', 'sort'])
                ->select();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }

}