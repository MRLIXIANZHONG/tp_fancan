<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class HomeZjTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'home_zj';
    //没有使用id作为主键名
    protected $pk = 'id';


    /**
     * 查询当前账号是否是中介
     * @return  array expire
     */
    public function getIsZj()
    {
        try{
            $userName = session('username');
            $where = " where siteid = $this->site_id and username = '$userName'";
            $fields = ' id,cmdtime,oLinkP,oTel,oEmail,oQQ,id,ZjId,iscmd,AgentType ';
            $entityValue = $this->getReadQuery("select top 1 $fields from $this->name(NOLOCK) $where ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
    /**
     * 更新数据
     * @param array $data
     * @return int
     */
    public function update(array $data):int{
        try{
            $id = $data['id'];
            unset($data['id']);
            return $this->getWriteDb()->where('id', '=', $id)->update($data) > 0;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}