<?php

/**
 * 逻辑基础类
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 13:32
 */
namespace app\home\logic;

use think\facade\Config;
use think\facade\Cache;
use Predis\Client;

class Logic
{
    use \traits\controller\Jump;

    protected $site_id;    //站点ID
    protected $cachePrefix;    //缓存标识
    protected $redisCluster;    //缓存标识
    protected $dataResult = [
        'code' => -1,
        'msg' => '',
        //'url' => '',
        'data' => []
    ];

    public function __construct()
    {
        $this->cachePrefix = 'Security_PCPHP_';
        if(!$this->redisCluster){
            if(!Config::get('database.redis')){
                return '请配置，redis服务器地址！';
            }
            if(Cache::get('redis_db')){
                $this->redisCluster = Cache::get('redis_db');
            }else{
                $this->redisCluster = new Client(Config::get('database.redis.database'),Config::get('database.redis.auth'));
                Cache::set('redis_db', $this->redisCluster, 60);
            }
        }
    }
    /**
     *
     * @param bool $result
     */
    protected function reset($result = false){
        $this->dataResult['code'] = $result ? $result : -1;
        $this->dataResult['msg'] = '';
        //$this->dataResult['url'] = '';
        $this->dataResult['data'] = [];
    }
    /**
     * 获取数组中的某个数据
     * @param array 原数组
     * @param string Key
     * @param $default 默认值
     * @param string 参数类型.string, int, float, bool, string
     * @return mixed|string
     */
    protected function getParam(array $data, string $key, $default, string $type = 'string'){
        $type = trim(strtolower($type));
        if(isset($data[$key])){
            switch ($type){
                case 'int':
                    if(is_numeric($data[$key])) return intval($data[$key]);
                    break;
                case "float":
                    if(is_numeric($data[$key])) return floatval($data[$key]);
                    break;
                case "bool":
                    return boolval($data[$key]);
                    break;
                case "string":
                    return trim($data[$key]);
                    break;
                case 'array':
                    if(is_array($data[$key])) return $data[$key];
                default:
                    return trim($data[$key]);
                    break;
            }

        }
        return $default;
    }

    /**
     * 获取参数中某个整数数据
     * @param array 原数组
     * @param string key
     * @param int 默认值
     * @return int
     */
    protected function getIntInParam(array $data, string $key, int $default = 0){
        if(isset($data[$key]) && is_numeric($data[$key])){
            return intval($data[$key]);
        }
        return $default;
    }

    /**
     * 获取参数中某个浮点型数据
     * @param array 原数组
     * @param string key
     * @param float 默认值
     * @return float
     */
    protected function getFloatInParam(array $data, string $key, float $default = 0.00){
        if(isset($data[$key]) && is_numeric($data[$key])){
            return floatval($data[$key]);
        }
        return $default;
    }
    /**
     * 获取布尔值参数
     * @param array $data
     * @param string $key
     * @param bool $default
     * @return bool
     */
    protected function getBoolInParam(array $data, string $key, bool $default = false){
        if(isset($data[$key])){
            return boolval($data[$key]);
        }
        return $default;
    }
    /**
     * 组合返回数组
     * @param string $message
     * @param array $data
     * @param bool $status
     * @return array
     */
    protected function getResult($message = '', array $data = [], $status = false): array{
        $this->reset($status);
        $this->dataResult['msg'] = $message;
        $this->dataResult['data'] = $data;
        return $this->dataResult;
    }
}