<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/tp6_framework/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Timer;
use think\facade\Db;
use think\facade\Config;
use app\controller\WorkermanPk10;
use app\controller\WorkermanSsc;
use app\controller\WorkermanXyft;
use app\controller\WorkermanLhc;
use app\controller\WorkermanK3;
use app\controller\WorkermanBj28;
use app\controller\WorkermanJnd28;

// 初始化ThinkPHP框架
\think\App::getInstance()->initialize();

// 游戏服务器配置
$game_servers = [
    'pk10' => [
        'class' => WorkermanPk10::class,
        'socket' => 'websocket://0.0.0.0:15531',
        'count' => 2
    ],
    'ssc' => [
        'class' => WorkermanSsc::class,
        'socket' => 'websocket://0.0.0.0:15532',
        'count' => 2
    ],
    'xyft' => [
        'class' => WorkermanXyft::class,
        'socket' => 'websocket://0.0.0.0:15537',
        'count' => 2
    ],
    'lhc' => [
        'class' => WorkermanLhc::class,
        'socket' => 'websocket://0.0.0.0:15533',
        'count' => 2
    ],
    'k3' => [
        'class' => WorkermanK3::class,
        'socket' => 'websocket://0.0.0.0:15538',
        'count' => 2
    ],
    'bj28' => [
        'class' => WorkermanBj28::class,
        'socket' => 'websocket://0.0.0.0:15534',
        'count' => 2
    ],
    'jnd28' => [
        'class' => WorkermanJnd28::class,
        'socket' => 'websocket://0.0.0.0:15535',
        'count' => 2
    ]
];

// 创建游戏服务器实例
$workers = [];

foreach ($game_servers as $game => $config) {
    echo "Starting {$game} server on {$config['socket']}...\n";
    
    // 创建WebSocket服务器
    $worker = new Worker($config['socket']);
    $worker->name = "GameServer_{$game}";
    $worker->count = $config['count'];
    
    // 游戏控制器实例
    $game_controller = null;
    
    $worker->onWorkerStart = function($worker) use ($config, $game, &$game_controller) {
        echo "Worker {$worker->name} started\n";
        
        // 初始化游戏控制器
        $class = $config['class'];
        $game_controller = new $class();
        $game_controller->initialize();
        
        // 设置定时器：每5秒检查开奖
        Timer::add(5, function() use ($game_controller) {
            try {
                $game_controller->checkGameResult();
            } catch (\Throwable $e) {
                echo "Game result check error: " . $e->getMessage() . "\n";
            }
        });
        
        // 设置定时器：每30秒清理连接
        Timer::add(30, function() use ($game_controller) {
            try {
                $game_controller->cleanupConnections();
            } catch (\Throwable $e) {
                echo "Cleanup error: " . $e->getMessage() . "\n";
            }
        });
    };
    
    $worker->onConnect = function($connection) use ($game, &$game_controller) {
        echo "New connection for {$game}: {$connection->id}\n";
        if ($game_controller) {
            $game_controller->onConnect($connection);
        }
    };
    
    $worker->onMessage = function($connection, $data) use ($game, &$game_controller) {
        echo "Message from {$game} client {$connection->id}: {$data}\n";
        if ($game_controller) {
            $game_controller->onMessage($connection, $data);
        }
    };
    
    $worker->onClose = function($connection) use ($game, &$game_controller) {
        echo "Connection closed for {$game}: {$connection->id}\n";
        if ($game_controller) {
            $game_controller->onClose($connection);
        }
    };
    
    $worker->onError = function($connection, $code, $msg) use ($game) {
        echo "Error in {$game} server: {$code} {$msg}\n";
    };
    
    $workers[$game] = $worker;
}

// 主监控进程
$monitor = new Worker();
$monitor->name = "GameMonitor";
$monitor->count = 1;

$monitor->onWorkerStart = function($worker) {
    echo "Game Monitor started\n";
    
    // 每分钟统计一次在线用户
    Timer::add(60, function() {
        try {
            $online_count = 0;
            foreach (\Workerman\Worker::getAllWorkers() as $worker) {
                if (isset($worker->connections)) {
                    $online_count += count($worker->connections);
                }
            }
            
            // 更新在线人数统计
            Db::name('statistics')->where('type', 'online')->update([
                'value' => $online_count,
                'update_time' => time()
            ]);
            
            echo "Online users: {$online_count}\n";
        } catch (\Throwable $e) {
            echo "Monitor error: " . $e->getMessage() . "\n";
        }
    });
    
    // 每小时备份关键数据
    Timer::add(3600, function() {
        try {
            // 备份当日投注数据
            $today = date('Y-m-d');
            $bet_count = Db::name('user_played')->where('time', '>=', strtotime($today))->count();
            echo "Today's bet count: {$bet_count}\n";
        } catch (\Throwable $e) {
            echo "Backup error: " . $e->getMessage() . "\n";
        }
    });
};

echo "\n=== Game Server Manager Starting ===\n";
echo "Available games: " . implode(', ', array_keys($game_servers)) . "\n";
echo "Monitor process: GameMonitor\n";
echo "==========================================\n\n";

// 启动所有服务器
Worker::runAll();