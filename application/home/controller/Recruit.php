<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/9/17/017
 * Time: 16:06
 */

namespace app\home\controller;

use think\Db;
use think\Exception;
use think\facade\Request;
use think\Page;
use app\home\logic\RecruitLogic;
use think\facade\Env;

class Recruit extends Base
{
    use \app\home\traits\HeaderFooter;
    /**
     * 兼职招聘列表
     * @access public
     */
    public function partList()
    {
        $siteid = $this->site_id;
        $strwhere = " j.siteid = $siteid and j.ccoochk = 1 and j.isdel = 0 and (q.isdel <> 1  OR q.isdel IS NULL)";
        $pageType = 1;
        $show_kind = 0;
        $show_area_id = 0;
        $listRows = 20;
        if(Request::post('key')){
            $keyword = Request::post('key');
            $strwhere .= " And ( j.title like '%$keyword%'  or j.compname like '%$keyword%') ";
        }else if(Request::param('kind_id') && Request::param('area_id')){
            $show_kind = Request::param('kind_id');
            $show_area_id = Request::param('area_id');
            $strwhere .= " And j.kind =$show_kind And j.areaId =$show_area_id";
            $pageType = 2;
            $listRows = 50;
        }else if(Request::param('kind_id')){
            $kind = Request::param('kind_id');
            $strwhere .= " And j.kind =$kind";
            $show_kind = $kind;
            $pageType = 2;
            $listRows = 50;
        }else if(Request::param('area_id')){
            //dump(Request::param());exit;
            $area = Request::param('area_id');
            $strwhere .= " And j.areaId =$area";
            $show_area_id = $area;
            $pageType = 2;
            $listRows = 50;
        }
        //封装列表联合筛选
        $sData['kind'] = $show_kind;
        $sData['area'] = $show_area_id;
        $db = Db::connect($this->data_info);
        $result = $db->query("select j.title,j.info,j.salary,j.salarytype,j.paytype,j.edittime,j.number,j.workarea,q.compname,q.compaddr,k.kindname,(select top 1 id from job_qiyeku qq where qq.siteid = $siteid and qq.ismq=1 and qq.username=j.username and qq.CmdTime>getdate()) as ismq,(select top 1 id from postWhite w where w.siteid=$siteid and w.tabCls=0 and w.ccooUser=j.username and w.expTime>getdate()) as isrz from job_JZ_info j left join job_qiyeku q on j.siteid = q.siteid and j.username = q.username left join job_JZ_kind k on j.kind = k.id where $strwhere order by j.IsCmd Desc, j.edittime Desc,j.id Desc");
        //dump($result);exit;
        $Page  = new Page(count($result), $listRows, $sData);

        $show = $Page->show($pageType, 'jianzhi');

        $res = $db->query("select j.id,j.title,j.info,j.salary,j.salarytype,j.paytype,j.edittime,j.number,j.workarea,q.compname,q.compaddr,k.kindname,(select top 1 id from job_qiyeku qq where qq.siteid = $siteid and qq.ismq=1 and qq.username=j.username and qq.CmdTime>getdate()) as ismq,(select top 1 id from postWhite w where w.siteid=$siteid and w.tabCls=0 and w.ccooUser=j.username and w.expTime>getdate()) as isrz from job_JZ_info j left join job_qiyeku q on j.siteid = q.siteid and j.username = q.username left join job_JZ_kind k on j.kind = k.id where $strwhere order by j.IsCmd Desc, j.edittime Desc,j.id Desc offset $Page->firstRow rows fetch next $Page->listRows rows only");
        //dump($db->getLastSql());exit;
        //获取职位数据
        if(cache('jianzhi_job_data_list')){
            $jobData = cache('jianzhi_job_data_list');
        }else{
            $jobData = $this->getDataKindName();
            cache('jianzhi_job_data_list', $jobData, 900);
        }
        //获取区域数据
        if(cache('jianzhi_area_data_list')){
            $areaData = cache('jianzhi_area_data_list');
        }else{
            $areaData = $this->getDataAreaName();
            cache('jianzhi_area_data_list', $areaData, 900);
        }

        //底部广告
        $advShow = false;
        if(Request::param('kind_id') || Request::param('area_id')){
            $advShow = GetDivBrandInfoList((int)$this->site_id, '1937', 0, 0, 1);
        }
        $url = Request::url(true);

        $urlData['url_zhis'] = '/post/jianzhis/';
        $urlData['url_zhi'] = '/post/jianzhi/';
        //账号投递的职位
        $tdstr = $this->get_user_deliver_record();

        foreach ($res as  $key=>&$val) {
            $val['show_td'] = get_td($tdstr, $val['id']);
        }

        $this->assign('tdstr', $tdstr);
        $this->assign('url', $url);
        $this->assign('show_kind', $show_kind);
        $this->assign('show_area_id', $show_area_id);
        $this->assign('jobData', $jobData);
        $this->assign('urlData', $urlData);
        $this->assign('areaData', $areaData);
        $this->assign('advShow', $advShow);
        $this->assign('areaName', $this->areaName);
        $this->assign('siteName', $this->siteName);
        $this->assign('nowPage', $Page->nowPage);
        $this->assign('list', $res);
        $this->assign('page', $show);
        unset($res);
        unset($show);
        return $this->fetch('partList');
    }
    /**
     * 兼职招聘职位数据
     * @access public
     */
    public function getDataKindName()
    {
        $db = Db::connect($this->data_info);
        $result = $db->query("Select id,kindname From job_jz_kind where siteid = $this->site_id And Pid = 0 order by viewlev Asc,id Desc");
        return $result;
    }
    /**
     * 兼职招聘区域数据
     * @access public
     */
    public function getDataAreaName()
    {
        $db = Db::connect($this->data_info);
        $result = $db->query("Select id,zonename From house_zone where Siteid = $this->site_id  Order By view_lev asc,id asc");
        return $result;
    }
    /**
     * 获取当前用户投递的简历
     */
    public function get_user_deliver_record()
    {
        if(cookie('postjztd'.session('username')) == ''){
            if($this->is_login == 1 && session('username')){
                $db = Db::connect($this->data_info);
                $result = $db->query("select classid from job_jz_user where siteid = $this->site_id and username = '".session('username')."'  and addtime>getdate()-1");
                $tdstr='|';
                if(count($result) > 0){
                    foreach($result as $key=>$val){
                        $tdstr .= $val['classid'].'|';
                    }
                }
                cookie('postjztd'.session('username'), $tdstr);
            }else{
                $tdstr = '';
            }
        }else{
            $tdstr = cookie('postjztd'.session('username'));
        }
        return $tdstr;
    }
    /**
     * 兼职招聘详情页
     */
    public function partShow()
    {
        if(!Request::param('id')){
            return json('非法参数');
        }
        //$action = $action = Request::controller().'_'.Request::action().'_';
        $result = (new RecruitLogic())->getPartCreate($this->getParams(), $this->is_login, $this->site_id);
        if(!is_array($result)){
            return json($result, 404);
            //return json($result);
        }
        $result['class1'] = 1;
        $result['IndexWinUrl'] = Request::url(true);
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        if ($result['vo']['tel'] !='' &&  strstr($result['vo']['tel'],'*') ){
            $result['vo']['tel_status']=1;//不完整显示
        }else{
            $result['vo']['tel_status']=2;//显示完整的
        }
        $result['vo']['job_id']=Request::param('id');//职位id
        $tupian=(new Setpng())->set($result['vo']['tel']);//base64加密
        $result['vo']['tel_png']="data:image/png;base64,".$tupian;
        $this->getHeaderFooter();
        return $this->fetch('partShowNew', $result);
    }
    /**
     * 公司兼职其他职位数据
     * @access public
     */
    public function partOthersList()
    {
        if(!Request::param('id')){
            return json('非法参数');
        }
        $result = (new RecruitLogic())->partOthersList($this->getParams(), $this->site_id);
        return $result;
    }
    /**
     * 全职招聘详情页
     */
    public function fullShow()
    {
        if(!Request::param('id')){
            return json('非法参数');
        }
        //$action = $action = Request::controller().'_'.Request::action().'_';
        $result = (new RecruitLogic())->getfullCreate($this->getParams(), $this->is_login, $this->site_id);
        if(!is_array($result)){
            return json($result, 404);
            //return json($result);
        }
        $result['class1'] = 1;
        $result['IndexWinUrl'] = Request::url(true);
        $result['codeWinUrl'] = str_replace('http://www.', 'http://m.', Request::domain());
        if ($result['vo']['tel'] !='' &&  strstr($result['vo']['tel'],'*') ){
            $result['vo']['tel_status']=1;//不完整显示
        }else{
            $result['vo']['tel_status']=2;//显示完整的
        }
        $result['vo']['job_id']=Request::param('id');//职位id
        $tupian=(new Setpng())->set($result['vo']['tel']);//base64加密
        $result['vo']['tel_png']="data:image/png;base64,".$tupian;
        $this->getHeaderFooter();
        return $this->fetch('fullShow', $result);
    }
    /**
     * 公司全职其他职位数据
     * @access public
     */
    public function fullOthersList()
    {
        if(!Request::param('id')){
            return json('非法参数');
        }
        $result = (new RecruitLogic())->fullOthersList($this->getParams(), $this->site_id);
        return $result;
    }
    /**
     * 职位申请
     * @access public
     */
    public function applicationPosition()
    {
        if(!Request::param('id')){
            $data['message'] = '非法参数';
            return json($data);
        }
        $result = (new RecruitLogic())->applicationPosition($this->getParams(), $this->is_login, $this->site_id);
        return $result;
    }
    /**
     * 职位申请
     * @access public
     */
    public function createConfig()
    {
        $result = red_web_config();
        $str = var_export($result,TRUE);

        $fileName = Env::get('config_path').'webConfig.php';
        file_put_contents($fileName,"<?php
        return ");
        file_put_contents($fileName, $str, FILE_APPEND);
        $result = file_put_contents($fileName, ';', FILE_APPEND);
        if($result != false){
            echo '写入成功';
        }
    }
    /**
     * 招聘首页
     * @access public
     */
    public function homepage()
    {
        $info=(new RecruitLogic())->getindexinfo($this->site_id);

        //获取广告信息(招聘首页顶部广告id为2058)
        $adtopinfo=(new RecruitLogic())->getAdinfo($this->site_id,2058);
        $this->assign('adtopinfo',$adtopinfo);
        if ($this->is_login==1){
            $userdata=(new RecruitLogic())->getuserinfo($this->site_id,$this->username);
            $userdata=$userdata['ServerInfo'];
            $this->assign('userdata',$userdata);
            $this->assign('is_login',1);
        }else{
            $this->assign('is_login',0);
        }
        if(!is_array($info)){
            return json($info, 404);
            //return json($result);
        }
        if (!empty($info)){
            $info=$info['ServerInfo'];
            $KindList=$info['KindList'];
            $no_child=1; //没有2级分类
            $FirstKindList=[];//筛选出所有一级分类
            if ($KindList){
                foreach ($KindList as $v){
                    if ($v['Pid']==0){
                        $FirstKindList[]=$v;
                    }
                    if ($v['Pid']>0){
                        $no_child=0;
                    }
                }
            }
            $this->assign('no_child',$no_child);
            $codeWinUrl = str_replace('http://www.', 'http://m.', Request::domain());
            $KindHotList=$info['KindHotList']; //热门职位
            if ($FirstKindList){
                foreach($FirstKindList as &$val){
                    foreach ($KindList as $value){
                        if ($val['id']==$value['Pid']){
                            $val['Seckindlist'][]=$value;
                        }
                    }
                }
            }
            $BannerList=$info['BannerList'];//轮播图列表
            $QiYeKuList=$info['QiYeKuList'];//名企列表
            $RecomJobList=$info['RecomJobList'];//招聘推荐列表
            //职位福利转化成数组
            if($RecomJobList){
                foreach ($RecomJobList as &$v){
                    if ($v['fuliName']){
                        $v['fuliNameArr']=explode(',',$v['fuliName']);
                    }
                }
            }
            $JobList=$info['JobList'];//招聘信息列表
            $JobNum=$info['JobNum']; //招聘数量
            if($JobList){
                foreach ($JobList as &$v){
                    if ($v['fuliName']){
                        $v['fuliNameArr']=explode(',',$v['fuliName']);
                    }
                }
            }

            //获取电话和qq
            $severData = getPublicTel($this->site_id, 0);
            $this->assign('service_comptel',$severData['ServicesTel']);
            $this->assign('service_qq',$severData['ServicesQQ']);
            $BigJLList=$info['BigJLList']; //大图简历列表
            $JLNum=$info['JLNum'];//简历数量
            $JLList=$info['JLList'];//简历列表
            $NewsList=$info['NewsList'];//资讯列表
            $LinkList=$info['LinkList'];//友情链接
            $title=$info['JobTitle']?$info['JobTitle']:'';
            $keyword=$info['JobKey']?$info['JobKey']:'';
            if ($keyword){
                $keyword=str_replace('，',',',$keyword);
            }
            $description=$info['JobDesc']?$info['JobDesc']:'';
            if ($description){
                $description=str_replace('，',',',$description);
            }
            if (!$this->site_id){
                LogRecord('未获取到站点信息首页', request()->url(true), 'app/home/controller/recruit', 'PC移植PHP');
            }
            $this->getHeaderFooter();
            $this->assign('codeWinUrl',$codeWinUrl);
            $this->assign('title',$title);
            $this->assign('keyword',$keyword);
            $this->assign('description',$description);
            $this->assign('BannerList',$BannerList);//轮播图列表
            $this->assign('firstkindlist',$FirstKindList);//左侧分类导航列表
            $this->assign('QiYeKuList',$QiYeKuList);//名企列表
            $this->assign('RecomJobList',$RecomJobList);//招聘推荐列表
            $this->assign('JobList',$JobList);//招聘信息列表
            $this->assign('JobNum',$JobNum); //招聘数量
            $this->assign('BigJLList',$BigJLList);//大图简历列表
            $this->assign('JLNum',$JLNum);//简历数量
            $this->assign('JLList',$JLList);//简历列表
            $this->assign('NewsList',$NewsList);//资讯列表
            $this->assign('LinkList',$LinkList);//友情链接
            $this->assign('KindHotList',$KindHotList);//热门职位
        }else{
            echo '数据加载错误';
        }
        //dump($info);die;
        $this->assign('class1',1);
        return view('homepage');
    }

    /**
     * 获取职位电话号码
     * @return mixed|\think\response\Json
     *
     */
    public function getPositionTel(){

        if(!Request::param('id')){
            return json('非法参数');
        }
        $jobId= input('id');
        $siteId=$this->site_id;
        $oType=input('otype');
        $username=$this->username;
        $result=getPositionTel($siteId,$jobId,$oType,$username);
        return $result;
    }

    /**
     * 生成电话号码图片
     * @return \think\response\Json
     */
    public function telimg(){
        if(!Request::param('tel')){
            return json('非法参数');
        }
        $tupian=(new Setpng())->set(input('tel'));//base64加密
        $result['tel_png']="data:image/png;base64,".$tupian;
        $result['code']=200;
        return $result;
    }

}