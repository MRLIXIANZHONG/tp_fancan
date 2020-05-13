<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\db\exception\DbException;


class TrafficInfoTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'traffic_info';
    //没有使用id作为主键名
    protected $pk = 'id';
    // 设置当前模型的数据库连接
    /*protected $connection = ();*/
    /**
     * 发布间隔查询
     * @param string $title 标题
     * @return data
     */
    public function getByTitle(string $title)
    {
        try{
            $entityList = $this->getReadQuery("select Top 1 systime,ip,uptime  from  $this->name(nolock) where Siteid = $this->site_id And isdel = 0 And title = '".$title."' And datediff(n,uptime,getdate()) <= 60 And username = '".Session("username")."' order by id desc");
            return $entityList;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
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