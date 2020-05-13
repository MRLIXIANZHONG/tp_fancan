<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::pattern([ 'name' => '\w+', 'id' => '\d+','p'=> '\d+' ]);

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');
//招聘首页
Route::get('post/job/', 'Home/Recruit/homepage');
//资讯
Route::get('post/zhaopin/zhichangzixun/<id>x', 'home/zixun/info?id=:id&kind=1');
Route::get('post/zhaopin/jianlibangzhu/<id>x', 'home/zixun/info?id=:id&kind=2');
Route::get('post/zhaopin/mianshijiqiao/<id>x', 'home/zixun/info?id=:id&kind=3');

Route::get('post/zhaopin/zhichangzixun/pn<p>', 'home/zixun/index?kind=1&p=:p');
Route::get('post/zhaopin/jianlibangzhu/pn<p>', 'home/zixun/index?kind=2&p=:p');
Route::get('post/zhaopin/mianshijiqiao/pn<p>', 'home/zixun/index?kind=3&p=:p');

Route::get('post/zhaopin/zhichangzixun/', 'home/zixun/index?kind=1');
Route::get('post/zhaopin/jianlibangzhu/', 'home/zixun/index?kind=2');
Route::get('post/zhaopin/mianshijiqiao/', 'home/zixun/index?kind=3');

//兼职招聘

Route::get('post/jianzhi/pn<p>', 'Home/Recruit/partList?p=:p');
Route::get('post/jianzhi/<id>x', 'Home/Recruit/partshow?id=:id');
Route::get('post/jianzhis/list-<kind_id>-<area_id>-<p>', 'Home/Recruit/partList?kind_id=:kind_id&area_id=:area_id&p=:p');
Route::get('post/jianzhis/list-<kind_id>-0-<p>', 'Home/Recruit/partList?kind_id=:kind_id&p=:p');
Route::get('post/jianzhis/list-0-<area_id>-<p>', 'Home/Recruit/partList?area_id=:area_id&p=:p');
//全职招聘
Route::get('post/zhaopin/<id>x', 'Home/Recruit/fullshow?id=:id');

Route::get('post/jianzhi/', 'Home/Recruit/partList');
//宠物世界
Route::get('post/petfabu/<type_id>', 'Home/release/create?type_id=:type_id');
//房产交易
Route::get('post/fwfabu/<type_id>', 'Home/release/houseCreate?type_id=:type_id');
//生活服务
Route::get('post/shfabu/<type_id>', 'Home/release/liveCreate?type_id=:type_id');
//二手信息
Route::get('post/oldfabu/<type_id>', 'Home/release/secondHandCreate?type_id=:type_id');
//车辆买卖
Route::get('post/carfabu/<type_id>', 'Home/release/carCreate?type_id=:type_id');
//分类首页
//Route::get('post', 'Home/index/index');

return [

];
