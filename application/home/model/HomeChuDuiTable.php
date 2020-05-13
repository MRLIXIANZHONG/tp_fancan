<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class HomeChuDuiTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'Home_Chudui';
    //没有使用id作为主键名
    protected $pk = 'id';
    // 设置当前模型的数据库连接
    /*protected $connection = ();*/
    /**
     * 发布间隔查询
     * @param array $data
     * @return bool
     */
    public function getByTitle(string $title)
    {
        try{
            $entityList = $this->getReadQuery("select Top 1 id  from  $this->name where Siteid = $this->site_id And isdel = 0 And oTitle = '".$title."' And datediff(n,uptime,getdate()) <= 60 And username = '".Session("username")."' order by id desc");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 发布的数据
     * @param array $IsChk 1通过审核 0未通过
     * @param array $isdel 1逻辑删除 0显示
     * @return bool
     */
    public function getList(int $IsChk, int $isdel)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['UserName', '=', session('username')],
                ['IsChk', '=', $IsChk],
                ['isdel', '=', $isdel]
            ];
            $entityList = $this->getReadDb()->where($where)->select();
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 查询当前账号发布的数量
     * @param array $IsChk 1通过审核 0未通过
     * @param array $isdel 1逻辑删除 0显示
     * @param array $ZjId 中介id
     * @param array $iszj 1中介 0 no
     * @return bool
     */
    public function getListCount(int $IsChk, int $isdel, int $ZjId , int $iszj = 0)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['UserName', '=', session('username')],
                ['IsChk', '=', $IsChk],
                ['isdel', '=', $isdel],
                ['ZjId', '=', $ZjId],
                ['iszj', '=', $iszj]
            ];
            $entityCount = $this->getReadDb()->where($where)->count();
            return $entityCount;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
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