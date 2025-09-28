<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
define('DS','/');
define('MING_SITE_PA', 'http://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost').DS );
define('MING_BASE_PA', __DIR__ );
define('MING_BASE_PATH', __DIR__ . '/');
define("UPLOAD_PATH", MING_BASE_PA . "/Uploads/");

// 应用入口文件



// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	header("Content-type: text/html; charset=utf-8");
	die('PHP环境不支持，使用本系统需要 PHP > 5.3.0 版本才可以~ !');
}

//当前目录路径
define('SITE_PATH', getcwd() . '/');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
define('VERSION','20180208');

//生成目录
//define('BIND_MODULE', 'Xyft');

// 设置缓存目录，必须可写
define('RUNTIME_PATH', './Runtime/');
// 定义应用目录
define('APP_PATH','./Application/');

//模板存放路径
define("TMPL_PATH", "./Template/");



// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';