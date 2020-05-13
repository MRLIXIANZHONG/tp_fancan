<?php

//获取值
function GetValue($str,$s,$e)
{
    $result = array();
    preg_match_all('/(?<=(' . $s . '))[.\\s\\S]*?(?=(' . $e . '))/',$str, $result);
    return $result[0][0];
}

// 定义一个函数getIP() 客户端IP，
function getIP(){
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";

    if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))
        return $ip;
    else
        return '';
}
// 服务器端IP
function serverIP(){
    return gethostbyname($_SERVER["SERVER_NAME"]);
}

//格式化时间
function now($time=0){
    if ($time>0){
        return date('y-m-d h:i:s',$time);
    }else{
        return date('y-m-d h:i:s',time());
    }
}

//判断一个字符串是否是时间格式

function is_date($str){
    $preg = '/^([12]\d\d\d)-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[0-1]) ([0-1]\d|2[0-4]):([0-5]\d)(:[0-5]\d)?$/';
    return preg_match($preg, $str);
}

/**过滤字符串中的标签样式空格等
 *$str 目标字符串
 * $is_kong 是否过滤空格
 * $length 截取字符串长度
 */
function str_handle($str,$is_kong=0,$length=0){
    $new_str=strip_tags($str);//过滤标签
    if ($is_kong==0){

    }else{
        $oldchar=array(" ","　","\t","\n","\r");
        $newchar=array("","","","","");
        $new_str=str_replace('　　','',$new_str);
        $new_str=str_replace('&nbsp;','',$new_str);
        $new_str=str_replace($oldchar,$newchar,$new_str);
    }

    if ($length==0){
        return $new_str;
    }
    return trim(mb_substr($new_str,0,$length,'utf8').'...');
}

function date_to_time($str,$format="Y-m-d"){
    $time=strtotime($str);
    return date($format,$time);
}

/**
 * 判断两个时间是否是相同
 * return：0相同，1：不同
 */
function datediff($str="d",$addtime,$oldtime){
    $time2=strtotime($oldtime);
    $time1=strtotime($addtime);
    if ($time2>$time1){
        $cha=$time2-$time1;
    }else{
        $cha=$time1-$time2;
    }
    if ($str=='s'){
        return $cha;
    }elseif ($str=='i'){
        return floor($cha/60);
    }elseif ($str=='h'){
        return floor($cha/3600);
    }elseif ($str=='d'){
        return floor($cha/86400);
    }elseif($str=='m'){
        return floor($cha/2592000);
    }elseif($str=='y'){
        return floor($cha/31104000);
    }
}

/**
 * 获取数组中的某一列
 * @param array $arr 数组
 * @param string $key_name  列名
 * @return array  返回那一列的数组
 */
function get_arr_column($arr, $key_name)
{
    $arr2 = array();
    if(!empty($arr)){
        foreach($arr as $key => $val){
            $arr2[] = $val[$key_name];
        }
    }
    return $arr2;
}
/**
 * 获取公司类型
 * @param int $type 类型
 */
function get_company_kind($type)
{
    switch($type)
    {
        case 1:
            return "民营";
            break;
        case 2:
            return "外商独资";
            break;
        case 3:
            return "国企";
            break;
        case 4:
            return "合资";
            break;
        case 5:
            return "股份制企业";
            break;
        case 6:
            return "上市公司";
            break;
        case 7:
            return "国家机关";
            break;
        case 8:
            return "事业单位";
            break;
        case 9:
            return "其他";
            break;

        default:
            return '';
    }
}
/**
 * 获取公司规模
 * @param int $type 类型
 */
function get_company_size($type)
{
    switch($type)
    {
        case 1:
            return "0-49人";
            break;
        case 2:
            return "50-99人";
            break;
        case 3:
            return "100-500人";
            break;
        case 4:
            return "500人以上";
            break;
        default:
            return '';
    }

}
/**
 * 获取公司行业(废弃)
 * @param int $id 自增ID
 */
function get_company_trade($read, $id)
{
    $sql = "Select top 1 kindname From job_hy_kind where (id=$id or oid=$id)";
    $db = Db::connect($read);
    $result = $db->query($sql);
    if(count($result) == 0){
        return '--';
    }
    return $result[0]['kindname'];
}
/**
 * 关注度类型
 * @param int $value 点击数
 */
