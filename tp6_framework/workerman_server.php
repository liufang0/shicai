#!/usr/bin/env php
<?php
/**
 * Workerman服务启动脚本
 * 用于启动各种游戏的WebSocket服务
 */

define('APP_PATH', __DIR__ . '/app/');

require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

// 检查扩展
if (!extension_loaded('pcntl')) {
    exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

if (!extension_loaded('posix')) {
    exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 解析命令行参数
global $argv;
$start_file = $argv[0];

// 检查参数
if (!isset($argv[1])) {
    echo "Usage: php $start_file {start|stop|restart|reload|status|connections}\n";
    exit;
}

// 设置进程标题
if (function_exists('cli_set_process_title')) {
    cli_set_process_title('WorkermanGameServer');
}

// 根据参数启动不同的游戏服务
$command = $argv[1];
$game = isset($argv[2]) ? $argv[2] : 'all';

// 游戏列表
$games = [
    '幸运飞艇' => 'app\\controller\\Workerman幸运飞艇',
    'ssc' => 'app\\controller\\WorkermanSsc', 
    'bj28' => 'app\\controller\\WorkermanBj28',
    'jnd28' => 'app\\controller\\WorkermanJnd28',
    'xjp28' => 'app\\controller\\WorkermanXjp28',
    'lhc' => 'app\\controller\\WorkermanLhc',
    'xyft' => 'app\\controller\\WorkermanXyft',
];

if ($game === 'all') {
    // 启动所有游戏服务
    foreach ($games as $game_name => $class) {
        if (class_exists($class)) {
            echo "Starting {$game_name} service...\n";
            new $class();
        }
    }
} else {
    // 启动指定游戏服务
    if (isset($games[$game]) && class_exists($games[$game])) {
        echo "Starting {$game} service...\n";
        new $games[$game]();
    } else {
        echo "Game {$game} not found or class not exists.\n";
        exit;
    }
}

// 如果不是在根目录运行，设置运行目录
if (!is_file($start_file)) {
    $start_file = realpath(__DIR__ . '/' . $start_file);
}

Worker::$pidFile = __DIR__ . '/workerman.pid';
Worker::$logFile = __DIR__ . '/runtime/workerman.log';
Worker::$stdoutFile = __DIR__ . '/runtime/workerman_stdout.log';

// 运行所有服务
Worker::runAll();