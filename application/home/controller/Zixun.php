<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/18/018
 * Time: 15:24
 */

namespace app\home\controller;


use think\Db;
use think\facade\Config;
use think\Page;
class Zixun extends Base
{
    //咨询列表页
    public function index()
    {
        $class1=1;
        $kind=input('kind',1);
        $page=input('p',1);
        $outPage="_第".$page.'页';
        if ($kind==1){
            $kindname="职场资讯";
            $kindvalue="/post/zhaopin/"."zhichangzixun/";
        }elseif ($kind==2){
            $kindname="简历帮助";
            $kindvalue="/post/zhaopin/"."jianlibangzhu/";
        }else{
            $kindname="面试技巧";
            $kindvalue="/post/zhaopin/"."mianshijiqiao/";
        }
        $url_var=$kindvalue;
        $pageNum=10;
        //数据库资讯列表查询
        $SetStrTable="site_news";
        $order="ID Desc";
        $strTextList="id,title,uptime,hit,memo,ispic";

        $where="siteid=".$this->site_id." and kind=".$kind;
        $db=Db::connect($this->data_info);
        $count=$db->table($SetStrTable)->where($where)->count();

        $zixun_list=Db::connect($this->data_info)->table($SetStrTable)->field($strTextList)->where($where)->order($order)->limit(($page-1)*$pageNum,$pageNum)->select();
        //dump($zixun_list);die;
        foreach($zixun_list as &$value){
            if ($value['memo']){
                $value['memo']=$new_str=str_handle($value['memo'],1,75);
            }
        }

        //查询其他类别推荐信息
        if ($kind==1){
            $tuijina1=Db::connect($this->data_info)->table($SetStrTable)->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=2")->order('id desc,uptime desc')->limit(0,8)->select();
            $tuijian1_name="简历帮助";
            $tuijian1_url="/post/zhaopin/"."jianlibangzhu/";
            $tuijina2=Db::connect($this->data_info)->table($SetStrTable)->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=3")->order('id desc,uptime desc')->limit(0,8)->select();
            $tuijian2_name="面试技巧";
            $tuijian2_url="/post/zhaopin/"."mianshijiqiao/";
        }elseif ($kind==2){
            $tuijina1=Db::connect($this->data_info)->table($SetStrTable)->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=1")->order('id desc,uptime desc')->limit(0,8)->select();
            $tuijian1_name="职场资讯";
            $tuijian1_url="/post/zhaopin/"."zhichangzixun/";
            $tuijina2=Db::connect($this->data_info)->table($SetStrTable)->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=3")->order('id desc,uptime desc')->limit(0,8)->select();
            $tuijian2_name="面试技巧";
            $tuijian2_url="/post/zhaopin/"."mianshijiqiao/";
        }elseif ($kind==3){
            $tuijina1=Db::connect($this->data_info)->table($SetStrTable)->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=1")->order('id desc,uptime desc')->limit(0,8)->select();
            $tuijian1_name="职场资讯";
            $tuijian1_url="/post/zhaopin/"."zhichangzixun/";
            $tuijina2=Db::connect($this->data_info)->table($SetStrTable)->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=2")->order('id desc,uptime desc')->limit(0,8)->select();
            $tuijian2_name="简历帮助";
            $tuijian2_url="/post/zhaopin/"."jianlibangzhu/";
        }
        $this->assign('tuijian1',$tuijina1);
        $this->assign('tuijian1_name',$tuijian1_name);
        $this->assign('tuijian1_url',$tuijian1_url);
        $this->assign('tuijian2',$tuijina2);
        $this->assign('tuijian2_name',$tuijian2_name);
        $this->assign('tuijian2_url',$tuijian2_url);

        if ($count>0){
            $totalPage=ceil($count/$pageNum);
        }else{
            $totalPage=0;
        }

        $Page  = new Page($count,$pageNum);
        $show = $Page->show(1,$kindname);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->assign('totalpage',$totalPage);
        $this->assign('list',$zixun_list);
        $this->assign('url_var',$url_var);
        $this->assign('pager', $show);

        //获取广告
        $advRight =GetDivBrandInfoList((int)$this->site_id, '1980', 0, 0);
        $this->assign('advRight',$advRight);


        $title=$kindname.'_'.$this->areaName.'人才网-'.$this->siteNane;
        $description="职场资讯栏目为你提供".$this->areaName.'职场资讯信息，以及简历帮助，面试技巧等信息，为你的求职招聘之路提供帮助。';
        $this->assign('title',$title);
        $this->assign('description',$description);
        $this->assign('has_weizhi',1);
        $weizhi=$kindname;
        $this->assign('weizhi',$weizhi);
        $this->assign('has_shaixuan',0);
        $this->assign('is_zixun',1);
        $this->assign('url_var',$url_var);
        $this->assign('kind',$kind);
        $this->assign('kindname',$kindname);
        $this->assign('active',5);
        return $this->fetch();
    }