function IsStar($value)
{
    $oStar = floor($value / 50 + 1);
    $oStar = $oStar > 5 ? 5 : $oStar;
    switch($oStar)
    {
        case 1:
            return '<i class="xicon-xingxing1 xicon"></i>';
            break;
        case 2:
            return '<i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i>';
            break;
        case 3:
            return '<i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i>';
            break;
        case 4:
            return '<i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i>';
            break;
        case 5:
            return '<i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i><i class="xicon-xingxing1 xicon"></i>';
            break;
    }
}
/**
 * 处理企业库图片
 * @param string $value 图片路径
 */
function get_company_img($value)
{
    if(!$value){
        return '';
    }
    $data = explode('|', $value);
    foreach($data as $key=>$val){
        $picData[$key]['img'] = get_s_img($val, '500x500(w)');
        $picData[$key]['long'] = $val;

    }
    return $picData;
}

/**
 * 获取缩略图
 * @param string $url 图片路径
 */
function get_s_img($url, $size){
    if(strpos($url,'http') !== 0){
    }else{
        $regex = '/http:\/\/p([0-9]+)\.pccoo\.cn\/(\w+\/\d+\/\w+)(\.(jpg|jpeg|png|gif))/i';
        $dis = "_$size";
        $url = preg_replace($regex,"http://r$1.pccoo.cn/$2" . $dis . "$3",$url);
    }
    return $url;
}

/**
 * 期望薪水方式
 * @param string $type 类型
 */
function get_salary($type)
{
    switch($type)
    {
        case 2:
            return '元/时';
            break;
        case 3:
            return '元/每次';
            break;
        default:
            return '元/天';
    }
}
/**
 * 付款周期
 * @param string $type 类型
 */
function get_payment($type)
{
    switch($type)
    {
        case 2:
            return '周结';
            break;
        case 3:
            return '月结';
            break;
        case 4:
            return '完工结算';
            break;
        default:
            return '日结';
    }
}
/**
 * 获取学历
 * @param string $type 类型
 */
function get_education($type)
{
    switch($type)
    {
        case 0:
            return '不限';
            break;
        case 1:
            return '初中';
            break;
        case 2:
            return '高中及中专';
            break;
        case 3:
            return '大专';
            break;
        case 4:
            return '本科';
            break;
        case 5:
            return '硕士以上';
            break;
        default:
            return '不限';
    }
}
/**
 * 工作经验
 * @param string $type 类型
 */
