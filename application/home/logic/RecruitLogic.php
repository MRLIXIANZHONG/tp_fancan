<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/12/02/05
 * Time: 13:55
 */

namespace app\home\logic;

use app\home\model\JobJlTable;

class RecruitLogic extends Logic
{
    /**
     * 兼职招聘详情页
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getPartCreate(array $params, int $is_login, $siteId)
    {
        $id = $this->getParam($params, 'id', 0, 'int');
        $notCache = $this->getParam($params, 'notCache', 0, 'int');
        $username = '';
        if($is_login == 1 && session('username')){
            $username = session('username');
        }
        $result = $this->getPartListData($siteId, $id, $username, $notCache);
        if(count($result) > 0){
            //内容页左下广告
            $advRes = GetDivBrandInfoList($siteId, '1808', 0, 0);
            $infoData = $result['JobInfo'];
            if($infoData['HitType'] == 1){
                $showHit = IsStar($infoData['hit']);
            }else{
                $showHit = $infoData['hit'];//tp 解析em标签会多一个
            }
            //管理连接
            $ldata = $this->get_manage_link($infoData['username'], $infoData['isVipMark'], $infoData['id'], $is_login);
            //处理职位描述
            if($infoData['info']){
                $infoData['info'] = strRepeat(['<div style="top:0px;">﻿﻿</div>','<divstyle="top:0px;">﻿﻿</div>'], '', $infoData['info']);
            }
            //处理类型
            $infoData['salarytype'] = get_salary($infoData['salarytype']);
            $infoData['paytype'] = get_payment($infoData['paytype']);
            //图集
            $picList = get_company_img($infoData['uppic']);
            //企业头像
            $conData['isrz'] = $infoData['isrz'];
            $conData['ismq'] = $infoData['ismq'];
            //获取默认头像
            $tempImg = get_company_portrait($conData);
            //处理后台账号发布
            if(!isset($infoData['TempLogo'])){
                $infoData['TempLogo'] = '';
            }
            if($infoData['TempLogo']){
                $tempImg = get_s_img($infoData['TempLogo'], '150x150(s)');
            }
            $infoData['TempLogo'] = $tempImg;
            //营业执照
            $infoData['licenseData']=null;
            if($infoData['license']){
                $infoData['licenseData']['img'] = get_s_img($infoData['license'], '500x300(w_3)');
                $infoData['licenseData']['long'] = get_s_img($infoData['license'], '1X1(w_3)');
            }
            //推荐数据
            $recom_adv = null;
            if($is_login == 1 && $infoData['username'] == session('username') && $infoData['istj'] == 0){
                $recom_adv =  getRecommendData($siteId, $id, 2);
                if(count($recom_adv) > 0){
                    $recom_adv = $this->handelRecommend($recom_adv);
                }
            }
            //热门职位数据处理
            if(count($result['HotList']) > 0){
                foreach ($result['HotList'] as  $hk=> &$hv) {
                    $hv['salarytype'] = get_salary($hv['salarytype']);
                }

            }
            //获取电话和qq
            $severData = getPublicTel($siteId, 0);
            $data['service_comptel'] = $severData['ServicesTel'];
            $data['service_qq'] = $severData['ServicesQQ'];
            $data['recom_adv'] = $recom_adv;
            $data['advRes'] = $advRes;
            $data['vo'] = $infoData;
            $data['OtherList'] = $result['OtherList'];
            $data['SimilarList'] = $result['SimilarList'];
            $data['HotList'] = $result['HotList'];
            $data['siteId'] = $siteId;
            $data['is_login'] = $is_login;
            $data['session_username'] = $is_login == 1? session('username') : null;
            $data['showHit'] = $showHit;
            $data['picList'] = $picList;
            $data['num'] = $result['IsNext'];
            $data['ldata']     = $ldata;

            return $data;
        }
        return '信息不存在';

    }
    /**
     * 兼职公司其他职位数据
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function partOthersList(array $params, $siteId)
    {
        $id = $this->getParam($params, 'id', 0, 'int');
        $p = $this->getParam($params, 'page', 0, 'int');
        $result = $this->getPartOtherPageData($siteId, $id, $p);
        //dump($result);exit;
        return $this->get_job_table($result);
    }
    /**
     * 兼职招聘获取管理连接
     * @param data $result 查询数据
     */

