<?php
/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 13:55
 */

namespace app\home\logic;

use think\facade\Cache;
use app\home\model\PostBdMobileTable;

class CommonLogic extends Logic
{
    use \app\home\traits\Common;
    public $postsurl="/post/pet/";

    /**
     * 分类发送验证码 默认手机绑定
     * @param $params
     * @param $is_login
     * @return array
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function sendVerificationCode(array $params, int $is_login)
    {
        if($is_login == 0){
           return $this->getResult('请先登入！', [], false);
        }
        $phone = $this->getParam($params, 'tel', '');
        $otype = $this->getParam($params, 'otype', 10, 'int');
        $result = send_verification_code($phone, $otype, $this->site_id, session('uid'), session('username'), '【PC手机绑定】', getIP(), 0, 2);
        if($result['MessageList']['code'] == 1000){
            $data['SiteId'] = $this->site_id;
            $data['tel'] = $phone;
            $data['num'] = 1;
            $data['code'] = $result['Extend']['AuthKey'];
            $data['IP'] = getIP();
            (new PostBdMobileTable())->insert($data);
        }
        return $this->getResult($result['MessageList']['message'], [], $result['MessageList']['code']);
    }
    /**
     * 校验验证码
     * @param $params
     * @param $is_login
     * @return array
     * 备注 $is_login 以后 换成 session('uid')
     */
    public function checkCode(array $params, int $is_login)
    {
        if($is_login == 0){
            return $this->getResult('请先登入！', [], 0);
        }
        $phone = $this->getParam($params, 'tel', 0, 'int');
        if(!$phone) return $this->getResult('请填写手机号！', [], 0);
        $code = $this->getParam($params, 'telcode', 0, 'int');
        if(!$code) return $this->getResult('请填写验证码！', [], 0);

        $result = $this->checkMobileCode($phone, $code);

        if(!$result) return $this->getResult('验证码错误！', [], 0);
        $data['id'] = $result;
        $data['ischk'] = 1;
        (new PostBdMobileTable())->update($data);
        /*if(!$result){
            return $this->getResult('修改失败！', [], 0);
        }*/
        return $this->getResult('验证成功！', [], 1);
    }

}