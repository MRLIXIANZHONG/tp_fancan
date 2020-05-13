<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/18/018
 * Time: 11:56
 */

namespace app\home\controller;

use think\Db;
use think\facade\Config;
use think\Controller;
use think\Model;
use app\home\logic\HeaderLogic;
use app\home\logic\FooterLogic;
use think\facade\Cache;

class Base extends Controller
{
    public $siteName; //地名
    protected $site_id;
    public $areaName; //站点名
    public $is_login; //是否登陆
    public $web_url; //站点域名url
    public $pc_url;
    public $stype=5; //用户访问环境 1：微信 3：安卓 4：ios 5：pc
    public $user;  //用户登录信息
    public $username;  //用户登录信息
    public $under_info=[];//底部信息
    protected $data_info; //分类只读库连接信息

    public function __construct()
    {
        parent::__construct();
        define('MODULE_NAME',$this->request->module());  // 当前模块名称是
        define('CONTROLLER_NAME',$this->request->controller()); // 当前控制器名称
        define('ACTION_NAME',$this->request->action()); // 当前操作名称是
        $this->check_site_id();
        
        $this->is_login=$this->check_login();
        $this->stype=$this->get_type();
    }
    //检测是cookie的站点id
    public function check_site_id()
    {
        if (!cookie('site_id')){
            $doMain = $_SERVER["HTTP_HOST"];
            $wxDoMain = (new HeaderLogic())->getSiteInfo($doMain);
            if (!$wxDoMain){
                LogRecord('未获取到站点信息', request()->url(true), 'app/home/controller/base', 'PC移植PHP');
                return json('未获取到该站点信息', 404);
            }
            //$wxDoMain =DB::table('tp_wx_domain')->where(array('domain'=>$doMain))->find(); //获取微信配置
            if(isset($wxDoMain['siteid']) && isset($wxDoMain['areatitle']) && isset($wxDoMain['site_name'])){
                $this->site_id = (int)$wxDoMain['siteid'];
                $this->areaName=$wxDoMain['areatitle'];
                $this->siteName=$wxDoMain['site_name'];
                setcookie('site_id',$wxDoMain['siteid'], time()+3600, '/');
                setcookie('siteid',$wxDoMain['siteid'], time()+3600, '/');
                //setcookie('wuser_id',$wxDoMain['uid'], time()+3600, '/');
                setcookie('sitename',$wxDoMain['areatitle'], time()+3600, '/');
                setcookie('site',$wxDoMain['site_name'], time()+3600, '/');
            }

        }else{
            $this->site_id=cookie('site_id');
            $this->areaName=cookie('sitename');
            $this->siteName=cookie('site');
        }
    }

    //检查用户是否登陆
    public function check_login()
    {
        $userinfo=cookie('ccoo');
        if ($userinfo){
            //$userinfo=iconv('GBK','utf-8',$userinfo);
            $userinfo = charset_icon($userinfo, 'UTF-8');
            $userinfo_arr=explode('&',$userinfo);
            $new_user_info_arr=[];
            foreach($userinfo_arr as $v){
                $new_user_info_arr[explode('=',$v)[0]]=explode('=',$v)[1];
            }
            if(isset($new_user_info_arr['uid']) && isset($new_user_info_arr['uchk']) && isset($new_user_info_arr['username']) ){
                if ($new_user_info_arr['uid'] !='' && $new_user_info_arr['uchk'] !='' && $new_user_info_arr['username'] !='' ){
                    session('username', $new_user_info_arr['username']);
                    session('uid', $new_user_info_arr['uid']);
                    session('nickname',$new_user_info_arr['nick']);
                    session('userface', $new_user_info_arr['userface']);
                    setcookie('nickname',$new_user_info_arr['nick'],time()+3600);
                    setcookie('userface',$new_user_info_arr['userface'],time()+3600);
                    $this->username=$new_user_info_arr['username'];
                    return 1;
                }else{
                    session(null);
                    cookie('nickname', null);
                    cookie('userface', null);
                    return 0;
                }
            }else{
                session(null);
                cookie('nickname', null);
                cookie('userface', null);
                return 0;
            }
        }else{
            return 0;
        }

    }
    //检测是否是手机环境
    public function get_type(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, 'MicroMessenger') !== false){
            return 1;
        }else if(strpos($agent, 'ccoocity_android') !== false){
            return 3;
        }else if(strpos($agent, 'ccoocity_ios') !== false){
            return 4;
        }else {
            return 5;
        }
    }
    /**
     * 获取传入的参数
     * @return mixed
     */
    protected function getParams(){
        if($this->request->isPost()){
            $params = $this->request->post();
        }else {
            $params = $this->request->param();
        }
        return is_array($params) ? $params : [];
    }
}