    public function get_manage_link($username, $isVipMark, $id, $is_login)
    {
        if($username && !$isVipMark){
            $data['strDelUrl'] = "/post/del/index_u.asp?oAction=jianzhi&isPage=12&id=$id";
            if(session('username') && $is_login == 1){
                if(session('username') == $username){
                    $data['strEditUrl'] = '<a class="item" href="/post/users/job/jz_edit.asp?id='.$id.'" target="_blank">修改</a>';
                }else{
                    $data['strEditUrl'] = "/post/ajax/post_user.asp?stype=14&id=$id&urllink=/post/users/job/jz_edit.asp";

                }
            }else{
                $data['strEditUrl'] = "/post/ajax/post_user.asp?stype=14&id=$id&urllink=/post/users/job/jz_edit.asp";;
            }
        }else{
            $data['strEditUrl'] = '';
            $data['strDelUrl'] = "/post/del/index.asp?oAction=jianzhi&isPage=12&id=$id";
        }
        return $data;
    }
    /**
     * 全职招聘获取管理连接
     * @param data $result 查询数据
     */

    public function get_full_manage_link($username, $isVipMark, $id, $is_login)
    {
        if($username && !$isVipMark){
            $data['strDelUrl'] = "/post/del/index_u.asp?oAction=zhaopin&isPage=1&id=$id";
            if(session('username') && $is_login == 1){
                if(session('username') == $username){
                    $data['strEditUrl'] = '<a class="item" href="/post/users/job/zp_edit.asp?id='.$id.'" target="_blank">修改</a>';
                }else{
                    $data['strEditUrl'] = "/post/ajax/post_user.asp?stype=14&id=$id&urllink=/post/users/job/zp_edit.asp";

                }
            }else{
                $data['strEditUrl'] = "/post/ajax/post_user.asp?stype=14&id=$id&urllink=/post/users/job/zp_edit.asp";;
            }
        }else{
            $data['strEditUrl']='';
            $data['strDelUrl'] = "/post/ del/index.asp?oAction=zhaopin&isPage=1&id=$id";
        }
        return $data;
    }
    /**
     * 生成兼职公司其他职位
     * @param data $result 查询数据
     */
    public function get_job_table($result)
    {
        if(count($result) == 0){
            return '';
        }
        $str = '';
        foreach ($result['OtherList'] as  $k=> $v) {
            $v['edittime'] = date('m-d', strtotime($v['edittime']));
            if($v['salary'] > 0){
                $money = $v['salary'].'元';
            }else{
                $money = '面议';
            }
            $compname = $v['compname'];
            $url = '/post/jianzhi/'.$v['id'].'x.html';
            $str .= '<li class="item fl">';
            $str .= '<a href="'.$url.'" class="link">';
            $str .= '<div class="titBox clearfix">';
            $str .= '<span class="price fr">'.$money.'</span>';
            $str .= '<p class="tit">'.$v['title'].'</p>';
            $str .= '</div>';

            $str .= '<div class="infoBox clearfix">';
            $str .= '<span class="date fr">'.$v['edittime'].'</span>';
            $str .= '<p class="info">'.$compname.'</p>';
            $str .= '</div>';

            $str .= '</a>';
            $str .= '</li>';
        }
        if($result['IsNext']['OtherListIsNext']==1)
        {
            $str .= '<div id="isNext"></div>';
        }
        return $str;
    }
    /**
     * 全职招聘详情页
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function getFullCreate(array $params, int $is_login, $siteId)
    {
        $id = $this->getParam($params, 'id', 0, 'int');
        $username = '';
        $notCache = $this->getParam($params, 'notCache', 0, 'int');
        if($is_login == 1 && session('username')){
            $username = session('username');
        }
        $result = $this->getFullListData($siteId, $id, $username, $notCache);

        if(count($result) > 0){
            //内容页左下广告
            $advRes = GetDivBrandInfoList($siteId, '1808', 0, 0);
            $infoData = $result['JobInfo'];
            if($infoData['HitType'] == 1){
                $showHit = IsStar($infoData['hit']);
            }else{
                $showHit = $infoData['hit'];//tp 解析em标签会多一个
            }
            //管理连接
            $ldata = $this->get_full_manage_link($infoData['username'], $infoData['isVipMark'], $infoData['id'], $is_login);
            //处理标题
            $infoData['salaryName'] = strRepeat(['月','元','以上','/','以下'], '', $infoData['salaryName']);
            //处理职位描述
            if($infoData['info']){
                $infoData['info'] = strRepeat(['<div style="top:0px;">﻿﻿</div>','<divstyle="top:0px;">﻿﻿</div>'], '', $infoData['info']);
            }
            //福利
            $infoData['Welfare'] = $this->getWelfare($infoData['Welfare']);
            //图集
            $picList = get_company_img($infoData['UpPic']);
            //企业头像
            $conData['isrz'] = $infoData['isrz'];
            $conData['ismq'] = $infoData['ismq'];
            //获取默认头像
            $tempImg = get_company_portrait($conData);
            //处理后台账号发布
            if(!isset($infoData['TempLogo'])){
                $infoData['TempLogo'] = '';
            }
            if($infoData['TempLogo']){
                $tempImg = get_s_img($infoData['TempLogo'], '150x150(s)');
            }
            $infoData['TempLogo'] = $tempImg;
            //营业执照
            $infoData['licenseData']=null;
            if($infoData['license']){
                $infoData['licenseData']['img'] = get_s_img($infoData['license'], '500x300(w_3)');
                $infoData['licenseData']['long'] = get_s_img($infoData['license'], '1X1(w_3)');
            }
            //推荐数据
            $recom_adv =  getRecommendData($siteId, $id, 1);
            if(count($recom_adv) > 0){
                $recom_adv = $this->handelRecommend($recom_adv);
            }
            //获取电话和qq
            $severData = getPublicTel($siteId, 0);
            $data['service_comptel'] = $severData['ServicesTel'];
            $data['service_qq'] = $severData['ServicesQQ'];
            $data['recom_adv'] = $recom_adv;
            $data['advRes'] = $advRes;
            $data['vo'] = $infoData;
            $data['OtherList'] = $result['OtherList'];
            $data['SimilarList'] = $result['SimilarList'];
            $data['HotList'] = $result['HotList'];
            $data['siteId'] = $siteId;
            $data['is_login'] = $is_login;
            $data['session_username'] = $is_login == 1? session('username') : null;
            $data['showHit'] = $showHit;
            $data['picList'] = $picList;
            $data['num'] = $result['IsNext'];
            $data['ldata']     = $ldata;
            //dump($data);
            return $data;
        }
        return '信息不存在';
    }
    /**
     *职位申请
     */
    public function applicationPosition(array $params, int $is_login, int $siteId)
    {
        $idList = $this->getParam($params, 'id', '');
        $type = $this->getParam($params, 'type', 0, 'int');//默认全职
        if($is_login != 1){
            $data['code'] = 1400;
            $data['message'] = '请先登入！';
            return json($data);
        }
        $jlData = $this->getResumeData($siteId, session('username'));
        if($jlData['id'] == 0){
            $data['code'] = 1003;
            $data['message'] = '没有简历！';
            return json($data);
        }
        //dump($jlData);exit;
        $result = send_application_position($siteId, $idList, session('username'), $type, $jlData['id'], session('uid'));
        return $result;
    }
    /**
     *全职招聘获取福利
     * @param  $id id
     * @param  $Welfare 福利
     */
    public function getWelfare($Welfare)
    {
        if(!$Welfare) return null;
        $result = explode(',', rtrim($Welfare, ','));
        return $result;
    }
    /**
     * 全职公司其他职位数据
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function fullOthersList(array $params, $siteId)
    {
        $id = $this->getParam($params, 'id', 0, 'int');
        $p = $this->getParam($params, 'page', 0, 'int');
        $result = $this->getOtherPageData($siteId, $id, $p);
        //dump($result);exit;
        return $this->get_job_full_table($result);
    }
    /**
     * 生成全职公司其他职位
     * @param data $result 查询数据
     */
    public function get_job_full_table($result)
    {
        if(count($result) == 0){
            return '';
        }
        $str = '';

        foreach ($result['OtherList'] as  $k=> $v) {
            $v['edittime'] = date('m-d', strtotime($v['edittime']));
            $v['salaryName'] = str_replace('元/月', '', $v['salaryName']);
            if($v['salaryName'] == '面议'){
                $money = '面议';
            }else{
                $money = $v['salaryName'].'元';
            }
            $compname = $v['compname'];
            $url = '/post/zhaopin/'.$v['id'].'x.html';
            $str .= '<li class="item fl">';
            $str .= '<a href="'.$url.'" class="link">';
            $str .= '<div class="titBox clearfix">';
            $str .= '<span class="price fr">'.$money.'</span>';
            $str .= '<p class="tit">'.$v['title'].'</p>';
            $str .= '</div>';

            $str .= '<div class="infoBox clearfix">';
            $str .= '<span class="date fr">'.$v['edittime'].'</span>';
            $str .= '<p class="info">'.$compname.'</p>';
            $str .= '</div>';

            $str .= '</a>';
            $str .= '</li>';
        }
        if($result['IsNext']['OtherListIsNext']==1)
        {
            $str .= '<div id="isNext"></div>';
        }
        return $str;
    }
    /**
     * 人才--全职详情接口
     * @param int $siteId 站点ID
     * @param int $id id
     * @param string $userName 用户名称
     * @param int $notCache 0缓存，1不缓存
     */
    public function getFullListData($siteId, $id, $userName, $notCache)
    {
        $request['Param']= '"siteId":'.$siteId .',"Id":"'.$id.'","userName":"'.$userName.'","notCache":"'.$notCache.'" ';
        $request['Method']="PHSocket_GetPCPostJobFulltimeData";
        $request['version']="5.6";
        $request['appName'] = "CcooCity";
        $request['ApiName'] = "zhaopinapiphp";
        $request['customerID'] = 8003;
        $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
        $info=GetAppServerApi($request);
        //$info=GetAppServerApiTest($request);//测试地址
        if($info['MessageList']['code'] == 1000){
            return $info['ServerInfo'];
        }
        return null;
    }
    /**
     * 人才--全职公司其他职位分页接口
     * @param int $siteId 站点ID
     * @param int $id id
     * @param int $type 当前页码
     */
    function getOtherPageData($siteId, $id, $type)
    {
        $request['Param']= '"siteId":'.$siteId .',"Id":"'.$id.'","pIndex":"'.$type.'" ';
        $request['Method']="PHSocket_GetPCJobFulltimeOthData";
        $request['version']="5.6";
        $request['appName'] = "CcooCity";
        $request['ApiName'] = "zhaopinapiphp";
        $request['customerID'] = 8003;
        $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
        $info=GetAppServerApi($request);
        if($info['MessageList']['code'] == 1000){
            return $info['ServerInfo'];
        }
        return null;
    }
    /**
     * 人才--兼职详情接口
     * @param int $siteId 站点ID
     * @param int $id id
     * @param string $userName 用户名称
     * @param int $notCache 0缓存，1不缓存
     */
    public function getPartListData($siteId, $id, $userName, $notCache)
    {
        $request['Param']= '"siteId":'.$siteId .',"Id":"'.$id.'","userName":"'.$userName.'","notCache":"'.$notCache.'" ';
        $request['Method']="PHSocket_GetPCPostJobParttimeData";
        $request['version']="5.6";
        $request['appName'] = "CcooCity";
        $request['ApiName'] = "zhaopinapiphp";
        $request['customerID'] = 8003;
        $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
        $info=GetAppServerApi($request);
       // $info=GetAppServerApiTest($request);
        if($info['MessageList']['code'] == 1000){
            return $info['ServerInfo'];
        }
        return null;
    }
    /**
     * 人才--兼职公司其他职位分页接口
     * @param int $siteId 站点ID
     * @param int $id id
     * @param int $type 当前页码
     */
    function getPartOtherPageData($siteId, $id, $type)
    {
        $request['Param']= '"siteId":'.$siteId .',"Id":"'.$id.'","pIndex":"'.$type.'" ';
        $request['Method']="PHSocket_GetPCJobParttimeOthData";
        $request['version']="5.6";
        $request['appName'] = "CcooCity";
        $request['ApiName'] = "zhaopinapiphp";
        $request['customerID'] = 8003;
        $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
        $info=GetAppServerApi($request);
        if($info['MessageList']['code'] == 1000){
            return $info['ServerInfo'];
        }
        return null;
    }
    /**
     * 人才--查看是否创建简历
     * @param int $siteId 站点ID
     * @param string $userName 用户名称
     */
    public function getResumeData($siteId, $userName)
    {
        $request['Param']= '"siteId":'.$siteId .',"userName":"'.$userName.'" ';
        $request['Method']="PHSocket_PostCheckUserResume";
        $request['version']="5.6";
        $request['appName'] = "CcooCity";
        $request['ApiName'] = "zhaopinapiphp";
        $request['customerID'] = 8003;
        $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
        $info=GetAppServerApi($request);
        if($info['MessageList']['code'] == 1000){
            return $info['ServerInfo'];
        }
        return null;
    }
    /**
     * 处理推荐数据
     */
    public function handelRecommend($data)
    {
        $data['len'] = count($data['arra_tjprice']);
        if(count($data['arra_tjprice']) > 0){
            foreach ($data['arra_tjprice'] as $key=>$val){
                if(count($data['arra_tjprice']) > 1){
                    if($key == 1){
                        $data['de_day'] =$val['Num'];
                        $data['de_money'] =number_format($val['price'], 2);
                        $data['de_num'] =number_format($val['price']/$val['Num'], 2);
                        $data['de_id'] =$val['id'];
                    }
                }else{
                    if($key == 0){
                        $data['de_day'] =$val['Num'];
                        $data['de_money'] =number_format($val['price'], 2);
                        $data['de_num'] =number_format($val['price']/$val['Num'], 2);
                        $data['de_id'] =$val['id'];
                    }
                }
            }
        }
        return $data;
    }
    public function getindexinfo($siteId)
    {
        $notCache = input('nocache',0);

        if ($this->redisCluster->get($this->cachePrefix.'recruit_homepage'.$siteId) && $notCache>0){
            $data=$this->redisCluster->get($this->cachePrefix.'recruit_homepage'.$siteId);
            $data=json_decode($data,true);
        }else{
            $data=$this->jk_homepage($siteId,$notCache);
            $this->redisCluster->setex($this->cachePrefix.'recruit_homepage'.$siteId,300,json_encode($data));
        }

        return $data;
    }

