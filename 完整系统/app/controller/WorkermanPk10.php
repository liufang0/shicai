<?php<?php

declare(strict_types=1);declare(strict_types=1);



namespace app\controller;namespace app\controller;



use think\facade\Db;use think\facade\Db;

use think\facade\Cache;use think\facade\Cache;

use think\facade\Config;use think\facade\Config;

use Workerman\Worker;use Workerman\Worker;

use Workerman\Timer;use Workerman\Timer;



/**/**

 * Workerman PK10 WebSocket 控制器 * Workerman PK10 WebSocket 控制器

 * 负责PK10游戏的WebSocket服务和实时数据推送 * 负责PK10游戏的WebSocket服务和实时数据推送

 */ */

class WorkermanPk10class WorkermanPk10

{{

    protected $socket = 'websocket://0.0.0.0:15530';    protected $socket = 'websocket://0.0.0.0:15530';

    protected $game = 'pk10';    protected $game = 'pk10';

        

    /**    /**

     * Worker启动时的初始化     * Worker启动时的初始化

     */     */

    public function onWorkerStart()    public function onWorkerStart()

    {    {

        $beginToday = strtotime('00:00:00');        // 授权检查

        $endToday = strtotime("23:59:59");        $auth = auth_check(config('auth_code'), config('siteurl'));

                

        // 设置定时器，每秒检查一次        $beginToday = strtotime('00:00:00');

        Timer::add(1, function() {        $endToday = strtotime("23:59:59");

            $this->checkAndPushData();        

        });        // 设置定时器，每秒检查一次

                Timer::add(1, function() {

        // 设置数据采集定时器，每30秒检查一次是否需要采集新数据            $this->checkAndPushData();

        Timer::add(30, function() {        });

            $this->autoCollect();        

        });        // 设置数据采集定时器，每30秒检查一次是否需要采集新数据

                Timer::add(30, function() {

        $caiji = Db::table('caiji')            $this->autoCollect();

            ->where("game", 'pk10')        });

            ->order("id desc")        

            ->limit(1)        $caiji = Db::table('caiji')

            ->find();            ->where("game", 'pk10')

                        ->order("id desc")

        $data = $this->pk10_format($caiji);            ->limit(1)

        $time_interval = 1;            ->find();

        $pkdata = json_decode($data, true);            

                $data = $this->pk10_format($caiji);

        // 设置PK10状态        $time_interval = 1;

        Cache::set('pk10_state', 1);        $pkdata = json_decode($data, true);

        $this->setConfig('pk10_state', 1);        $nexttime = 75 + strtotime($pkdata['next']['awardTime']);

                

        if (!Cache::get('pk10data')) {        // 设置PK10状态

            Cache::set('pk10data', $pkdata);        if (true) { // 原逻辑条件：$nexttime-time()>config('pk10_stop_time') && $nexttime-time()<288 && time()>$beginToday && time()<$endToday

        }            Cache::set('pk10_state', 1);

                    $this->setConfig('pk10_state', 1);

        if (!Cache::get('is_send')) {        } else {

            Cache::set('is_send', 1);            Cache::set('pk10_state', 0);

        }            $this->setConfig('pk10_state', 0);

    }        }

            

    /**        if (!Cache::get('pk10data')) {

     * 检查并推送数据            Cache::set('pk10data', $pkdata);

     */        }

    protected function checkAndPushData()        

    {        if (!Cache::get('is_send')) {

        $beginToday = strtotime('00:00:00');            Cache::set('is_send', 1);

        $endToday = strtotime("23:59:59");        }

                

        Cache::set('game', 'pk10');        // 这里需要集成 Workerman Timer，在实际部署时需要安装 Workerman

        $pk10data = Cache::get('pk10data');        $this->addTimer($time_interval);

            }

        if ($pk10data) {    

            // 处理开奖逻辑    /**

            $this->processDrawing($pk10data);     * Worker启动时的初始化

        }     */

    }    public function onWorkerStart()

        {

    /**        // 授权检查

     * 自动采集数据        $auth = auth_check(config('auth_code'), config('siteurl'));

     */        

    protected function autoCollect()        $beginToday = strtotime('00:00:00');

    {        $endToday = strtotime("23:59:59");

        // 数据采集逻辑        

        $caiji = Db::table('caiji')        $caiji = Db::table('caiji')

            ->where("game", 'pk10')            ->where("game", '幸运飞艇')

            ->order("id desc")            ->order("id desc")

            ->limit(1)            ->limit(1)

            ->find();            ->find();

                        

        if ($caiji) {        $data = 幸运飞艇_format($caiji);

            $newData = $this->pk10_format($caiji);        $time_interval = 1;

            $newPkdata = json_decode($newData, true);        $pkdata = json_decode($data, true);

                    $nexttime = 75 + strtotime($pkdata['next']['awardTime']);

            // 更新缓存        

            Cache::set('pk10data', $newPkdata);        // 设置幸运飞艇状态

        }        if (true) { // 原逻辑条件：$nexttime-time()>config('幸运飞艇_stop_time') && $nexttime-time()<288 && time()>$beginToday && time()<$endToday

    }            Cache::set('幸运飞艇_state', 1);

                $this->setConfig('幸运飞艇_state', 1);

    /**        } else {

     * 处理开奖逻辑            Cache::set('幸运飞艇_state', 0);

     */            $this->setConfig('幸运飞艇_state', 0);

    protected function processDrawing($pk10data)        }

    {        

        // 获取最新数据        if (!Cache::get('幸运飞艇data')) {

        $caiji = Db::table('caiji')            Cache::set('幸运飞艇data', $pkdata);

            ->where("game", 'pk10')        }

            ->order("id desc")        

            ->limit(1)        if (!Cache::get('is_send')) {

            ->find();            Cache::set('is_send', 1);

                    }

        if ($caiji) {        

            $newData = $this->pk10_format($caiji);        // 这里需要集成 Workerman Timer，在实际部署时需要安装 Workerman

            $newPkdata = json_decode($newData, true);        $this->addTimer($time_interval);

                }

            // 更新缓存    

            Cache::set('pk10data', $newPkdata);    /**

                 * 添加定时器处理开奖逻辑

            // 推送数据到所有连接的客户端     */

            $this->broadcastToAll($newData);    protected function addTimer($interval)

        }    {

    }        // 在实际项目中，这里需要使用 Workerman\Lib\Timer::add

