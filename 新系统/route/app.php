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
use think\facade\Route;

// ========= 兼容原有ThinkPHP 3.x URL结构 =========

// 后台管理路由组 - 兼容 /Admin/Controller/Action 结构
Route::group('Admin', function () {
    Route::rule(':controller/:action', 'admin.:controller/:action');
    Route::rule(':controller', 'admin.:controller/index');
})->pattern(['controller' => '\w+', 'action' => '\w+']);

// 小写admin也支持
Route::group('admin', function () {
    Route::rule(':controller/:action', 'admin.:controller/:action');
    Route::rule(':controller', 'admin.:controller/index');
})->pattern(['controller' => '\w+', 'action' => '\w+']);

// 代理系统路由组 - 兼容 /Agent/Controller/Action 结构
Route::group('Agent', function () {
    Route::rule(':controller/:action', 'agent.:controller/:action');
    Route::rule(':controller', 'agent.:controller/index');
})->pattern(['controller' => '\w+', 'action' => '\w+']);

// 前台路由组 - 兼容 /Home/Controller/Action 结构
Route::group('Home', function () {
    Route::rule(':controller/:action', ':controller/:action');
    Route::rule(':controller', ':controller/index');
})->pattern(['controller' => '\w+', 'action' => '\w+']);

// ========= 具体路由规则 =========

// 首页路由
Route::get('/', 'Index/index');
Route::get('/index', 'Index/index');

// 用户相关路由
Route::group('user', function () {
    Route::get('login', 'User/login');
    Route::post('login', 'User/login');
    Route::get('register', 'User/register');
    Route::post('register', 'User/register');
    Route::get('logout', 'User/logout');
    Route::get('center', 'User/center');
    Route::get('recharge', 'User/recharge');
    Route::post('recharge', 'User/recharge');
    Route::get('withdraw', 'User/withdraw');
    Route::post('withdraw', 'User/withdraw');
    Route::get('bet_history', 'User/betHistory');
    Route::get('money_log', 'User/moneyLog');
});

// API路由
Route::group('api', function () {
    Route::post('login', 'Api/login');
    Route::get('balance', 'Api/balance');
    Route::post('bet', 'Api/bet');
    Route::get('game_data', 'Api/gameData');
    Route::get('bet_history', 'Api/betHistory');
    Route::get('notice', 'Api/notice');
    Route::post('recharge', 'Api/recharge');
    Route::post('withdraw', 'Api/withdraw');
});

// 游戏控制路由
Route::group('run', function () {
    Route::get('pk10', 'Run/pk10');
    Route::get('ssc', 'Run/ssc');
    Route::get('bj28', 'Run/bj28');
    Route::get('jnd28', 'Run/jnd28');
    Route::get('xjp28', 'Run/xjp28');
    Route::get('lhc', 'Run/lhc');
    Route::get('xyft', 'Run/xyft');
    Route::get('stop', 'Run/stop');
    Route::get('status', 'Run/status');
    Route::get('restart', 'Run/restart');
    Route::get('caiji', 'Run/caiji');
});

// 管理后台路由
Route::group('admin', function () {
    Route::get('login', 'Admin/login');
    Route::post('login', 'Admin/login');
    Route::get('logout', 'Admin/logout');
    Route::get('index', 'Admin/index');
    Route::get('user_list', 'Admin/userList');
    Route::get('user_edit/:id', 'Admin/userEdit');
    Route::post('user_edit/:id', 'Admin/userEdit');
    Route::get('bet_list', 'Admin/betList');
    Route::get('recharge_list', 'Admin/rechargeList');
    Route::post('recharge_audit', 'Admin/rechargeAudit');
    Route::get('withdraw_list', 'Admin/withdrawList');
    Route::post('withdraw_audit', 'Admin/withdrawAudit');
    Route::get('setting', 'Admin/setting');
    Route::post('setting', 'Admin/setting');
});

// 数据采集路由
Route::get('caiji', 'Caiji/index');

// 兼容性路由
Route::get('hello/:name', 'index/hello');
Route::get('think', function () {
    return 'hello,ThinkPHP8!';
});
