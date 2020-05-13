<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class JobQiYeKuTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'job_qiyeku';
    //没有使用id作为主键名
    protected $pk = 'id';
    // 设置当前模型的数据库连接
    /*protected $connection = ();*/
    /**
     * 查询账号是否是名企
     * @param string $name 账号
     * @return bool
     */
    public function getByName(string $name)
    {
        try{
            $entityList = $this->getReadQuery("select id from $this->name(NOLOCK) where siteid = $this->site_id and ismq=1 and username= '$name' and (isdel <> 1  OR isdel IS NULL) and CmdTime>getdate()");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 字段加1
     * @param int $id ID
     * @param string $field 更新字段
     * @return bool
     */
    public function setInc(int $id ,string $field, int $num=1)
    {
        try{
            $where = [
                ['id', '=', $id]
            ];
            $entityValue = $this->getWriteDb()->where($where)->setInc($field, $num);
            return $entityValue;
        }catch (DbException $ex){
            //return json($ex->getMessage());
            return null;
        }
    }
    /**
     * 查询公司地址等
     * @param string $name 账号
     * @return bool
     */
    public function getByList(string $name)
    {
        try{
            $entityList = $this->getReadQuery("select id as m_id,oMap,TempLogo,uppic,compname as kcompname,compinfo as kcompinfo,compaddr as kcompaddr,comptrade as kcomptrade,compkind as kcompkind,compsize as kcompsize from $this->name(NOLOCK) where siteid = $this->site_id and username= '$name' ");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}