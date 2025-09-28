<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;

/**
 * 数据采集控制器
 * 负责各种游戏数据的采集和处理
 */
class Caiji extends BaseController
{
    protected $socket = 'websocket://0.0.0.0:15525';
    
    /**
     * Worker启动时初始化
     */
    public function onWorkerStart()
    {
        $typearr = [
            1 => 'pk10'
        ];
        
        // 添加定时器，每6秒执行一次数据采集
        $this->addTimer(6, function () {
            $this->collectData();
            file_put_contents('test.txt', time() . PHP_EOL);
        });
        
        // 自动反水功能
        if (config('is_auto_fs') == '1') {
            $this->fanshui();
        }
    }
    
    /**
     * 数据采集主逻辑
     */
    private function collectData()
    {
        // 采集PK10数据
        $this->collectPk10Data();
        
        // 采集时时彩数据
        $this->collectSscData();
        
        // 采集28数据
        $this->collect28Data();
    }
    
    /**
     * 采集PK10数据
     */
    private function collectPk10Data()
    {
        // 这里可以调用多个数据源
        $apis = [
            'http://api.api861861.com/pks/getPksHistoryList.do?date=&lotCode=10037',
            // 其他API地址
        ];
        
        foreach ($apis as $api) {
            try {
                $data = $this->httpGet($api);
                $result = json_decode($data, true);
                
                if ($result && isset($result['result'])) {
                    $this->processPk10Data($result['result']);
                }
            } catch (\Exception $e) {
                // 记录错误日志
                error_log("PK10数据采集失败: " . $e->getMessage());
            }
        }
    }
    
    /**
     * 处理PK10数据
     */
    private function processPk10Data($data)
    {
        foreach ($data as $item) {
            $periodnumber = $item['preDrawIssue'] ?? '';
            $awardNumbers = $item['preDrawCode'] ?? '';
            $awardTime = $item['preDrawTime'] ?? '';
            
            if ($periodnumber && $awardNumbers && $awardTime) {
                // 检查是否已存在
                $exists = Db::table('caiji')
                    ->where([
                        'periodnumber' => $periodnumber,
                        'game' => 'pk10'
                    ])
                    ->find();
                    
                if (!$exists) {
                    $insertData = [
                        'periodnumber' => $periodnumber,
                        'awardnumbers' => $awardNumbers,
                        'awardtime' => $awardTime,
                        'game' => 'pk10',
                        'addtime' => time()
                    ];
                    
                    Db::table('caiji')->insert($insertData);
                }
            }
        }
    }
    
    /**
     * 采集时时彩数据
     */
    private function collectSscData()
    {
        $api = 'http://api.api861861.com/CQShiCai/getBaseCQShiCaiList.do?lotCode=10060';
        
        try {
            $data = $this->httpGet($api);
            $result = json_decode($data, true);
            
            if ($result && isset($result['result'])) {
                $this->processSscData($result['result']);
            }
        } catch (\Exception $e) {
            error_log("时时彩数据采集失败: " . $e->getMessage());
        }
    }
    
    /**
     * 处理时时彩数据
     */
    private function processSscData($data)
    {
        foreach ($data as $item) {
            $periodnumber = $item['preDrawIssue'] ?? '';
            $awardNumbers = $item['preDrawCode'] ?? '';
            $awardTime = $item['preDrawTime'] ?? '';
            
            if ($periodnumber && $awardNumbers && $awardTime) {
                $exists = Db::table('caiji')
                    ->where([
                        'periodnumber' => $periodnumber,
                        'game' => 'ssc'
                    ])
                    ->find();
                    
                if (!$exists) {
                    $insertData = [
                        'periodnumber' => $periodnumber,
                        'awardnumbers' => $awardNumbers,
                        'awardtime' => $awardTime,
                        'game' => 'ssc',
                        'addtime' => time()
                    ];
                    
                    Db::table('caiji')->insert($insertData);
                }
            }
        }
    }
    
