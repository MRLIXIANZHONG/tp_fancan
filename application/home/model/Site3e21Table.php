<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class Site3e21Table extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'site_3e21';
    //没有使用id作为主键名
    protected $pk = 'siteid';


    /**
     * 获取配置联系电话
     * @param int $tabCls 类别
     * @return  array
     */
    public function getSiteTel2()
    {
        try{
            $where = [
                ['a.siteid', '=', $this->site_id]
            ];
            $field = "ISNULL(b.tell,'') as tel, b.qq";
            $entityValue = $this->getUserDb()->field($field)
                ->alias('a')
                ->leftJoin('dept3e21 b', 'a.companyno = b.dept_id')
                ->where($where)
                ->find();
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    public function getSiteTel()
    {
        try{
            $where = " where a.siteid = $this->site_id ";
            $fields = "ISNULL(b.tell,'') as tel, b.qq";
            $entityValue = $this->getUserQuery("select $fields  from $this->name(NOLOCK) as a left join dept3e21(NOLOCK) as b on a.companyno = b.dept_id  $where ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 查询站点信息所在域名
     * @param int $siteId 站点ID
     * @return  array
     */
    public function getSiteName(int $siteId)
    {
        try{
            $where = " where siteid = $siteId";
            $fields = ' siteid,site_name as sitename,weburl,areatitle,companyno,web_dir,areano,isview,isjf,web_type,viewtime ';
            $entityValue = $this->getUserQuery("select top 1 $fields from $this->name(NOLOCK) $where ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}