    //咨询详情页
    public function info(){
        $class1=1;
        $kind=input('kind',1);

        if ($kind==1){
            $kindname="职场资讯";
            $kindvalue="/post/zhaopin/"."zhichangzixun/";
        }elseif ($kind==2){
            $kindname="简历帮助";
            $kindvalue="/post/zhaopin/"."jianlibangzhu/";
        }else{
            $kindname="面试技巧";
            $kindvalue="/post/zhaopin/"."mianshijiqiao/";
        }
        $id=intval(trim(input('id')));
        $rs=\db('site_news',$this->data_info)
            ->field('title,memo,comf,uptime,purl,hit,ding,cai')
            ->where('siteid='.$this->site_id .' and id='.$id)
            ->find();
        if (!$rs){
            $this->error("未找到您选择的信息！");
        }
        $memo=str_replace('/r/n','<br />',$rs['memo']);
        $hit=$rs['hit']+1;
        $conn=$this->get_write_info();
        $db=Db::connect($conn);
        $res=$db->table('site_news')->where('siteid='.$this->site_id .' and id='.$id)->update(['hit'=>$hit,'memo'=>$memo]);

        //dump($rs);

        //获取上一篇和下一篇
        $db=Db::connect($this->data_info);
        $prev_info=$db->table('site_news')->where('siteid='.$this->site_id .' and id <'.$id)->field('id,title')->order('id desc')->find();
        $next_info=$db->table('site_news')->where('siteid='.$this->site_id .' and id >'.$id)->field('id,title')->order('id desc')->find();
        $this->assign('prev_info',$prev_info);
        $this->assign('next_info',$next_info);

        //获取相关阅读
        $db=Db::connect($this->data_info);
        $xiangguan_list=$db->table('site_news')
            ->where('siteid='.$this->site_id .'and kind ='.$kind.' and id <>'.$id)
            ->field('id,title,uptime')
            ->order('uptime desc,id desc')
            ->limit(0,4)
            ->select();
        $this->assign('xiangguan_list',$xiangguan_list);

        //获取右侧推荐信息
        $tuijina1=Db::connect($this->data_info)->table('site_news')->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=1")->order('id desc,uptime desc')->limit(0,8)->select();
        $tuijian1_name="职场资讯";
        $tuijian1_url="/post/zhaopin/"."zhichangzixun/";
        $tuijina2=Db::connect($this->data_info)->table('site_news')->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=2")->order('id desc,uptime desc')->limit(0,8)->select();
        $tuijian2_name="简历帮助";
        $tuijian2_url="/post/zhaopin/"."jianlibangzhu/";
        $tuijina3=Db::connect($this->data_info)->table('site_news')->field("id,title,uptime")->where("siteid=".$this->site_id." and kind=3")->order('id desc,uptime desc')->limit(0,8)->select();
        $tuijian3_name="面试技巧";
        $tuijian3_url="/post/zhaopin/"."mianshijiqiao/";

        $this->assign('tuijian1',$tuijina1);
        $this->assign('tuijian1_name',$tuijian1_name);
        $this->assign('tuijian1_url',$tuijian1_url);
        $this->assign('tuijian2',$tuijina2);
        $this->assign('tuijian2_name',$tuijian2_name);
        $this->assign('tuijian2_url',$tuijian2_url);
        $this->assign('tuijian3',$tuijina3);
        $this->assign('tuijian3_name',$tuijian3_name);
        $this->assign('tuijian3_url',$tuijian3_url);

        //获取广告
        $advRight =GetDivBrandInfoList((int)$this->site_id, '1811', 0, 0);
        $this->assign('advRight',$advRight);

        $this->assign('info',$rs);
        $this->assign('kindname',$kindname);
        $this->assign('kindvalue',$kindvalue);
        $this->assign('has_weizhi',1);
        $this->assign('kind',$kind);
        $this->assign('id',$id);
        $weizhi="<a href=".$kindvalue.">文章列表</a> &gt;&gt; 文章详细内容";
        $this->assign('weizhi',$weizhi);
        $this->assign('active',5);
        $this->assign('is_zixun',1);
        $this->assign('has_shaixuan',0);
        return $this->fetch();
    }


