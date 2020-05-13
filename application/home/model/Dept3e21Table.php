<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/11/01/111
 * Time: 15:46
 */
namespace app\home\model;

use think\exception\DbException;


class Dept3e21Table extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'dept3e21';
    //没有使用id作为主键名
    protected $pk = 'dept_id';


    /**
     *
     * @param string $deptId
     * @return  array
     */
    public function getByDeptId(string $deptId)
    {
        try{
            $fields = 'dept_name,wwwurl,tell,address,fax,email,post,qq,ba,wangjing_code,wangjing_img,tongji_code,falv,linkman ';
            $entityValue = $this->getUserQuery("select top 1 $fields from $this->name(NOLOCK) where dept_id = $deptId ");
            if(count($entityValue) > 0){
                return $entityValue[0];
            }
            return $entityValue;
        }catch (DbException $ex){
            return json($ex->getMessage());
        }
    }
}