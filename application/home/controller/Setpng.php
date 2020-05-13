<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25/025
 * Time: 15:38
 */

namespace app\home\controller;


use think\Controller;
use think\Db;
use think\Request;

class Setpng extends Controller
{
    //网站首页
    public function set($tel)
    {
       /* if(!Request::param('tel')){
            return json('非法参数');
        }*/
       //$p=input('tel');


        $p=$tel;
        //$p = 13888888888;     //电话号码
        //  $p=Request::param('tel');
        if($p !=''){
            //生成5位的数字图片
            Header("Content-type:image/png"); //告诉浏览器，下面的数据是图片，而不要按文字显示
            //定义图片宽高
            if (strlen($p) >12){
                $nwidth=280;
            }else{
                $nwidth=260;
            }
            $nheight=60;
            $background=255;
            $im=@imagecreate($nwidth,$nheight) or die("Can't initialize new GD image stream"); //建立图象
            //图片色彩设置
            $background_color=imagecolorallocate($im,255,255,255); //匹配颜色
            //$text_color=imagecolorallocate($im,23,14,91);
            $text_color=imagecolorallocate($im,255,153,0);
            //绘制图片边框
           // imagefilledrectangle($im,0,0,$nwidth-1,$nheight-1,$background); //矩形区域着色
            imagerectangle($im,0,0,$nwidth-1,$nheight-1,$background_color); //绘制矩形
            //srand((double)microtime()*1000000); //取得目前时间的百万分之一秒值，以执行时的百万分之一秒当乱数种子
            //$randval=rand();
            $randval=$p; //5位数
          //  imagestring($im,5,20,5,$randval,$text_color); //绘制横式字串
            imagettftext($im,30,0,5,40,$text_color,'./Arial.ttf',$randval);//绘制字符串

            //加入干扰因素
            //for($i=0;$i<478;$i++)
            //{
            //$randcolor=imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
            //imagesetpixel($im,rand()%100,rand()%30,$randcolor); //点
            //}
            //imagestring($im,3,5,5,"A Simple Text String",$text_color);
            //imageinterlace($im,1);
            ob_start();
            imagepng($im); //建立png图型
            $content=ob_get_clean();

            imagedestroy($im); //结束图型

            $image_data_base64 = base64_encode ($content);
            return $image_data_base64;exit();
           // return response($content,200,['content-Length'=>strlen($content)])->contentType('image/png');
           // return $content;
           // exit();
        }else{
            echo "商家未输入电话号码";
        }

    }

}