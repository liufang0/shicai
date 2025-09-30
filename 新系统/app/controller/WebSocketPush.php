<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Log;

/**
 * WebSocket实时数据推送服务
 * 负责向前端推送开奖数据、投注状态等实时信息
 */
class WebSocketPush extends Base
{
    private static $connections = [];
    private static $userConnections = [];

    /**
     * 启动WebSocket服务
     */
    public function start()
    {
        // 这个方法在实际部署时会被Workerman调用
        echo "WebSocket推送服务启动成功\n";
        
        // 设置定时器检查数据更新
        $this->startTimers();
    }

    /**
     * 启动定时器
     */
    private function startTimers()
    {
        // 每秒检查是否有新的开奖数据需要推送
        // Timer::add(1, [$this, 'checkGameUpdates']);
        
        // 每30秒推送在线用户数据
        // Timer::add(30, [$this, 'pushOnlineStats']);
        
        echo "定时器启动完成\n";
    }

    /**
     * 检查游戏数据更新
     */
    public function checkGameUpdates()
    {
        $games = ['pk10', 'ssc', 'bj28', 'jnd28'];
        
        foreach ($games as $game) {
            $this->checkSingleGame($game);
        }
    }

    /**
     * 检查单个游戏数据
     */
    private function checkSingleGame($game)
    {
        // 获取最新数据
        $latest = Db::table('caiji')
            ->where('game', $game)
            ->order('id', 'desc')
            ->find();
            
        if (!$latest) {
            return;
        }

        // 检查缓存中的版本
        $cacheKey = "game_version_{$game}";
        $lastVersion = Cache::get($cacheKey, 0);
        
        if ($latest['id'] > $lastVersion) {
            // 有新数据，推送给所有订阅此游戏的用户
            $this->pushGameData($game, $latest);
            
            // 更新缓存版本
            Cache::set($cacheKey, $latest['id']);
        }

        // 检查倒计时
        $this->pushCountdown($game, $latest);
    }

    /**
     * 推送游戏数据
     */
    private function pushGameData($game, $data)
    {
        $formatData = $this->formatGameData($game, $data);
        
        $message = json_encode([
            'type' => 'game_update',
            'game' => $game,
            'data' => $formatData,
            'timestamp' => time()
        ]);

        $this->broadcastToGame($game, $message);
        
        Log::info("推送{$game}开奖数据: 期号{$data['periodnumber']}");
    }

    /**
     * 推送倒计时
     */
    private function pushCountdown($game, $data)
    {
        $nextTime = $data['next_time'] ?? 0;
        $currentTime = time();
        $countdown = max(0, $nextTime - $currentTime);

        $message = json_encode([
            'type' => 'countdown',
            'game' => $game,
            'countdown' => $countdown,
            'next_period' => $data['next_term'] ?? 0,
            'timestamp' => $currentTime
        ]);

        $this->broadcastToGame($game, $message);
    }

    /**
     * 格式化游戏数据
     */
    private function formatGameData($game, $data)
    {
        switch ($game) {
            case 'pk10':
                return $this->formatPk10Data($data);
            case 'ssc':
                return $this->formatSscData($data);
            case 'bj28':
                return $this->formatBj28Data($data);
            case 'jnd28':
                return $this->formatJnd28Data($data);
            default:
                return $data;
        }
    }

    /**
     * 格式化PK10数据
     */
    private function formatPk10Data($data)
    {
        $numbers = explode(',', $data['awardnumbers']);
        
        return [
            'period' => $data['periodnumber'],
            'numbers' => $numbers,
            'champion' => $numbers[0] ?? 0,
            'runner_up' => $numbers[1] ?? 0,
            'third' => $numbers[2] ?? 0,
            'champion_sum' => (intval($numbers[0] ?? 0) + intval($numbers[1] ?? 0)),
            'suma_big_small' => $this->getBigSmall(intval($numbers[0] ?? 0) + intval($numbers[1] ?? 0), 11),
            'suma_odd_even' => $this->getOddEven(intval($numbers[0] ?? 0) + intval($numbers[1] ?? 0)),
            'award_time' => $data['awardtime'],
            'next_period' => $data['next_term'],
            'next_time' => $data['next_time']
        ];
    }

