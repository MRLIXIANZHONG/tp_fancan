<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class SiteTopUrlTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'site_top_url';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取站点类别
     * @param string $siteId  站点ID
     * @param int $fid   父ID
     * @param int $isDel 是否删除
     * @param int $condition 条件
     * @return  array
     */
    public function getByFid(int $siteId, int $fid, int $isDel, string $condition)
    {
        try{
            $where = " where siteid = $siteId and fid $condition $fid and isdel = $isDel ";
            $fields = ' id, sname, surl, spic, view_lev, fid ';
            $entityValue = $this->getUserQuery("select $fields from $this->name(NOLOCK) $where ");
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }

}