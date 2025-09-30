<?php
declare(strict    /**
     * Worker启动时的初始化
     */
    public function onWorkerStart()
    {
        // 授权检查
        $auth = auth_check(config('auth_code'), config('siteurl'));
        
        $beginToday = strtotime('00:00:00');
        $endToday = strtotime("23:59:59");
        
        // 设置定时器，每秒检查一次
        Timer::add(1, function() {
            $this->checkAndPushData();
        });
        
        // 设置数据采集定时器，每30秒检查一次是否需要采集新数据
        Timer::add(30, function() {
            $this->autoCollect();
        });
        
        $caiji = Db::table('caiji')
            ->where("game", 'pk10')
            ->order("id desc")
            ->limit(1)
            ->find();
            
        $data = pk10_format($caiji);ace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Config;
use Workerman\Worker;
use Workerman\Timer;

/**
 * Workerman 幸运飞艇 WebSocket 控制器
 * 负责幸运飞艇游戏的WebSocket服务和实时数据推送
 */
class Workerman幸运飞艇 extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15531';
    protected $game = '幸运飞艇';
    
    /**
     * Worker启动时的初始化
     */
    public function onWorkerStart()
    {
        // 授权检查
        $auth = auth_check(config('auth_code'), config('siteurl'));
        
        $beginToday = strtotime('00:00:00');
        $endToday = strtotime("23:59:59");
        
        $caiji = Db::table('caiji')
            ->where("game", '幸运飞艇')
            ->order("id desc")
            ->limit(1)
            ->find();
            
        $data = 幸运飞艇_format($caiji);
        $time_interval = 1;
        $pkdata = json_decode($data, true);
        $nexttime = 75 + strtotime($pkdata['next']['awardTime']);
        
        // 设置幸运飞艇状态
        if (true) { // 原逻辑条件：$nexttime-time()>config('幸运飞艇_stop_time') && $nexttime-time()<288 && time()>$beginToday && time()<$endToday
            Cache::set('幸运飞艇_state', 1);
            $this->setConfig('幸运飞艇_state', 1);
        } else {
            Cache::set('幸运飞艇_state', 0);
            $this->setConfig('幸运飞艇_state', 0);
        }
        
        if (!Cache::get('幸运飞艇data')) {
            Cache::set('幸运飞艇data', $pkdata);
        }
        
        if (!Cache::get('is_send')) {
            Cache::set('is_send', 1);
        }
        
        // 这里需要集成 Workerman Timer，在实际部署时需要安装 Workerman
        $this->addTimer($time_interval);
    }
    
    /**
     * 添加定时器处理开奖逻辑
     */
    protected function addTimer($interval)
    {
        // 在实际项目中，这里需要使用 Workerman\Lib\Timer::add
        // 现在用伪代码表示逻辑
        
        /*
        \Workerman\Lib\Timer::add($interval, function(){
            $beginToday = strtotime('00:00:00');
            $endToday = strtotime("23:59:59");
            
            Cache::set('game', '幸运飞艇');
            $幸运飞艇data = Cache::get('幸运飞艇data');
            $next_time = 75 + strtotime($幸运飞艇data['next']['awardTime']);
            $awardtime = $幸运飞艇data['current']['awardTime'];
            
            if (true) { // 游戏开放条件
                Cache::set('幸运飞艇_state', 1);
                $this->setConfig('幸运飞艇_state', 1);
            } else {
                Cache::set('幸运飞艇_state', 0);
                $this->setConfig('幸运飞艇_state', 0);
            }
            
            // 处理开奖逻辑
            $this->processDrawing($幸运飞艇data, $next_time);
        });
        */
    }
    
    /**
     * 处理开奖逻辑
     */
    protected function processDrawing($幸运飞艇data, $next_time)
    {
        // 获取最新数据
        $caiji = Db::table('caiji')
            ->where("game", '幸运飞艇')
            ->order("id desc")
            ->limit(1)
            ->find();
            
        if ($caiji) {
            $newData = 幸运飞艇_format($caiji);
            $newPkdata = json_decode($newData, true);
            
            // 更新缓存
            Cache::set('幸运飞艇data', $newPkdata);
            
            // 推送数据到所有连接的客户端
            $this->broadcastToAll($newData);
        }
    }
    
    /**
     * 广播数据到所有客户端
     */
    protected function broadcastToAll($data)
    {
        // 在实际项目中，这里需要遍历所有WebSocket连接
        // 现在用伪代码表示
        /*
        foreach($this->connections as $connection) {
            $connection->send($data);
        }
        */
    }
    
    /**
     * WebSocket连接建立时
     */
    public function onConnect($connection)
    {
        echo "新连接建立\n";
    }
    
    /**
     * 接收到消息时
     */
    public function onMessage($connection, $data)
    {
        // 处理客户端发送的消息
        $message = json_decode($data, true);
        
        switch ($message['type']) {
            case 'get幸运飞艇Data':
                $幸运飞艇data = Cache::get('幸运飞艇data');
                $connection->send(json_encode($幸运飞艇data));
                break;
            case 'bet':
                // 处理投注逻辑
                $this->handleBet($connection, $message);
                break;
        }
    }
    
    /**
     * 连接关闭时
     */
    public function onClose($connection)
    {
        echo "连接关闭\n";
    }
    
    /**
     * 设置配置
     */
    protected function setConfig($key, $value)
    {
        // 更新数据库配置
        Db::table('config')->where('key', $key)->update(['value' => $value]);
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
}