    /**
     * 格式化时时彩数据
     */
    private function formatSscData($data)
    {
        $numbers = explode(',', $data['awardnumbers']);
        
        return [
            'period' => $data['periodnumber'],
            'numbers' => $numbers,
            'sum' => array_sum(array_map('intval', $numbers)),
            'sum_big_small' => $this->getBigSmall(array_sum(array_map('intval', $numbers)), 22.5),
            'sum_odd_even' => $this->getOddEven(array_sum(array_map('intval', $numbers))),
            'award_time' => $data['awardtime'],
            'next_period' => $data['next_term'],
            'next_time' => $data['next_time']
        ];
    }

    /**
     * 格式化28系列数据
     */
    private function formatBj28Data($data)
    {
        return [
            'period' => $data['periodnumber'],
            'sum' => $data['tema'] ?? 0,
            'big_small' => $data['tema_dx'] ?? '',
            'odd_even' => $data['tema_ds'] ?? '',
            'segment' => $data['tema_dw'] ?? '',
            'award_time' => $data['awardtime'],
            'next_period' => $data['next_term'],
            'next_time' => $data['next_time']
        ];
    }

    /**
     * 格式化加拿大28数据
     */
    private function formatJnd28Data($data)
    {
        return $this->formatBj28Data($data); // 格式相同
    }

    /**
     * 获取大小
     */
    private function getBigSmall($value, $threshold)
    {
        return $value >= $threshold ? '大' : '小';
    }

    /**
     * 获取单双
     */
    private function getOddEven($value)
    {
        return $value % 2 == 0 ? '双' : '单';
    }

    /**
     * 向指定游戏的所有订阅者广播消息
     */
    private function broadcastToGame($game, $message)
    {
        // 在实际Workerman环境中实现
        // foreach (self::$connections as $connection) {
        //     if ($connection->game == $game) {
        //         $connection->send($message);
        //     }
        // }
        
        // 这里可以记录日志或缓存消息
        Cache::set("latest_message_{$game}", $message, 60);
    }

    /**
     * 向所有连接广播消息
     */
    private function broadcastToAll($message)
    {
        // 在实际Workerman环境中实现
        // foreach (self::$connections as $connection) {
        //     $connection->send($message);
        // }
        
        Cache::set('latest_broadcast', $message, 60);
    }

    /**
     * 向指定用户发送消息
     */
    private function sendToUser($userId, $message)
    {
        // 在实际Workerman环境中实现
        // if (isset(self::$userConnections[$userId])) {
        //     self::$userConnections[$userId]->send($message);
        // }
        
        Cache::set("user_message_{$userId}", $message, 300);
    }

    /**
     * 推送投注结果
     */
    public function pushBetResult($userId, $betId, $result)
    {
        $message = json_encode([
            'type' => 'bet_result',
            'bet_id' => $betId,
            'result' => $result,
            'timestamp' => time()
        ]);

        $this->sendToUser($userId, $message);
    }

    /**
     * 推送系统通知
     */
    public function pushSystemNotice($content, $level = 'info')
    {
        $message = json_encode([
            'type' => 'system_notice',
            'content' => $content,
            'level' => $level,
            'timestamp' => time()
        ]);

        $this->broadcastToAll($message);
    }

    /**
     * 推送在线统计
     */
    public function pushOnlineStats()
    {
        $stats = [
            'online_users' => count(self::$userConnections),
            'total_connections' => count(self::$connections),
            'games_active' => $this->getActiveGames(),
            'timestamp' => time()
        ];

        $message = json_encode([
            'type' => 'online_stats',
            'data' => $stats
        ]);

        $this->broadcastToAll($message);
    }

    /**
     * 获取活跃游戏列表
     */
    private function getActiveGames()
    {
        $games = [];
        $gameTypes = ['pk10', 'ssc', 'bj28', 'jnd28'];

        foreach ($gameTypes as $game) {
            $latest = Db::table('caiji')
                ->where('game', $game)
                ->order('id', 'desc')
                ->find();

            if ($latest && (time() - strtotime($latest['awardtime'])) < 3600) {
                $games[] = $game;
            }
        }

        return $games;
    }

