#!/usr/bin/env php
<?php
/**
 * Socket.IO服务启动脚本
 */

require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;
use PHPSocketIO\SocketIO;

// 全局数组保存uid在线数据
$uidConnectionMap = array();
// 记录最后一次广播的在线用户数
$last_online_count = 0;
// 记录最后一次广播的在线页面数
$last_online_page_count = 0;

// PHPSocketIO服务
$sender_io = new SocketIO(12223);

// 客户端发起连接事件时，设置连接socket的各种事件回调
$sender_io->on('connection', function($socket){
    global $uidConnectionMap, $last_online_count, $last_online_page_count;
    
    // 当客户端发来登录事件时触发
    $socket->on('login', function ($uid) use ($socket) {
        global $uidConnectionMap, $last_online_count, $last_online_page_count;
        
        // 已经登录过了
        if(isset($socket->uid)){
            return;
        }
        
        // 更新对应uid的在线数据
        $uid = (string)$uid;
        if(!isset($uidConnectionMap[$uid]))
        {
            $uidConnectionMap[$uid] = 0;
        }
        
        // 这个uid有++$uidConnectionMap[$uid]个socket连接
        ++$uidConnectionMap[$uid];
        // 将这个连接加入到uid分组，方便针对uid推送数据
        $socket->join($uid);
        $socket->uid = $uid;
        
        // 更新这个socket对应页面的在线数据
        $socket->emit('update_online_count', count($uidConnectionMap));
    });
    
    // 当客户端断开连接是触发（一般是关闭网页或者跳转刷新导致）
    $socket->on('disconnect', function () use ($socket) {
        global $uidConnectionMap, $last_online_count, $last_online_page_count;
        
        if(!isset($socket->uid))
        {
           return;
        }
        
        // 将uid的在线socket数减一
        if(--$uidConnectionMap[$socket->uid] <= 0)
        {
            unset($uidConnectionMap[$socket->uid]);
        }
    });
    
    // 游戏数据推送
    $socket->on('join_game', function ($game) use ($socket) {
        $socket->join($game);
        echo "User joined game: $game\n";
    });
    
    // 离开游戏房间
    $socket->on('leave_game', function ($game) use ($socket) {
        $socket->leave($game);
        echo "User left game: $game\n";
    });
});

// 定时器，定时向所有客户端推送数据
Timer::add(1, function() use ($sender_io) {
    global $uidConnectionMap, $last_online_count, $last_online_page_count;
    
    // 在线用户数
    $online_count = count($uidConnectionMap);
    // 在线页面数
    $online_page_count = array_sum($uidConnectionMap);
    
    // 只有在客户端在线数变化了才广播，减少不必要的客户端通讯
    if($last_online_count != $online_count || $last_online_page_count != $online_page_count)
    {
        $sender_io->emit('update_online_count', $online_count, $online_page_count);
        $last_online_count = $online_count;
        $last_online_page_count = $online_page_count;
    }
});

// 监听一个http端口，通过http协议推送数据给任意uid或者所有uid
$push_worker = new Worker('http://0.0.0.0:12224');
$push_worker->name = 'PushWorker';
$push_worker->count = 1;

$push_worker->onWorkerStart = function() use ($sender_io) {
    // 定时推送游戏数据
    Timer::add(3, function() use ($sender_io) {
        // 获取各游戏最新数据并推送
        $games = ['pk10', 'ssc', 'bj28', 'jnd28', 'xjp28', 'lhc', 'xyft'];
        
        foreach ($games as $game) {
            try {
                // 从缓存或数据库获取游戏数据
                $game_data = getGameData($game);
                if ($game_data) {
                    $sender_io->to($game)->emit('game_data', $game_data);
                }
            } catch (Exception $e) {
                echo "Error pushing {$game} data: " . $e->getMessage() . "\n";
            }
        }
    });
};

$push_worker->onMessage = function($connection, $request) use ($sender_io) {
    global $uidConnectionMap;
    
    // 推送数据的url格式 type=publish&content=xxxx&uid=xxxx
    // type=publish&content=xxxx 向所有uid推送
    $_GET = $request->get();
    $type = $_GET['type'] ?? '';
    $content = $_GET['content'] ?? '';
    $uid = $_GET['uid'] ?? '';
    
    switch($type) {
        case 'publish':
            if ($uid) {
                $sender_io->to($uid)->emit('new_msg', $content);
            } else {
                $sender_io->emit('new_msg', $content);
            }
            $connection->send('{"code":0,"msg":"ok"}');
            break;
            
        case 'game_update':
            $game = $_GET['game'] ?? '';
            if ($game) {
                $sender_io->to($game)->emit('game_data', json_decode($content, true));
                $connection->send('{"code":0,"msg":"ok"}');
            } else {
                $connection->send('{"code":1,"msg":"game parameter required"}');
            }
            break;
            
        default:
            $connection->send('{"code":1,"msg":"type parameter required"}');
    }
};

/**
 * 获取游戏数据
 */
function getGameData($game) {
    try {
        // 这里应该从数据库或缓存获取实际游戏数据
        // 临时返回模拟数据
        return [
            'game' => $game,
            'time' => time(),
            'current' => [
                'periodNumber' => date('YmdHi'),
                'awardTime' => date('Y-m-d H:i:s'),
                'awardNumbers' => '1,2,3,4,5'
            ],
            'next' => [
                'periodNumber' => date('YmdHi', time() + 300),
                'awardTime' => date('Y-m-d H:i:s', time() + 300),
                'awardTimeInterval' => 300000
            ]
        ];
    } catch (Exception $e) {
        return null;
    }
}

if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}