function get_record($type)
{
    switch($type)
    {
        case 0:
            return '不限';
            break;
        case 1:
            return '应届毕业生';
            break;
        case 2:
            return '一年';
            break;
        case 3:
            return '1-3年';
            break;
        case 4:
            return '3-5年';
            break;
        case 5:
            return '5-8年';
            break;
        case 6:
            return '8年以上';
            break;
        default:
            return '不限';
    }
}
/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method="GET", $postfields = null, $headers = array(), $debug = false) {
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if($ssl){
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    //curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}
/**
 * 调用接口模板
 * @param array $request 类型
 */
function GetAppServerApi($request){
    $request['appName'] = empty($request['appName']) ? "WebApp" : $request['appName'];
    $request['requestTime'] = date("Y-m-d H:i:s");
    $request['customerID'] = isset($request['customerID']) ? $request['customerID'] : 8004;
    $request['sign'] = empty($request['sign']) ? '32cdu88aseLfv+k0siQ+17WjDbc23whalsc1z5aWea2=' : $request['sign'];
    $request['customerKey'] = md5($request['sign'] . $request['Method'] . $request['requestTime']);//普通MD5加密
    $body = '{"appName":"' . $request['appName'] . '","Param":{' . $request['Param'] . '},"requestTime":"' . $request['requestTime'] . '","customerKey":"' . $request['customerKey'] . '","Method":"' . $request['Method'] . '","Statis":{"SystemNo":"1"},"customerID":' . $request['customerID'] . ',"version":"' . $request['version'] . '"}';
    //dump($body);
    $post_arr = array('param'=>$body);
    //$json = httpRequest('http://localhost:52511/appserverapi.ashx',"POST", $post_arr);//测试地址
    $json = httpRequest('http://' . $request['ApiName'] . '.bccoo.cn/appserverapi.ashx',"POST", $post_arr);//调试请求连接
    return json_decode($json,true);
}
/**
 * 调用接口模板
 * @param array $request 类型
 */
function GetAppServerApiTest($request){
    $request['appName'] = empty($request['appName']) ? "WebApp" : $request['appName'];
    $request['requestTime'] = date("Y-m-d H:i:s");
    $request['customerID'] = isset($request['customerID']) ? $request['customerID'] : 8004;
    $request['sign'] = empty($request['sign']) ? '32cdu88aseLfv+k0siQ+17WjDbc23whalsc1z5aWea2=' : $request['sign'];
    $request['customerKey'] = md5($request['sign'] . $request['Method'] . $request['requestTime']);//普通MD5加密
    $body = '{"appName":"' . $request['appName'] . '","Param":{' . $request['Param'] . '},"requestTime":"' . $request['requestTime'] . '","customerKey":"' . $request['customerKey'] . '","Method":"' . $request['Method'] . '","Statis":{"SystemNo":"1"},"customerID":' . $request['customerID'] . ',"version":"' . $request['version'] . '"}';
    //dump($body);exit;
    $post_arr = array('param'=>$body);
    $json = httpRequest('http://192.168.0.10:82/appserverapi.ashx',"POST", $post_arr);//测试地址
    //$json = httpRequest('http://' . $request['ApiName'] . '.bccoo.cn/appserverapi.ashx',"POST", $post_arr);//调试请求连接
    return json_decode($json,true);
}
/**
 * 内容页广告模板
 * @param array $request 类型
 */
function advTemple($value, $placeID)
{
    $hand = explode('_',$value['Pic']);
    $ext = explode('.',$value['Pic']);
    $ext = array_pop($ext);
    array_pop($hand);
    $hand = implode('_',$hand).'.'.$ext;
    $str = '';
    $str .= '<div adid="'.$placeID.'" class="postAd2016">';
    $str .= '<a href="'.$value['Href'].'" target=_blank style="position: relative; display: block;">';
    $str .= '<img border="0" align="top" src="'.$hand.'" title="'.$value['Title'].'" />';
    $str .= '</a>';
    $str .= '</div>';
    //dump($value);exit;
    return $str;
}
/**
 * 列表页广告模板
 * @param array $request 类型
 */
function advTempleList($value)
{
    $hand = explode('_',$value['Pic']);
    $ext = explode('.',$value['Pic']);
    $ext = array_pop($ext);
    array_pop($hand);
    $hand = implode('_',$hand).'.'.$ext;
    $str = '';
    $str .= '<a href="'.$value['Href'].'" target=_blank>';
    $str .= '<img border="0" align="top" src="'.$hand.'" title="'.$value['Title'].'" />';
    $str .= '</a>';

    //dump($value);exit;
    return $str;
}
/**
 * 查询广告数据
 * @param int $siteId 站点ID
 * @param string $placeID 广告ID
 */
function GetDivBrandInfoList($siteId, $placeID, $pageName, $advType, $showType=0, $cityAdv=0, $showDesc=0)
{
    $request['Param']= '"siteID":'.$siteId .',"placeID":"'.$placeID.'","pageName":'.$pageName.',"advType":'.$advType.',"cityAdv":'.$cityAdv.',"showDesc":'.$showDesc.'';
    $request['Method']="PHSocket_GetCcooAdvertInfo";
    $request['version']="4.6";
    $request['ApiName'] = "advapi";
    $request['sign'] = '32asu88aseLfv+k0siQ+o7WjDbcdewhal7c1zsgWebA=';
    $info=GetAppServerApi($request);
    if($info['MessageList']['code'] == 1000){
        //判断是否是百度广告 AdvFrom 1 配置过的广告 0 代表百度广告
        if($info['ServerInfo']['AdvFrom'] == 1){
            if(is_array($info['ServerInfo']['Content'])){
                if($showType == 0){
                    return advTemple($info['ServerInfo']['Content'][0], $placeID);
                }else{
                    return advTempleList($info['ServerInfo']['Content'][0]);
                }
            }
            return null;
        }else{
            if($info['ServerInfo']['Content'] == ''){
                return null;
            }
            return $info['ServerInfo']['Content'];//百度广告
        }
    }
    return null;
}
function GetDivBrandInfoInfo($siteId, $placeID, $pageName, $advType, $showType=0, $cityAdv=0, $showDesc=0)
{
    $request['Param']= '"siteID":'.$siteId .',"placeID":"'.$placeID.'","pageName":'.$pageName.',"advType":'.$advType.',"cityAdv":'.$cityAdv.',"showDesc":'.$showDesc.'';
    $request['Method']="PHSocket_GetCcooAdvertInfo";
    $request['version']="4.6";
    $request['ApiName'] = "advapi";
    $request['sign'] = '32asu88aseLfv+k0siQ+o7WjDbcdewhal7c1zsgWebA=';
    $info=GetAppServerApi($request);
    if($info['MessageList']['code'] == 1000){
        return $info['ServerInfo']['Content'];
    }
    return null;
}

/**
 * 获取职位的联系方式
 * @param int $siteId 站点id
 * @param int $jobId  职位id
 * @param int $oType 职位类型：0全职 1兼职
 * @param string $userName 当前登入的用户名
 * @return |null
 */
function getPositionTel($siteId, $jobId,$oType,$userName)
{
    $request['Param']= '"siteId":'.$siteId .',"jobId":'.$jobId.',"oType":'.$oType.',"userName":"'.$userName.'" ';
    $request['Method']="PHSocket_GetPCPostJobTel";
    $request['version']="5.6";
    $request['appName'] = "CcooCity";
    $request['ApiName'] = "zhaopinapiphp";
    $request['customerID'] = 8003;
    $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
    $info=GetAppServerApi($request);
    return $info;
}


/**
 * 人才--推荐数据
 * @param int $siteId 站点ID
 * @param int $id id
 * @param int $type 1全职 2兼职
 */
function getRecommendData($siteId, $id, $type)
{
    $request['Param']= '"siteId":'.$siteId .',"Id":"'.$id.'","jtype":"'.$type.'" ';
    $request['Method']="PHSocket_GetPCPCBuyTJData";
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
 * 是否投递简历
 * @param int $tdstr cookie存储的记录
 * @param string $tmpid 职位ID
 */
function get_td($tdstr, $tmpid)
{
    $gettd = 1;
    if(strpos($tdstr, $tmpid) === false){
        $gettd = 0;
    }
    return $gettd;
}
/**
 * js escape php 实现
 * @param $string  the sting want to be escaped
 * @param $in_encoding
 * @param $out_encoding
 */
function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
    $return = '';
    if (function_exists('mb_get_info')) {
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
            $str = mb_substr ( $string, $x, 1, $in_encoding );
            if (strlen ( $str ) > 1) { // 多字节字符
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
            } else {
                $return .= '%' . strtoupper ( bin2hex ( $str ) );
            }
        }
    }
    return $return;
}
/**
 * php实现 js的 unescape解密
 * @param string $str 解密的字符串
 */
function unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i ++)
    {
        if ($str[$i] == '%' && $str[$i + 1] == 'u')
        {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
                if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) .
                        chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
            if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
    }
    return $ret;
}
/**
 * 分类发布--判断发布权限
 * @param int $siteId 站点ID
 * @param int $classID classID
 * @param string $userName 用户名
 * @param string $ip IP地址
 * $classID 1：job_info（全职招聘） 2：job_jz_info（兼职招聘） 3：Home_ChuShou（出售） 4：Home_ChuZu（出租） 5：Home_QiuGou（求购）
 * 6：Home_QiuZu（求租） 7：Home_Chudui（出兑 店铺转让） 8：idle_info（二手） 9:TRAFFIC_INFO（车辆） 10：live_info（生活）
 * 13：friend_info（交友） 14：pet_info（宠物） 15：TRAFFIC_INFO（拼车）
 */