    /**
     * 处理客户端连接
     */
    public function onConnect($connection)
    {
        self::$connections[] = $connection;
        echo "新连接建立，当前连接数: " . count(self::$connections) . "\n";
    }

    /**
     * 处理客户端消息
     */
    public function onMessage($connection, $data)
    {
        $message = json_decode($data, true);
        
        if (!$message) {
            return;
        }

        switch ($message['type']) {
            case 'login':
                $this->handleLogin($connection, $message);
                break;
            case 'subscribe':
                $this->handleSubscribe($connection, $message);
                break;
            case 'heartbeat':
                $this->handleHeartbeat($connection, $message);
                break;
            default:
                Log::warning("未知消息类型: " . $message['type']);
        }
    }

    /**
     * 处理用户登录
     */
    private function handleLogin($connection, $message)
    {
        $userId = $message['user_id'] ?? 0;
        $token = $message['token'] ?? '';

        // 验证token
        if ($this->validateToken($userId, $token)) {
            $connection->user_id = $userId;
            self::$userConnections[$userId] = $connection;
            
            // 发送登录成功消息
            $response = json_encode([
                'type' => 'login_result',
                'success' => true,
                'user_id' => $userId,
                'timestamp' => time()
            ]);
            
            $connection->send($response);
            
            // 推送用户相关数据
            $this->pushUserData($userId);
        } else {
            $response = json_encode([
                'type' => 'login_result',
                'success' => false,
                'msg' => '登录验证失败'
            ]);
            
            $connection->send($response);
        }
    }

    /**
     * 处理游戏订阅
     */
    private function handleSubscribe($connection, $message)
    {
        $game = $message['game'] ?? '';
        
        if (in_array($game, ['pk10', 'ssc', 'bj28', 'jnd28'])) {
            $connection->game = $game;
            
            // 立即推送该游戏的最新数据
            $latest = Db::table('caiji')
                ->where('game', $game)
                ->order('id', 'desc')
                ->find();
                
            if ($latest) {
                $this->pushGameData($game, $latest);
            }
        }
    }

    /**
     * 处理心跳
     */
    private function handleHeartbeat($connection, $message)
    {
        $response = json_encode([
            'type' => 'heartbeat_response',
            'timestamp' => time()
        ]);
        
        $connection->send($response);
    }

    /**
     * 验证用户token
     */
    private function validateToken($userId, $token)
    {
        // 简单的token验证，实际应该更严格
        $userToken = Cache::get("user_token_{$userId}");
        return $userToken && $userToken === $token;
    }

    /**
     * 推送用户数据
     */
    private function pushUserData($userId)
    {
        $user = Db::table('user')->where('id', $userId)->find();
        
        if ($user) {
            $message = json_encode([
                'type' => 'user_data',
                'data' => [
                    'money' => $user['money'],
                    'frozen_money' => $user['frozen_money'],
                    'total_bet' => $user['total_bet'],
                    'total_win' => $user['total_win']
                ],
                'timestamp' => time()
            ]);
            
            $this->sendToUser($userId, $message);
        }
    }

    /**
     * 处理连接关闭
     */
    public function onClose($connection)
    {
        // 从连接列表中移除
        foreach (self::$connections as $key => $conn) {
            if ($conn === $connection) {
                unset(self::$connections[$key]);
                break;
            }
        }

        // 从用户连接中移除
        if (isset($connection->user_id)) {
            unset(self::$userConnections[$connection->user_id]);
        }

        echo "连接关闭，当前连接数: " . count(self::$connections) . "\n";
    }

    /**
     * 获取最新游戏数据（HTTP接口）
     */
    public function getLatestData()
    {
        $game = request()->param('game', 'pk10');
        
        $data = Cache::get("latest_message_{$game}");
        
        if ($data) {
            return json(['code' => 1, 'data' => json_decode($data, true)]);
        }

        // 如果缓存没有，从数据库获取
        $latest = Db::table('caiji')
            ->where('game', $game)
            ->order('id', 'desc')
            ->find();

        if ($latest) {
            $formatData = $this->formatGameData($game, $latest);
            return json(['code' => 1, 'data' => $formatData]);
        }

        return json(['code' => 0, 'msg' => '暂无数据']);
    }
}