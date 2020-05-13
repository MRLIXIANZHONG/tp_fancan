<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class CheliangTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'cheliang_ch';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 二手商家信息
     * @param string $username 账号名称
     * @return  array
     */
    public function getInfo(string $username)
    {
        try{
            $where = " where siteid = $this->site_id and username = '$username' and CmdTime > getdate() ";
            $fields = ' oLinkP as linkman,oTel as tel,oQQ as qq ';
            $entityValue = $this->getReadQuery("select top 1 $fields from $this->name(NOLOCK) $where ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}