function get_post_fabu_root($siteId, $classID, $userName, $ip)
{
    $request['Param']= '"siteid":'.$siteId .',"source":'.$classID.',"userName":"'.$userName.'","ip":"'.$ip.'"';
    $request['Method']="PHSocket_CheckSendRoot";
    $request['version']="4.6";
    $request['ApiName'] = "webappnew";
    $request['customerID'] = 8003;//此处customerID 必须是8003 否则配置读取失败
    $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
    $info=GetAppServerApi($request);
    if(count($info) > 0){
        if(count($info['ServerInfo']) > 0){
            return $info['ServerInfo'];
        }
        return null;
    }
    return null;
}
/**
 * 分类发布--判断发布权限(已废弃)
 * @param int $siteId 站点ID
 * @param int $classID classID
 */
function check_tel($tel, $username, $read)
{
    $tempTel = str_replace('undefined', '', $tel."");
    if(!$tempTel){
        $checkTel = false;
        return $checkTel;
    }
    $sql = "Select Top 1 id From CheckTel With(NoLock) Where (Tel = '".$tempTel."' Or Tel = '".$username."')";
    $db = Db::connect($read);
    $result = $db->query($sql);
    if(count($result) > 0){//电话禁发
        $checkTel = true;
    }else{
        $checkTel = false;
    }
    return $checkTel;
}
/**
 * 非法关键词验证接口
 * @param string $keyWorld 校验的字符串
 * @param string $title 标题
 * @param int $infoType 0 全部 1 生活分类(post) 2 社区闹闹(bbs) 3 城事类(news) 4用户类(User) 5楼盘类(lp) 6商务类(store) 7待扩展频道
 * @param int $siteId 站点ID
 * @param int $userID 用户ID
 * @param string $userName 账号
 * @param string $url 区分那个那个方法请的接口
 * @param string $checkType check1 => check6 的 &值。PS：KeyTypeEnum值。  1 2 4 8 16 32  ， 默认31， 分类用了19，其他频道请自行计算。
 * @param int $rangeLength 兼容长度忽略敏感词中间字的个数
 * @param int $bedWordType 1真实敏感词 2包含敏感词的字符串
 * @param string $sp 匹配相似度最低值，默认0.5
 * return value KeyType 敏感词级别 1摄政禁发，2转审核，3摄政提醒，4低俗提醒，5广告法限制替换，6待扩展
 */
