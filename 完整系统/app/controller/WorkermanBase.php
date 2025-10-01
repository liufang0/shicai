<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;

/**
 * Workerman WebSocket 基础控制器
 */
abstract class WorkermanBase extends BaseController
{
    protected $socket;
    protected $game;
    
    /**
     * Worker启动时的通用初始化
     */
    public function onWorkerStart()
    {
        $this->initializeGame();
        $this->startTimer();
    }
    
    /**
     * 初始化游戏
     */
    abstract protected function initializeGame();
    
    /**
     * 启动定时器
     */
    protected function startTimer()
    {
        // 主定时器，每秒执行
        $this->addTimer(1, function() {
            $this->processGameLogic();
        });
    }
    
    /**
     * 处理游戏逻辑
     */
    protected function processGameLogic()
    {
        $gameData = $this->getLatestGameData();
        
        if ($gameData) {
            $this->updateGameCache($gameData);
            $this->broadcastToClients($gameData);
        }
    }
    
    /**
     * 获取最新游戏数据
     */
    protected function getLatestGameData()
    {
        $caiji = Db::table('caiji')
            ->where('game', $this->game)
            ->order('id desc')
            ->limit(1)
            ->find();
            
        if (!$caiji) {
            return null;
        }
        
        return $this->formatGameData($caiji);
    }
    
    /**
     * 格式化游戏数据
     */
    abstract protected function formatGameData($caiji);
    
    /**
     * 更新游戏缓存
     */
    protected function updateGameCache($gameData)
    {
        Cache::set($this->game . '_data', $gameData);
        Cache::set($this->game . '_state', $this->getGameState());
    }
    
    /**
     * 获取游戏状态
     */
    protected function getGameState()
    {
        $beginToday = strtotime('00:00:00');
        $endToday = strtotime("23:59:59");
        $currentTime = time();
        
        // 默认游戏开放状态逻辑
        return ($currentTime > $beginToday && $currentTime < $endToday) ? 1 : 0;
    }
    
    /**
     * 广播给所有客户端
     */
    protected function broadcastToClients($data)
    {
        // 在实际Workerman环境中实现
        // foreach($this->connections as $connection) {
        //     $connection->send(json_encode($data));
        // }
    }
    
    /**
     * WebSocket连接建立时
     */
    public function onConnect($connection)
    {
        echo "新的{$this->game}连接建立\n";
    }
    
    /**
     * 接收到消息时
     */
    public function onMessage($connection, $data)
    {
        $message = json_decode($data, true);
        
        switch ($message['type']) {
            case 'getGameData':
                $gameData = Cache::get($this->game . '_data');
                $this->sendToClient($connection, $gameData);
                break;
            case 'bet':
                $this->handleBet($connection, $message);
                break;
        }
    }
    
    /**
     * 处理投注
     */
    protected function handleBet($connection, $message)
    {
        // 投注逻辑处理
        // 验证用户、金额、期号等
        // 记录投注数据
        // 返回投注结果
    }
    
    /**
     * 连接关闭时
     */
    public function onClose($connection)
    {
        echo "{$this->game}连接关闭\n";
    }
    
    /**
     * 发送消息给客户端
     */
    protected function sendToClient($connection, $data)
    {
        // $connection->send(json_encode($data));
    }
    
    /**
     * 添加定时器（需要Workerman环境）
     */
    protected function addTimer($interval, $callback)
    {
        // \Workerman\Lib\Timer::add($interval, $callback);
    }
}