    //点赞与踩赞
    public function dingcai()
    {
        $action=trim(input('action'));
        $id=intval(trim(input('id')));
        $vtype=intval(trim(input('type')));
        if ($action=='postviews'){
            //获取cookie
            $ip=getIP();
            $oldIP=cookie('ip');
            $oldTime=cookie('addTime');
            $addTime=now();
            if ($oldTime){
                if(datediff('d',$addTime,$oldTime)==0&&$ip==$oldIP){
                    //表示同一天并是同一个ip操作
                    return 'no';
                }
            }
            setcookie('ip',$ip, time() + 3600, '/');
            setcookie('addTime',$addTime, time() + 3600, '/');
            //执行
            if ($vtype==0){
                //顶
                $srs=Db::connect($this->data_info)->table('site_news')->where('siteid='.$this->site_id.' and id='.$id)->value('ding');
                if (isset($srs)){
                    //Db::connect($this->get_write_info())->table('site_news')->where('siteid='.$this->site_id.' and id='.$id)->data(['ding'=>$srs+1])->update();
                    \db('site_news',$this->get_write_info())->execute("update site_news set ding=ding+1 where siteid=$this->site_id and id=$id");
                    return $srs+1;
                }
            }else{
                //踩
                $srs=Db::connect($this->data_info)->table('site_news')->where('siteid='.$this->site_id.' and id='.$id)->value('cai');
                if (isset($srs)){
                    \db('site_news',$this->get_write_info())->execute("update site_news set cai=cai+1 where siteid=$this->site_id and id=$id");
                    return $srs+1;
                }
            }
            return "no2";
        }
    }

    public function ajax_pinglun()
    {
        $action=input('action');
        $sid=input('sid');
        switch ($action){
            case "showList":
                $this->showList();
                break;
            case "savepinglun":
                 $this->savepinglun();
                break;
            case "plDing":
                $ssid=input('ssid');
                $webHost=$_SERVER['HTTP_HOST']."_plviews_".$ssid;
                $ip=getIP();
                $oldip=cookie('ip');
                $oldtime=cookie('addTime');
                $addtime=now();
                $phit=0;
                if (is_date($oldtime)==0){
                    setcookie('ip',$ip,time()+3600);
                    setcookie('addTime',$addtime,time()+3600);
                    $db=Db::connect($this->data_info);
                    $srs=$db->table('site_news_pinglun')->where("siteid=".$this->site_id." and id=".$ssid)->field('ding')->find();
                    if ($srs){
                        $phit=$srs['ding']+1;
                        $db=Db::connect($this->get_write_info());
                        //$res=\db('site_news_pinglun',$this->get_write_info())->execute("update site_news_pinglun set ding='".$phit."' where siteid=".$this->site_id." and id=".$ssid);
                        $res=$db->table('site_news_pinglun')->where("siteid=".$this->site_id." and id=".$ssid)->update(['ding'=>'1']);
                    }
                }else{
                    //$oldip="192.168.0.11";
                    if (datediff('d',$addtime,$oldtime)==0&&$ip==$oldip){
                        $phit="no";
                    }else{
                        setcookie('ip',$ip,time()+3600);
                        setcookie('addTime',$addtime,time()+3600);
                        $db=Db::connect($this->data_info);
                        $srs=$db->table('site_news_pinglun')->where("siteid=".$this->site_id." and id=".$ssid)->field('ding')->find();
                        if ($srs){
                            $phit=$srs['ding']+1;
                            $db=Db::connect($this->get_write_info());
                            //$res=$db->table('site_news_pinglun')->where("siteid=".$this->site_id." and id=".$ssid)->update(['ding'=>'1']);
                            $res=\db('site_news_pinglun',$this->get_write_info())->execute("update site_news_pinglun set ding='".$phit."' where siteid=".$this->site_id." and id=".$ssid);

                        }

                    }
                }
                return $phit;
                break;
        }
        
    }