function get_check_key($keyWorld, $title,$infoType, $siteId, $userID, $userName, $url, $checkType = 31, $rangeLength = 0, $bedWordType = 1, $sp = "0.5")
{
    $request['Param'] = '"body":"'.str_replace(['"',' ','  '], '', stripslashes($keyWorld)) .'","infoType":'.$infoType.',"siteID":'.$siteId.',"userID":'.$userID.',"userName":"'.$userName.'","url":"'.$url.'",';
    $request['Param'] .= '"checkType":'.$checkType.',"fromType":1,"rangeLength":"'.$rangeLength.'","bedWordType":'.$bedWordType.',"sp":"'.$sp.'"';
    $request['Method']="IllegalKeyAPI_GetSensitiveWordCheckNew";
    $request['version']="4.6";
    $request['ApiName'] = "illegalkeyapi";
    $request['customerID'] = 8004;
    $request['sign'] = '32csd44fgdwertgyusdfsd1ewwejhhalsc1z5aWea2=';
    $info=GetAppServerApi($request);

    if($info['MessageList']['code'] == 1000){
        $data['code'] = 1000;
        if(count($info['ServerInfo']) > 0){
            foreach ($info['ServerInfo'] as  $key=> $val) {
                if($val['KeyType'] == 1){
                    $data['code'] = 1001;//摄政禁发
                    break;
                }
                if($val['KeyType'] == 2){
                    $data['code'] = 1002;//转审核
                }
                if($val['KeyType'] == 5){
                    $data['code'] = 1005;//广告法限制替换
                    $keyWorld = str_replace($val['SimilarWord'],str_repeat("*",mb_strlen($val['SimilarWord'],'utf8')),$keyWorld);
                    $total = mb_strlen($keyWorld,'utf8');
                    $len = mb_strlen($title,'utf8');
                    $data['title'] = mb_substr($keyWorld,0,$len,'utf8');
                    $data['info'] = mb_substr($keyWorld,$len,$total-$len,'utf8');
                }

            }

        }
        return $data;
    }

}
/**
 * 分类发布--发送手机验证码
 * @param string $phone 手机号
 * @param int $otype 短信发送类型 4手机绑定 6手机注册 7手机登录 8登录或绑定 9找回密码 10手机发送验证码 81短信提醒
 * @param int $siteId 城市ID
 * @param int $userID 用户ID
 * @param string $userName 用户名
 * @param string $tag 用户名
 * @param string $ip IP地址
 * @param int $fromType 来源  0 pc 1 webapp 2 安卓 3 ios 5微生活
 * @param int $api 短信三方接口 0 漫道 1阿里 2软维
 *
 */
function send_verification_code($phone, $otype, $siteId, $userID, $userName, $tag, $ip, $fromType, $api)
{
    $request['Param']= '"phone":"'.$phone.'","otype":'.$otype.',"siteID":'.$siteId .',"userID":'.$userID.',"userName":"'.$userName.'","tag":"'.$tag.'","ip":"'.$ip.'","fromType":'.$fromType.',"api":'.$api.'';
    $request['Method']="SmsSendAPI_SendSmsCode";
    $request['version']="4.8";
    $request['ApiName'] = "smssendapi";
    $request['sign'] = '32csd44fgdwertgyusdfsd1ewwejhhalsc1z5aWea2=';
    $info=GetAppServerApi($request);

    return $info;
}
/**
 * 人才--申请职位接口
 * @param int $siteId 站点ID
 * @param string $jobIDList 职位id 多职位 逗号分隔
 * @param string $userName 用户名
 * @param int $type 类型 0全职，1兼职
 * @param int $jlid 简历ID
 * @param int $fromUserId 用户id
 */