    /**
     * 采集28游戏数据
     */
    private function collect28Data()
    {
        $games = ['bj28', 'jnd28', 'xjp28'];
        
        foreach ($games as $game) {
            $this->collect28GameData($game);
        }
    }
    
    /**
     * 采集指定28游戏数据
     */
    private function collect28GameData($game)
    {
        // 根据游戏类型选择API
        $apis = [
            'bj28' => 'http://api.bj28.com/api/lottery/history',
            'jnd28' => 'http://api.jnd28.com/api/lottery/history',
            'xjp28' => 'http://api.xjp28.com/api/lottery/history'
        ];
        
        if (!isset($apis[$game])) {
            return;
        }
        
        try {
            $data = $this->httpGet($apis[$game]);
            $result = json_decode($data, true);
            
            if ($result && isset($result['data'])) {
                $this->process28Data($result['data'], $game);
            }
        } catch (\Exception $e) {
            error_log("{$game}数据采集失败: " . $e->getMessage());
        }
    }
    
    /**
     * 处理28游戏数据
     */
    private function process28Data($data, $game)
    {
        foreach ($data as $item) {
            $periodnumber = $item['issue'] ?? '';
            $awardNumbers = $item['opencode'] ?? '';
            $awardTime = $item['opentime'] ?? '';
            
            if ($periodnumber && $awardNumbers && $awardTime) {
                $exists = Db::table('caiji')
                    ->where([
                        'periodnumber' => $periodnumber,
                        'game' => $game
                    ])
                    ->find();
                    
                if (!$exists) {
                    $insertData = [
                        'periodnumber' => $periodnumber,
                        'awardnumbers' => $awardNumbers,
                        'awardtime' => $awardTime,
                        'game' => $game,
                        'addtime' => time()
                    ];
                    
                    Db::table('caiji')->insert($insertData);
                }
            }
        }
    }
    
    /**
     * 自动反水功能
     */
    private function fanshui()
    {
        // 每分钟检查一次是否需要反水
        $this->addTimer(60, function () {
            if (config('is_auto_fs') == '1') {
                $this->processAutoRebate();
            }
        });
    }
    
    /**
     * 处理自动反水
     */
    private function processAutoRebate()
    {
        // 获取需要反水的用户
        $users = Db::table('user')
            ->where('rebate_rate', '>', 0)
            ->select();
            
        foreach ($users as $user) {
            // 计算用户的投注金额和反水金额
            $betAmount = $this->getUserTodayBetAmount($user['id']);
            $rebateAmount = $betAmount * ($user['rebate_rate'] / 100);
            
            if ($rebateAmount > 0) {
                // 给用户加反水
                Db::table('user')
                    ->where('id', $user['id'])
                    ->inc('money', $rebateAmount);
                    
                // 记录反水记录
                Db::table('rebate')->insert([
                    'userid' => $user['id'],
                    'amount' => $rebateAmount,
                    'bet_amount' => $betAmount,
                    'rate' => $user['rebate_rate'],
                    'create_time' => time()
                ]);
            }
        }
    }
    
    /**
     * 获取用户今日投注金额
     */
    private function getUserTodayBetAmount($userid)
    {
        $beginToday = mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'));
        $endToday = mktime(0, 0, 0, (int)date('m'), (int)date('d') + 1, (int)date('Y')) - 1;
        
        return Db::table('order')
            ->where([
                ['userid', '=', $userid],
                ['time', '>=', $beginToday],
                ['time', '<=', $endToday],
                ['state', '=', 1]
            ])
            ->sum('del_points') ?: 0;
    }
    
    /**
     * 添加定时器（伪代码，实际需要Workerman环境）
     */
    private function addTimer($interval, $callback)
    {
        // 在实际部署时，这里需要使用 \Workerman\Lib\Timer::add($interval, $callback);
        // 现在只是记录逻辑
        // \Workerman\Lib\Timer::add($interval, $callback);
    }
    
    /**
     * HTTP GET请求
     */
    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        if (strpos($url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        
        curl_close($ch);
        return $result;
    }
}