    public function jk_homepage($siteId,$notCache)
    {
        $request['version'] = "4.6";
        //$request['ApiName'] = "postapi4";//"CcooCityIOS";
        $request['Param'] = '"siteId":'.$siteId.',"notCache":'.$notCache;
        $request['Method'] = "PHSocket_GetPCJobIndexData";//需传
        $request['customerID']=8004;
        $request['ApiName'] = "zhaopinapiphp";//"zhaopinapiphp";
        $request['sign'] = '32asu88aseLfv+k0siQ+o7WjDbcdewhal7c1zsgWebA=';
        //return $request;die;
        $arr = GetAppServerApi($request);


        return $arr;
    }

    public function getuserinfo($siteId,$userName)
    {
        $request['version'] = "4.6";
        //$request['ApiName'] = "postapi4";//"CcooCityIOS";
        $request['Param'] = '"siteId":'.$siteId.',"userName":"'.$userName.'"';
        $request['Method'] = "PHSocket_GetPCJobIndexUserData";//需传
        $request['customerID']=8004;
        $request['ApiName'] = "zhaopinapiphp";//"zhaopinapiphp";
        $request['sign'] = '32asu88aseLfv+k0siQ+o7WjDbcdewhal7c1zsgWebA=';
        //return $request;die;
        $arr = GetAppServerApi($request);


        return $arr;
    }

    /*
     *获取招聘首页广告
     * siteid：站点id，adid：广告id
     * */
    public function getAdinfo($siteid,$adid)
    {
        if (input('notcache')==1){
            $adinfo=getadlistinfo($siteid, $adid);
        }else{
            if ($this->redisCluster->get($this->cachePrefix.'adinfo'.$siteid.'_'.$adid)){
                $adinfo=$this->redisCluster->get($this->cachePrefix.'adinfo'.$siteid.'_'.$adid);
            }else{
                $adinfo=getadlistinfo($siteid, $adid);
                $this->redisCluster->setex($this->cachePrefix.'adinfo'.$siteid.'_'.$adid,300,$adinfo);
            }
        }
        return $adinfo;
    }
}