function send_application_position($siteId, $jobIDList, $userName, $type, $jlid, $fromUserId)
{
    $request['Param']= '"siteID":'.$siteId .',"jobIDList":'.$jobIDList.',"userName":"'.$userName.'","type":"'.$type.'","jlid":"'.$jlid.'","fromUserId":"'.$fromUserId.'" ';
    $request['Method']="PHSocket_SetUserApplyForAllJobs";
    $request['version']="5.6";
    $request['appName'] = "CcooCity";
    $request['ApiName'] = "zhaopinapiphp";
    $request['customerID'] = 8003;
    $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
    $info=GetAppServerApi($request);
    return $info['MessageList'];
}
/**
 * 获取公共电话和qq
 * @param int $siteId 站点ID
 * @param int $id id
 */
function getPublicTel($siteId, $tabCls)
{
    $request['Param']= '"siteId":'.$siteId .',"tabCls":"'.$tabCls.'" ';
    $request['Method']="PHSocket_GetPostServiceInfoData";
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
 * 日志记录
 * @param unknown $msg
 * @param unknown $url
 * @param unknown $param
 * @param unknown $fromType
 */
function LogRecord($msg,$url,$param,$fromType){
    $request['Param']= '"logContent":"'. $msg .'","url":"'.$url.'","param":"'.$param.'","fromtype":"'.$fromType.'"';//需传
    $request['Method'] = "PHSocket_SetAppLogMsg";//需传
    $request['version'] = "4.7";
    $request['appName'] = "CcooCity";
    $request['ApiName'] = "zhaopinapiphp";
    $request['customerID'] = 8003;
    $request['sign'] = '32asu83aseLfv+k0seQ+o7WjDbcdewhal7c1zsgWasA=';
    $info=GetAppServerApi($request);
    return $info;
}
/**
 * 房产交易获取1980年到当前年份的数据
 */
function get_home_year()
{
    $y = date('Y');
    for($i=1980;$i<=$y;$i++){
        $data[] = $i;
    }
    return $data;
}
/**
 * 房产交易获取1980年到当前年份的数据
 */
function get_home_money()
{
    $data = array("面议", "10万以下", "10-20万", "20-30万", "30-40万", "40-50万", "50-60万", "60-80万", "80-100万", "100-150万", "150-200万", "200万以上");
    return $data;
}
/**
 * 房产交易获取1980年到当前年份的数据
 */
function get_h_type($h, $s, $t)
{
    $type = $h;
    if($s > 0){
        if($s >= 4){
            $type = 11;
            return $type;
        }else{
            switch($s)
            {
                case 1:
                    if($t == 0){
                        $type = 13;
                        return $type;
                    }
                    if($t == 1){
                        $type = 6;
                        return $type;
                    }
                    break;
                case 2:
                    if($t == 1){
                        $type = 7;
                        return $type;
                    }
                    if($t == 2){
                        $type = 8;
                        return $type;
                    }
                    break;
                case 3:
                    if($t == 1){
                        $type = 9;
                        return $type;
                    }
                    if($t == 2){
                        $type = 10;
                        return $type;
                    }
                    break;
            }
        }
    }
    return $type;
}
/**
 * 字符串处理 获取 数字，字母和汉字
 */
function replace_title($title){

    preg_match_all('/[\x{4e00}-\x{9fa5}a-zA-Z0-9]/u' , $title, $result);
    return implode('', $result[0]);
}
/**
 * 对数据进行编码转换
 * @param array/string $data  数组
 * @param string $output    转换后的编码
 */
function charset_icon($data, $to) {
    if(is_array($data)) {
        foreach($data as $key => $val) {
            $data[$key] = charset_icon($val, $to);
        }
    } else {
        $encode_array = array('ASCII', 'UTF-8', 'GBK', 'GB2312', 'BIG5');
        $encoded = mb_detect_encoding($data, $encode_array);
        $to = strtoupper($to);
        if($encoded != $to) {
            $data = mb_convert_encoding($data, $to, $encoded);
        }
    }
    return $data;
}
/**
 * 获取当前菜单
 * @param int  $type 类型
 */
function get_menu($type)
{
    switch($type)
    {
        case 1:
            return "人才网";
            break;
        case 2:
            return "二手网";
            break;
        case 3:
            return "房产网";
            break;
        case 32:
            return "房产网";
            break;
        case 4:
            return "生活网";
            break;
        case 5:
            return "车辆网";
            break;
        case 6:
            return "生活网";
            break;
        case 7:
            return "交通出行";
            break;
        case 8:
            return "宠物网";
            break;
        case 642:
            return "交友网";
            break;
        case 8:
            return "宠物网";
            break;
        default:
            return "分类信息";
    }
}
/**
 * 1、隐藏电话号码中间4位和邮箱
 */
function hidTel($phone)
{
    //隐藏邮箱
    if (strpos($phone, '@')) {
        $email_array = explode("@", $phone);
        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($phone, 0, 3); //邮箱前缀
        $count = 0;
        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $phone, -1, $count);
        $rs = $prevfix . $str;
        return $rs;
    } else {
        //隐藏联系方式中间4位
        $Istelephone = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
        if ($Istelephone) {
            return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
        } else {
            return preg_replace('/(1[0-9]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
        }
    }

}
/**
 *获取企业头像
 */
function get_company_portrait($res)
{
    $tempImg = 'http://c.imgccoo.cn/wap/webapp/images/me-logologo.jpg';
    if($res['isrz'] || $res['ismq']){
        $tempImg = 'http://img.pccoo.cn/website/fenlei/images/logo_rz.jpg';
        if($res['ismq']){
            $tempImg = 'http://img.pccoo.cn/website/fenlei/images/logo_mq.jpg';
        }
    }
    return $tempImg;
}
/**
 * 替换数组某个下标的值
 * @param array $arr 数组
 * @param string $key  列名
 * @param string $value  需要替换的值
 * @return array/bool  返回那一列的数组
 */
function array_replace_value($array, $key, $value)
{
    if (count($array) == count($array, 1)) {
        //一维数组
        if(!isset($array[$key])) return false;
        $array[$key] = $value;
    } else {
        //二维数组
        foreach ($array as  $k=> &$val) {
            if(!isset($val[$key])) return false;
            $val[$key] = $value;
        }
    }
    return $array;
}
/**
 * 获取当前菜单
 * @param int  $type 类型
 */
function get_salary_define($type)
{
    switch($type)
    {
        case 0:
            return "面议";
            break;
        case 1:
            return "1000元以下";
            break;
        case 2:
            return "1000-1999元";
            break;
        case 3:
            return "2000-2999元";
            break;
        case 4:
            return "3000-4999元";
            break;
        case 5:
            return "5000-7999元";
            break;
        case 6:
            return "8000-11999元";
            break;
        case 7:
            return "12000-19999元";
            break;
        case 8:
            return "20000以上";
            break;
    }
}

/**
 * 字符串截取
 * @param array  $data 需要查找字符串
 * @param string  $result 要替换的字符串
 * @param string  $string 字符串
 */
function strRepeat($data, $result, $string)
{
    foreach ($data as  $key=> $val) {
        $string = str_replace($val, $result, $string);
    }
    return $string;
}
/**
 * 个人中心-我的发布-url链接
 * @param $catId 类别id
 * @param $id    详情id
 * @param $modId 模块id 我的发布1 我的招聘2
 */
function get_personal_url($catId, $id, $modId)
{
    $url = '';
    if($modId == 1){
        if($catId == 1){

        }elseif($catId == 5){
            //房屋求购
            $url = '/post/fangwu/qiugou/'.$id.'x.html';
        }elseif($catId == 6){
            //房屋求租
            $url = '/post/fangwu/qiuzu/'.$id.'x.html';
        }elseif($catId == 8){
            //房屋求租
            $url = '/post/fangwu/qiuzu/'.$id.'x.html';
        }elseif($catId == 9){
            //车辆买卖
            $url = '/post/cheliang/'.$id.'x.html';
        }elseif($catId == 10){
            //生活服务
            $url = '/post/shenghuo/'.$id.'x.html';
        }elseif($catId == 14){
            //宠物
            $url = '/post/pet/'.$id.'x.html';
        }elseif($catId == 14){
            //拼车
            $url = '/post/cheliang/'.$id.'x.html';
        }
    }

    return $url;
}
/**
 * 读取指定目录的配置
 */
function red_web_config()
{
    //$result = file_get_contents(config('htdocs_root').'webconfig/WebConfig.txt');
    $result = file_get_contents('http://m68.ccoo.cn/webconfig/webconfig.txt');
    if(preg_match('/^\xEF\xBB\xBF/',$result))
    {
        $result = trim(substr($result,3));
    }
    $result= json_decode($result, true);
    if(count($result) > 0){
        return $result = array_column($result['ServerInfo'], NULL ,'wapSiteUrl');
    }
    return $result;
}
/**
 * 根据二维数组某个字段的值查找数组
 * @param $array 原数组
 * @param $index 字段名称
 * @param $value 字段值
 */
function search_by_value ($array, $index, $value){
    $data = [];
    if(is_array($array) && count($array)>0)
    {
        foreach(array_keys($array) as $key){
            $temp[$key] = $array[$key][$index];
            if ($temp[$key] == $value){
                $data[] = $array[$key];
            }
        }
    }
    if(count($data) > 0){
        return $data[0];
    }
    return $data;
}

function getadlistinfo($siteId,$placeID){
    $request['Param']= '"siteID":'.$siteId .',"placeID":'.$placeID;
    $request['Method']="PHSocket_GetPcPostAdvList";
    $request['version']="4.6";
    $request['ApiName'] = "advapi";//advapi,appnewv5
    $request['sign'] = '32asu88aseLfv+k0siQ+o7WjDbcdewhal7c1zsgWebA=';
    $info=GetAppServerApi($request);
    if($info['MessageList']['code'] == 1000){
        $ServerInfo=$info['ServerInfo'];
        $AdType=$ServerInfo['AdType'];
        $ViewStyle=$ServerInfo['ViewStyle'];
        $Advw=$ServerInfo['Advw'];
        $advh=$ServerInfo['Advh'];
        $AdList=$ServerInfo['AdList'];
        if ($AdType==1){
            $temphtml='<div class="ccooliad">';
            $temphtml.=$info['ServerInfo']['AdvHtml'];
            $temphtml.='</div>';
            return '';
        }else{
            if (is_array($AdList)){
                $temphtml='<section class="ccooliad">';
                foreach ($AdList as $k=>$value){
                    if ($value['isUserCode']==1){
                        $temphtml.=$value['userCode'];
                    }else{
                        if ($ViewStyle==1){
                            $temphtml.="<a AdId='" . $value['advid'] . "' href='/ad.asp?id=" . $value['advid'] . "' target='_blan'>" . $value['advtitle']. "</a>";
                        }else{
                            if ($value['pic']){
                                if (strpos($value['pic'],'.swf')!==false){
                                    $temphtml = $temphtml . "<div AdId='".$value['advid']."' class='postAd2016'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0'";
                                    if ($Advw > 0) { $temphtml .= " width='" . $Advw . "'"; }
                                    if ($advh > 0) { $temphtml .= " height='" . $advh . "' >"; }
                                    $temphtml .= "	<param name='movie' value='" . $value['pic'] . "'>" . "	<param name='quality' value='high'>" . "	<param name='wmode' value='transparent'>"."<embed src='".$value['pic']."' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'";
                                    if ($Advw > 0) { $temphtml .= " width='" . $Advw . "'"; }
                                    if ($advh > 0) { $temphtml .= " height='" . $advh . "'></embed>"; }
                                    $temphtml .= "</object></div>";
                                }else{
                                    $temphtml .= "<div AdId='" . $value['advid'] . "' class='postAd2016'><a href='/ad.asp?id=" . $value['advid'] . "' target='_blank' style='margin:0 0 10px 0;'>";
                                    $temphtml .= "<img border='0' align='top' src='" . $value['pic'] . "'";
                                    if ($Advw > 0) { $temphtml .= " width='" . $Advw . "'"; }
                                    if ($advh > 0) { $temphtml .= " height='" . $advh . "'"; }
                                    if ($value['advtitle']) { $temphtml .= " title='" . $value['advtitle'] . "' ALT='" . $value['advtitle'] . "'"; }
                                    $temphtml .= ">";
                                    $temphtml .= "</a></div>";
                                }
                            }
                        }
                    }
                }
                $temphtml.='</section>';
                return $temphtml;
            }
        }
    }
    return null;
}

