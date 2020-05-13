<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class JobJZInfoTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'job_JZ_info';
    //没有使用id作为主键名
    protected $pk = 'id';
    // 设置当前模型的数据库连接
    /*protected $connection = ();*/
    /**
     * 获取兼职详情数据
     * @param int $id ID
     * @return bool
     */
    public function getById(int $id)
    {
        try{
            $field = ' j.id,j.username,j.IsExit,j.IsSite,j.hit,j.kind,j.ClassId,j.areaId,j.countJl,j.title,j.info,j.salary,j.salarytype,j.paytype,j.edittime,j.number,j.tel,j.linkman,j.qq,j.email,j.compname,j.compinfo,j.compaddr,j.comptrade,j.compkind,j.compsize,k.kindname,k.id as k_id,q.id as m_id,q.oMap,q.TempLogo,q.uppic,q.compname as kcompname,q.compinfo as kcompinfo,q.compaddr as kcompaddr,q.comptrade as kcomptrade,q.compkind as kcompkind,q.compsize as kcompsize ';
            $entityList = $this->getReadQuery("select $field  from $this->name(NOLOCK) as j left join job_qiyeku(NOLOCK) as q on j.siteid = q.siteid and j.username = q.username left join job_JZ_kind(NOLOCK) k on j.kind = k.id where j.id = $id ");
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
     * 获取其他最新招聘
     * @param  $id 详情ID
     * @param  $kind 类别ID
     * @param  $classId classId
     * @return array
     */
    public function getOthersList($id, $num, $kind=null, $classId=null)
    {
        try{
            $where = "where siteid = $this->site_id and id <> $id and ccoochk = 1 and isdel = 0 ";
            if($kind){
                $where .= " and kind = $kind ";
            }
            if($classId){
                $where .= " and ClassId = $classId ";
            }
            $fields = ' id,title,number,salary,workarea,salarytype,paytype,edittime,compname';
            if($kind){
                $fields .= ",(select top 1 compname from job_qiyeku with(nolock) where siteid=$this->site_id and job_qiyeku.username=job_JZ_info.username) as kcompname ";
            }
            $orderBy = ' Order By IsCmd Desc,edittime Desc, id Desc ';
            $data = $this->getReadQuery("select top $num $fields from $this->name(NOLOCK) $where $orderBy ");
            return $data;
        }catch (DbException $ex){
            return null;
        }
    }
    /**
     * 当前职位是否置顶推荐
     * @param  $id 详情ID
     * @param  $istj 是否推荐(都是零)
     * @return array
     */
    public function getRecommend($id, $istj)
    {
        try{
            $entityList = $this->getReadQuery("select top 1 id from $this->name(NOLOCK) where id = $id and istj = $istj and istjtime > getdate()");
            if(count($entityList) > 0){
                return $entityList[0];
            }
            return $entityList;
        }catch (DbException $ex){
            return null;
        }
    }
    /**
     * 获取公司其它职位
     * @param  $id 详情ID
     * @param  $kind 类别ID
     * @param  $classId classId
     * @return array
     */
    public function getPage($id, $kind, $classId)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['kind', '=', $kind],
                ['ClassId', '=', $classId],
                ['isdel', '=', 0],
                ['ccoochk', '=', 1],
                ['id', '<>', $id],
            ];
            $entityList = $this->getReadDb()
                ->field(['id', 'title', 'salary', 'salarytype', 'workarea', 'edittime'])
                ->where($where)
                ->limit(5)
                ->order(['edittime'=>'desc', 'id'=>'desc'])
                ->select();
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 根据账号名称
     * @param  $id
     * @return  string
     */
    public function getUserNameById($id)
    {
        try{
            $data = $this->getReadQuery("select top 1 username from $this->name(NOLOCK) where siteid = $this->site_id and id = $id and ccoochk = 1 and isdel = 0");
            if(count($data) > 0){
                return  $data[0]['username'];
            }
            return $data;
        }catch (DbException $ex){
            return null;
        }
    }
    /**
     * 获取其他职位总条数
     * @param  $id
     * @return  number
     */
    public function getOtherCount2($id, $userName=null)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['id', '<>', $id],
                ['ccoochk', '=', 1],
                ['isdel', '=', 0],
            ];
            if($userName){
                $where[] = ['username', '=', $userName];
            }
            $num = $this->getReadDb()->where($where)->count();
            return $num;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    public function getOtherCount($id, $userName=null)
    {
        try{
            $condition = '';
            if($userName){
                $condition = " and username = '$userName' ";
            }
            $num = $this->getReadQuery("select count(*) as num from $this->name(NOLOCK)  where siteid = $this->site_id and id <> $id and ccoochk = 1 and isdel = 0 $condition ");
            return $num[0]['num'];
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取相似职位总条数
     * @param  $id
     * @return  number
     */
    public function getSimilarCount($id, $kind, $classId)
    {
        try{
            $where = " where siteid = $this->site_id and id <> $id and ccoochk = 1 and isdel = 0 and kind = $kind and ClassId = $classId ";
            $num = $this->getReadQuery("select count(*) as num from $this->name(NOLOCK)  $where ");
            return $num[0]['num'];
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取热门职位总条数
     * @param  $id
     * @return  number
     */
    public function getHotCount($id)
    {
        try{
            $where = "where siteid = $this->site_id and id <> $id and ccoochk = 1 and isdel = 0 ";
            $num = $this->getReadQuery("select count(*) as num from $this->name(NOLOCK)  $where ");
            return $num[0]['num'];
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取公司其他职位
     * @param  $id
     * @return  number
     */
    public function getComOtherList($id, $userName, $start, $num)
    {
        try{
            $fields = ' id,title,number,salary,salarytype,paytype,edittime,compname';
            $fields .= ",(select top 1 compname from job_qiyeku with(nolock) where siteid=$this->site_id and job_qiyeku.username=job_JZ_info.username) as kcompname ";
            $orderBy = ' Order By edittime Desc, id Desc ';
            $limit = " offset $start rows fetch next $num rows only ";
            $data = $this->getReadQuery("select  $fields from $this->name(NOLOCK) where siteid = $this->site_id and id <> $id and username ='$userName' and ccoochk = 1 and isdel = 0 $orderBy $limit ");
            return $data;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}