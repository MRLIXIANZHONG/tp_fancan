<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2020/2/20
 * Time: 14:21
 */

namespace app\home\model;


class ZphTable extends Table
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'zph_info';

    /**
     * 获取招聘会数量
     * @param string $siteId  站点ID
     */
    public function getZphNum($siteId)
    {
        try{

            $where = ' siteid='.$siteId.' and isstatus=1 and enddate>=getdate() and isdel=0';
            $fields = ' id,Title,startDate,endDate,Html ';
            $rs = $this->getReadQuery("select".$fields."from ".$this->name.' where'.$where );
            if ($rs){
                return 1;
            }else{
                return 0;
            }
        }catch (DbException $ex){
            return 0;
        }
    }
}