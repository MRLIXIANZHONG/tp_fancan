<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 19:23
 */

namespace app\home\model;


use think\facade\Config;
use think\Db;
use think\facade\Cache;

class Table
{
    protected $site_id;    //站点ID
    protected $read_info;  //分类只读库连接信息
    protected $write_info; //分类只写库连接信息
    protected $name = "";  //带前缀的表名
    protected $pk;         //主键
    protected $readDb = null; //创建db实例
    protected $writeDb = null;
    protected $userDb = null;
    protected $countDb = null;
    protected $smsDb = null;
    protected $smsWriteDb = null;


    public function __construct()
    {
        if(!$this->userDb){
            $this->userDb = Db::connect('user');
        }
/*        //30秒数据库切换一个库
        if(Cache::get('read_postdb_'.$this->site_id)){
            $this->read_info = Cache::get('read_postdb_'.$this->site_id);
        }else{
            echo '换库'.'<br />';
            $data_name=$this->get_database_name();
            $data_info=$this->get_read_dbinfo();
            $data_info['database'] = $data_name;
            $this->read_info = $data_info;
            Cache::set('read_postdb_'.$this->site_id, $data_info, 30);
        }
        //查询库
        if(!$this->readDb){
            $this->readDb = Db::connect($this->read_info);
        }*/
        //初始化连接未读消息数据库
        if(!$this->smsDb){
            $this->smsDb = Db::connect('sms');
        }

        //$this->write_info = $this->get_write_info();
    }
    /**
     * 获取连接数据库名称
     */
    public function get_read_dbinfo()
    {
        $rondow=mt_rand(0,4);
        switch ($rondow){
            case 0:
                return Config::get('database.read');
                break;
            case 4:
                return Config::get('database.read');
                break;
            case 1:
                return Config::get('database.read1');
                break;
            case 2:
                return Config::get('database.read1');
                break;
            case 3:
                return Config::get('database.read1');
                break;
            default:
                return Config::get('database.read1');
        }

    }
    /**
     * 获取只读库的数据库名称
     */
    public function get_database_name()
    {
        $site_id=$this->site_id;
        if (intval($site_id)>=1&&intval($site_id)<601){
            return '3e21post';
        }elseif (intval($site_id)>=601&&intval($site_id)<1201){
            return '3e21post52';
        }elseif (intval($site_id)>=1201&&intval($site_id)<1801){
            return '3e21post54';
        }elseif (intval($site_id)>=1801){
            return '3e21post90';
        }
    }
    /**
     * 获取主库连接信息
     */
    public function get_write_info()
    {
        $site_id=$this->site_id;
        if (intval($site_id)>=1&&intval($site_id)<601){
            return Config::get('database.write');
        }elseif (intval($site_id)>=601&&intval($site_id)<1201){
            return Config::get('database.write1');
        }elseif (intval($site_id)>=1201&&intval($site_id)<1801){
            return Config::get('database.write2');
        }elseif (intval($site_id)>=1801){
            return Config::get('database.write3');
        }
    }
    /**
     * 获取读Db连接实体
     * @param string $name
     * @return Db
     */
    protected function getReadDb($name = ''){
        /*if(empty($this->readDb)){
            $this->readDb = Db::connect($this->read_info);
        }*/
        return $this->readDb->table($name != '' ?: $this->name);
    }
    /**
     * 原生语句查询
     * @param string $query
     * @return Db
     */
    protected function getReadQuery($query){
        if(empty($this->readDb)){
            $this->readDb = Db::connect($this->read_info);
        }
        return $this->readDb->query($query);
    }
    /**
     * 打印读sql语句
     * @param string $name
     * @return Db
     */
    protected function getReadLastSql(){
        return $this->readDb->getLastSql();
    }
    /**
     * 获取写Db连接实体
     * @param string $name
     * @return Db
     */
    protected function getWriteDb($name = ''){
        if(empty($this->writeDb)){
            $this->writeDb = Db::connect($this->write_info);
        }
        return $this->writeDb->table($name != '' ?: $this->name);
    }
    /**
     * 存储过程调用 无返回集
     * @param string $query
     * @return Db
     */
    protected function getWriteExecute($query){
        if(empty($this->writeDb)){
            $this->writeDb = Db::connect($this->write_info);
        }
        return $this->writeDb->execute($query);
    }
    /**
     * 带参数的存储过程 有返回集
     * @param string $query
     * @param array $bind
     * @return Db
     */
    protected function getWriteQuery($query, $bind){
        if(empty($this->writeDb)){
            $this->writeDb = Db::connect($this->write_info);
        }
        return $this->writeDb->query($query, $bind);
    }
    /**
     * 打印写sql语句
     * @param string $name
     * @return Db
     */
    protected function getWriteLastSql(){
        return $this->readDb->getLastSql();
    }
    /**
     * 获取用户库Db连接实体
     * @param string $name
     * @return Db
     */
    protected function getUserDb($name = ''){
        if(empty($this->userDb)){
            $this->userDb = Db::connect('user');
        }
        return $this->userDb->table($name != '' ?: $this->name);
    }
    /**
     * 获取用户库Db连接实体
     * @param string $name
     * @return Db
     */
    protected function getUserQuery($query){
        if(empty($this->userDb)){
            $this->userDb = Db::connect('user');
        }
        return $this->userDb->query($query);
    }
    /**
     * 获取统计库Db连接实体
     * @param string $name
     * @return Db
     */
    protected function getCountDb($name = ''){
        if(empty($this->countDb)){
            $this->countDb = Db::connect('tj');
        }
        return $this->countDb->table($name != '' ?: $this->name);
    }
    /**
     * 统计库原生语句写入
     * @param string $query
     * @param array $bind
     * @return Db
     */
    protected function getCountExecute($query, $bind){
        if(empty($this->countDb)){
            $this->countDb = Db::connect('tj');
        }
        return $this->countDb->query($query , $bind);
    }
    /**
     * 获取sms库Db连接实体
     * @param string $name
     * @return Db
     */
    protected function getSmsDb($name = ''){
        if(empty($this->smsDb)){
            $this->smsDb = Db::connect('sms');
        }
        return $this->smsDb->table($name != '' ?: $this->name);
    }
    /**
     * sms原生语句查询
     * @param string $query
     * @return Db
     */
    protected function getSmsQuery($query){
        if(empty($this->smsDb)){
            $this->smsDb = Db::connect('sms');
        }
        return $this->smsDb->query($query);
    }
    /**
     * 带参数的存储过程 有返回集
     * @param string $query
     * @param array $bind
     * @return Db
     */
    protected function getSmsWriteQuery($query, $bind){
        if(empty($this->smsWriteDb)){
            $this->smsWriteDb = Db::connect('sms_write');
        }
        return $this->smsWriteDb->query($query, $bind);
    }
}