<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2020/1/13/013
 * Time: 16:12
 */

namespace app\home\traits;

use app\home\logic\HeaderLogic;
use app\home\logic\FooterLogic;


trait HeaderFooter
{
    public function getHeaderFooter()
    {
        if ($this->is_login == 1){
            //获取未读消息
            $noreadmsg=(new HeaderLogic())->getNoneMsg(session('username'), $this->site_id, 'post');
            $this->assign('noreadmsg',$noreadmsg);
            $this->assign('nickname',session('nickname'));
        }else{
            $this->assign('noreadmsg',0);
        }
        $this->assign('is_login', $this->is_login);
        //重写底部
        $footerData = (new FooterLogic())->getFooterData($this->site_id);
        $this->assign('footerData', $footerData);
        //重写顶部
        $topData = (new HeaderLogic())->getHeaderData($this->site_id);
        $this->assign('top_info1', $topData['top_info']);
        $this->assign('top_info2', $topData['top_info2']);

        $pc_url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->assign('pc_url', urlencode($pc_url));
        $this->assign('site_id', $this->site_id);

        $this->assign('siteName',$this->siteName);
        $this->assign('areaName',$this->areaName);
        //获取是否有招聘会
        $zph_num=(new HeaderLogic())->getZphNum($this->site_id);
        $this->assign('zph_num',$zph_num);

    }
}