<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class PostWhiteTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'postwhite';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 获取配置联系电话
     * @param int $tabCls 类别
     * @return  array
     */
    public function getWhite(string $strChkUser, int $tabCls)
    {
        try{
            $where = [
                ['siteid', '=', $this->site_id],
                ['ccooUser', '=', $strChkUser],
                ['tabCls', '=', $tabCls],
                ['expTime', 'EXP', '>getdate()']
            ];

            $entityValue = $this->getReadQuery("select top 1 oNum,id from $this->name(NOLOCK) where siteid = $this->site_id and ccooUser = '$strChkUser' and tabCls = $tabCls and expTime > getdate()");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return null;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 获取信息
     * @param int $tabCls 类别
     * @return  array
     */
    public function getInfo(string $strChkUser, int $tabCls)
    {
        try{

            $entityValue = $this->getReadQuery("select top 1 oNum,id from $this->name(NOLOCK) where siteid = $this->site_id and ccooUser = '$strChkUser' and tabCls = $tabCls ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return null;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}