    public function savepinglun()
    {
        $sid=input('sid');
        if (session('username')!=''){
            if ($this->checkuser(session('username'))){
                return 2;
            }
        }else{
            return "请登录后发表评论";
        }
        $plcontent= request()->param('plcontent');
        $reid=request()->param('reid');
        $ip=getIP();
        $sql="select top 1 id from site_news_pinglun where siteId = ".$this->site_id." And classid=".$sid." and cast(Content as varchar(2000))='".$plcontent."' Order By Id Desc";
        $ckrs=\db('site_news_pinglun',$this->data_info)->query($sql);
        if ($ckrs){
            $ckrs=$ckrs[0];
            if (datediff('i',$ckrs['addtime'],now())<=5&&$ip==$ckrs['IP']){
                return 'no';
            }
        }
        unset($ckrs);
        \db('site_news_pinglun',$this->get_write_info())
            ->insert(['siteid'=>$this->site_id, 'classid'=>$sid, 'UserName'=>session('username'), 'Content'=>$plcontent, 'IP'=>$ip, 'reid'=>$reid]);
        \db('site_news',$this->get_write_info())->where('siteid='.$this->site_id.' and id='.$sid)->update(['postNum'=> ['exp','postNum+1']]);
        return 1;
    }


    public function showList()
    {
        $action=input('action');
        $pagenum=5;
        $page=input('page',1);
        $sid=input('sid');
        $pinglun_num=\db('site_news_pinglun',$this->get_write_info())
            ->where("siteId = ".$this->site_id." and reid=0 And classid = ".$sid)
            ->count();
        if ($pinglun_num==0){
            return "<p>暂无相关评论！</p>";
        }else{
            //获取资讯评论，即reid为0
            $pinglun_list=\db('site_news_pinglun',$this->get_write_info())
                ->where("siteId = ".$this->site_id." and reid=0 And classid = ".$sid)
                ->field("id,UserName,AddTime,Content,ding")
                ->order("id Desc")
                ->limit(0,$pagenum*$page)
                ->select();

            //获取用户信息
            $u_arr=get_arr_column($pinglun_list,'UserName');
            $u_str=implode(',',$u_arr);
            $uinfo=\db('users','user')->where('username','in',($u_str))->field('username,nick,userface')->select();

            //获取回复帖子id
            $hfid_arr=get_arr_column($pinglun_list,'id');
            if ($hfid_arr){
                $hfid_str=implode(',',$hfid_arr);
                $hfinfo=\db('site_news_pinglun',$this->get_write_info())
                    ->where("siteId = ".$this->site_id." and reid in (".$hfid_str.") And classid = ".$sid)
                    ->field("id,UserName,AddTime,Content,ding,reid")
                    ->order("id Desc")
                    ->select();
                if ($hfinfo){
                    $u_arr=get_arr_column($pinglun_list,'UserName');
                    $u_str=implode(',',$u_arr);
                    $uinfo2=\db('users','user')->where('username','in',($u_str))->field('username,nick,userface')->select();
                    foreach ($hfinfo as &$val){
                        if (is_null($val['UserName'])||$val['UserName']==""||$val['UserName']=="匿名"){
                            $val['UserName']="匿名用户";
                        }
                        foreach ($uinfo2 as $value){
                            if ($val['UserName']==$value['username']){
                                $val['nick']=$value['nick'];
                                $val['userface']=$value['userface'];
                            }
                        }
                    }

                }
            }

            foreach ($pinglun_list as $k=>&$val){
                   if (is_null($val['UserName'])||$val['UserName']==""||$val['UserName']=="匿名"){
                       $val['UserName']="匿名用户";
                   }
                   foreach ($uinfo as $v){
                       if ($val['UserName']==$v['username']){
                           $val['nick']=$v['nick'];
                           $val['userface']=$v['userface'];
                       }
                   }
                   if ($hfinfo){
                       foreach ($hfinfo as $v2){
                          if ($val['id']==$v2['reid']){
                              $val['hfinfo'][]=$v2;
                          }
                       }
                   }else{
                       $pinglun_list[$k]['hfinfo']=[];
                   }
           }

           if ($pinglun_num>$page*$pagenum){
               $this->assign('has_more',1);
           }else{
               $this->assign('has_more',0);
           }
            $this->assign('page',$page);
            $this->assign('pinglun_list',$pinglun_list);
           $this->assign('sid',$sid);
           return $this->fetch();
        }
    }


    function checkuser($strchkuser){
        $db=Db::connect($this->data_info);
        $uinfo=$db->table('CheckTel')->where("Tel='".$strchkuser."'")->field('id')->find();
        if ($uinfo){
            return true;
        }else{
            return false;
        }
        unset($uinfo);
    }

}