<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class JobInfoTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'job_info';
    //没有使用id作为主键名
    protected $pk = 'id';
    // 设置当前模型的数据库连接
    /*protected $connection = ();*/
    /**
     * 获取全职详情数据(框架查询慢很多)
     * @param int $id ID
     * @return bool
     */
    public function getById(int $id)
    {
        try{
            $entityList = $this->getReadQuery("Select top 1 id,title,countJl,edittime,kind,hit,education,record,salary,number,workArea,email,linkman,qq,tel,UpPic,compname,compaddr,comptrade,compkind,compsize,username,IsExit,kind,ClassId,areaId,fuli,info,compinfo,issite,IsCmd,istjtime,maxsalary,minsalary From $this->name(NOLOCK) Where siteid=$this->site_id And isdel = 0 And ccoochk = 1 And id=$id ");
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
     * 获取相似职位和热门职位
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
            $fields = ' id,title,number,minSalary,education,record,maxSalary,edittime,compname,salary';
            if($kind){
                $fields .= ",(select top 1 compname from job_qiyeku with(nolock) where siteid=$this->site_id and job_qiyeku.username=job_info.username) as kcompname ";
            }
            $orderBy = ' Order By IsCmd Desc,edittime Desc, id Desc ';
            $data = $this->getReadQuery("select top $num $fields from $this->name(NOLOCK) $where $orderBy ");
            return $data;
        }catch (DbException $ex){
            //return json($ex->getMessage());
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
            $where = " where id = $id and istj = $istj and istjtime > getdate()";
            $entityList = $this->getReadQuery("select top 1 id from $this->name(NOLOCK) $where ");
            if(count($entityList) > 0){
                return $entityList;
            }
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
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
                ->field(['id', 'title', 'salary', 'workarea', 'edittime'])
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

            $orderBy = ' Order By edittime Desc, id Desc ';
            $fields = ' id,title,number,minSalary,maxSalary,edittime,compname';
            $fields .= ",(select top 1 compname from job_qiyeku with(nolock) where siteid=$this->site_id and job_qiyeku.username=job_info.username) as kcompname ";
            $limit = " offset $start rows fetch next $num rows only ";
            $data = $this->getReadQuery("select  $fields from $this->name(NOLOCK) where siteid = $this->site_id and id <> $id and ccoochk = 1 and isdel = 0 and username = '$userName' $orderBy $limit ");
            return $data;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取公司总的简历投递数量
     * @param int $id ID
     * @return bool
     */
    public function getResumeTotalNum($username)
    {
        try{
            $entityList = $this->getReadQuery("SELECT
                    (SELECT count(1) as sumCount FROM  JOB_USER(NOLOCK) AS A INNER JOIN JOB_JL(NOLOCK) AS B  ON B.id=A.jlid
                    WHERE A.SITEID=$this->site_id AND B.isdel=0 AND A.CLASSID in(select id from job_info(NOLOCK) where siteid=$this->site_id and isdel=0 and username='$username' and ccoochk =1))
                    +
                    (SELECT count(1) as sumCount FROM  JOB_JZ_USER(NOLOCK) AS A INNER JOIN JOB_JL(NOLOCK) AS B  ON B.id=A.jlid
                    WHERE A.SITEID=$this->site_id AND B.isdel=0 AND A.CLASSID in(select id from job_jz_info(NOLOCK) where siteid=$this->site_id and isdel=0 and username='$username' and ccoochk =1)) as num");
                                return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取公司总处理过的简历数
     * @param int $id ID
     * @return bool
     */
    public function getHandelTotalNum($username)
    {
        try{
            $entityList = $this->getReadQuery("SELECT
                    (SELECT count(1) as sumCount FROM  JOB_USER(NOLOCK) AS A INNER JOIN JOB_JL(NOLOCK) AS B  ON B.id=A.jlid
                    WHERE A.SITEID=$this->site_id AND A.isView=1 AND B.isdel=0 AND A.CLASSID in(select id from job_info(NOLOCK) where siteid=$this->site_id and isdel=0 and username='$username' and ccoochk =1))
                    +
                    (SELECT count(1) as sumCount FROM  JOB_JZ_USER(NOLOCK) AS A INNER JOIN JOB_JL(NOLOCK) AS B  ON B.id=A.jlid
                    WHERE A.SITEID=$this->site_id AND A.isView=1 AND B.isdel=0 AND A.CLASSID in(select id from job_jz_info(NOLOCK) where siteid=$this->site_id and isdel=0 and username='$username' and ccoochk =1)) as num");
                                return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}