            // 现在用伪代码表示逻辑

    /**        

     * PK10数据格式化        /*

     */        \Workerman\Lib\Timer::add($interval, function(){

    protected function pk10_format($caiji)            $beginToday = strtotime('00:00:00');

    {            $endToday = strtotime("23:59:59");

        if (!$caiji) {            

            return json_encode(['error' => 'No data found']);            Cache::set('game', '幸运飞艇');

        }            $幸运飞艇data = Cache::get('幸运飞艇data');

                    $next_time = 75 + strtotime($幸运飞艇data['next']['awardTime']);

        return json_encode([            $awardtime = $幸运飞艇data['current']['awardTime'];

            'current' => [            

                'expect' => $caiji['expect'] ?? '',            if (true) { // 游戏开放条件

                'opencode' => $caiji['opencode'] ?? '',                Cache::set('幸运飞艇_state', 1);

                'awardTime' => $caiji['opentime'] ?? ''                $this->setConfig('幸运飞艇_state', 1);

            ],            } else {

            'next' => [                Cache::set('幸运飞艇_state', 0);

                'expect' => ($caiji['expect'] ?? 0) + 1,                $this->setConfig('幸运飞艇_state', 0);

                'awardTime' => date('Y-m-d H:i:s', strtotime($caiji['opentime'] ?? 'now') + 300) // 5分钟后            }

            ]            

        ]);            // 处理开奖逻辑

    }            $this->processDrawing($幸运飞艇data, $next_time);

            });

    /**        */

     * 广播数据到所有客户端    }

     */    

    protected function broadcastToAll($data)    /**

    {     * 处理开奖逻辑

        // 在实际项目中，这里需要遍历所有WebSocket连接     */

        echo "Broadcasting data: " . $data . "\n";    protected function processDrawing($幸运飞艇data, $next_time)

    }    {

            // 获取最新数据

    /**        $caiji = Db::table('caiji')

     * WebSocket连接建立时            ->where("game", '幸运飞艇')

     */            ->order("id desc")

    public function onConnect($connection)            ->limit(1)

    {            ->find();

        echo "新连接建立\n";            

    }        if ($caiji) {

                $newData = 幸运飞艇_format($caiji);

    /**            $newPkdata = json_decode($newData, true);

     * 接收到消息时            

     */            // 更新缓存

    public function onMessage($connection, $data)            Cache::set('幸运飞艇data', $newPkdata);

    {            

        // 处理客户端发送的消息            // 推送数据到所有连接的客户端

        $message = json_decode($data, true);            $this->broadcastToAll($newData);

                }

        switch ($message['type'] ?? '') {    }

            case 'getPk10Data':    

                $pk10data = Cache::get('pk10data');    /**

                $connection->send(json_encode($pk10data));     * 广播数据到所有客户端

                break;     */

            case 'bet':    protected function broadcastToAll($data)

                // 处理投注逻辑    {

                $this->handleBet($connection, $message);        // 在实际项目中，这里需要遍历所有WebSocket连接

                break;        // 现在用伪代码表示

        }        /*

    }        foreach($this->connections as $connection) {

                $connection->send($data);

    /**        }

     * 连接关闭时        */

     */    }

    public function onClose($connection)    

    {    /**

        echo "连接关闭\n";     * WebSocket连接建立时

    }     */

        public function onConnect($connection)

    /**    {

     * 设置配置        echo "新连接建立\n";

     */    }

    protected function setConfig($key, $value)    

    {    /**

        // 更新数据库配置     * 接收到消息时

        try {     */

            Db::table('config')->where('key', $key)->update(['value' => $value]);    public function onMessage($connection, $data)

        } catch (\Exception $e) {    {

            echo "Config update failed: " . $e->getMessage() . "\n";        // 处理客户端发送的消息

        }        $message = json_decode($data, true);

    }        

            switch ($message['type']) {

    /**            case 'get幸运飞艇Data':

     * 处理投注                $幸运飞艇data = Cache::get('幸运飞艇data');

     */                $connection->send(json_encode($幸运飞艇data));

    protected function handleBet($connection, $message)                break;

    {            case 'bet':

        // 投注逻辑处理                // 处理投注逻辑

        // 验证用户、金额、期号等                $this->handleBet($connection, $message);

        // 记录投注数据                break;

        // 返回投注结果        }

        $connection->send(json_encode(['status' => 'bet_received', 'message' => '投注已接收']));    }

    }    

}    /**
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