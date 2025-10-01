<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Log;

/**
 * 开奖数据采集控制器
 * 负责从外部API采集开奖数据并处理
 */
class Collect extends Base
{
    // 第三方API配置
    private $apiConfig = [
        'pk10' => [
            'url' => 'http://api.api861861.com/pks/getPksHistoryList.do?date=&lotCode=10037',
            'interval' => 300, // 5分钟一期
            'backup_url' => 'http://api.api68.com/pks/getPksHistoryList.do?lotCode=10037'
        ],
        'ssc' => [
            'url' => 'http://api.cpkjapi.com/json?t=jisussc&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
            'interval' => 600, // 10分钟一期
            'backup_url' => ''
        ],
        'bj28' => [
            'url' => 'http://api.xxxx.com/bj28',
            'interval' => 210, // 3.5分钟一期
            'backup_url' => ''
        ],
        'jnd28' => [
            'url' => 'http://api.xxxx.com/jnd28', 
            'interval' => 210, // 3.5分钟一期
            'backup_url' => ''
        ]
    ];

    /**
     * 开奖数据采集主入口
     */
    public function index()
    {
        $games = ['pk10', 'ssc', 'bj28', 'jnd28'];
        
        foreach ($games as $game) {
            try {
                $this->collectGameData($game);
            } catch (\Exception $e) {
                Log::error("采集{$game}数据失败: " . $e->getMessage());
            }
        }
        
        return json(['code' => 1, 'msg' => '采集完成']);
    }

    /**
     * 采集单个游戏数据
     */
    private function collectGameData($game)
    {
        // 检查游戏状态
        $gameConfig = Db::table('game_config')
            ->where('game', $game)
            ->where('config_key', 'status')
            ->value('config_value');
            
        if (!$gameConfig) {
            return false;
        }

        // 获取最后一期数据
        $lastRecord = Db::table('caiji')
            ->where('game', $game)
            ->order('id', 'desc')
            ->find();

        $currentTime = time();
        $shouldCollect = false;

        if (!$lastRecord) {
            $shouldCollect = true;
        } else {
            $nextTime = $lastRecord['next_time'] ?? 0;
            if ($currentTime >= $nextTime) {
                $shouldCollect = true;
            }
        }

        if (!$shouldCollect) {
            return false;
        }

        // 采集数据 - 优先从API获取，失败则生成模拟数据
        $apiData = $this->fetchApiData($game);
        if (!$apiData) {
            // API失败，生成模拟数据
            $apiData = $this->generateMockData($game);
        }

        // 处理数据
        $this->processApiData($game, $apiData, $lastRecord);
        
        return true;
    }

    /**
     * 从API获取数据
     */
    private function fetchApiData($game)
    {
        if (!isset($this->apiConfig[$game])) {
            return false;
        }

        $config = $this->apiConfig[$game];
        
        // 尝试主API
        $data = $this->httpGet($config['url']);
        if (!$data && !empty($config['backup_url'])) {
            // 尝试备用API
            $data = $this->httpGet($config['backup_url']);
        }

        return $data ? json_decode($data, true) : false;
    }

    /**
     * 处理API数据
     */
    private function processApiData($game, $apiData, $lastRecord)
    {
        // 根据不同游戏处理数据格式
        switch ($game) {
            case 'pk10':
                $this->processPk10Data($apiData, $lastRecord);
                break;
            case 'ssc':
                $this->processSscData($apiData, $lastRecord);
                break;
            case 'bj28':
                $this->processBj28Data($apiData, $lastRecord);
                break;
            case 'jnd28':
                $this->processJnd28Data($apiData, $lastRecord);
                break;
        }
    }

    /**
     * 处理PK10数据
     */
    private function processPk10Data($apiData, $lastRecord)
    {
        if (!isset($apiData['result']['data'][0])) {
            return false;
        }

        $data = $apiData['result']['data'][0];
        $periodNumber = $data['preDrawIssue'];
        
        // 检查期号是否已存在
        $exists = Db::table('caiji')
            ->where('game', 'pk10')
            ->where('periodnumber', $periodNumber)
            ->find();
            
        if ($exists) {
            return false;
        }

        // 检查是否有管理员预设开奖号码
        $adminControl = Db::table('admin_control')
            ->where('game', 'pk10')
            ->where('periodnumber', $periodNumber)
            ->where('is_used', 0)
            ->find();

        $awardNumbers = '';
        if ($adminControl && $adminControl['awardnumbers']) {
            // 使用管理员预设号码
            $awardNumbers = $adminControl['awardnumbers'];
            
            // 标记为已使用
            Db::table('admin_control')
                ->where('id', $adminControl['id'])
                ->update(['is_used' => 1]);
        } else {
            // 使用API数据或生成随机号码
            if (isset($data['preDrawCode'])) {
                $awardNumbers = implode(',', $data['preDrawCode']);
            } else {
                // 生成PK10随机号码 (1-10，10个号码)
                $numbers = range(1, 10);
                shuffle($numbers);
                $awardNumbers = implode(',', $numbers);
            }
        }

        // 计算和值等统计数据
        $numbersArray = explode(',', $awardNumbers);
        $tema = array_sum(array_slice($numbersArray, 0, 3)); // 冠亚季和
        $tema_dx = $tema > 13 ? '大' : '小';
        $tema_ds = $tema % 2 == 0 ? '双' : '单';

        // 计算下期时间
        $nextTime = time() + $this->apiConfig['pk10']['interval'];
        $nextPeriod = $periodNumber + 1;

        // 插入采集表
        $insertData = [
            'game' => 'pk10',
            'periodnumber' => $periodNumber,
            'awardnumbers' => $awardNumbers,
            'awardtime' => $data['preDrawTime'] ?? date('Y-m-d H:i:s'),
            'addtime' => time(),
            'next_term' => $nextPeriod,
            'next_time' => $nextTime,
            'tema' => $tema,
            'tema_dx' => $tema_dx,
            'tema_ds' => $tema_ds,
            'status' => 1
        ];

        $caijiId = Db::table('caiji')->insertGetId($insertData);

        if ($caijiId) {
            // 同时插入历史记录表
            $numberData = [
                'game' => 'pk10',
                'periodnumber' => $periodNumber,
                'awardnumbers' => $awardNumbers,
                'awardtime' => $insertData['awardtime'],
                'tema' => $tema,
                'tema_dx' => $tema_dx,
                'tema_ds' => $tema_ds,
                'addtime' => time()
            ];

            Db::table('number')->insert($numberData);

            // 更新缓存
            Cache::set('pk10_latest', $insertData);
            
            // 处理投注结算
            $this->settleBets('pk10', $periodNumber, $awardNumbers);
            
            // 记录日志
            Log::info("PK10期号{$periodNumber}采集成功，开奖号码：{$awardNumbers}");
        }

        return $caijiId > 0;
    }

    /**
     * 处理时时彩数据
     */
    private function processSscData($apiData, $lastRecord)
    {
        // 类似PK10的处理逻辑，但针对时时彩5位数字
        // 实现略...
        return true;
    }

    /**
     * 处理北京28数据
     */
    private function processBj28Data($apiData, $lastRecord)
    {
        // 类似的处理逻辑，但针对28系列游戏
        // 实现略...
        return true;
    }

    /**
     * 处理加拿大28数据
     */
    private function processJnd28Data($apiData, $lastRecord)
    {
        // 类似的处理逻辑
        // 实现略...
        return true;
    }

    /**
     * 投注结算
     */
    private function settleBets($game, $periodNumber, $awardNumbers)
    {
        // 获取该期所有投注
        $bets = Db::table('bet')
            ->where('game', $game)
            ->where('periodnumber', $periodNumber)
            ->where('status', 0)
            ->select();

        if (empty($bets)) {
            return;
        }

        $numbersArray = explode(',', $awardNumbers);
        
        foreach ($bets as $bet) {
            $winAmount = 0;
            $status = 2; // 默认未中奖

            // 根据投注类型计算是否中奖
            $winAmount = $this->calculateWin($game, $bet, $numbersArray);
            
            if ($winAmount > 0) {
                $status = 1; // 中奖
                
                // 更新用户余额
                Db::table('user')
                    ->where('id', $bet['user_id'])
                    ->inc('money', $winAmount)
                    ->inc('total_win', $winAmount);
            }

            // 更新投注记录
            Db::table('bet')
                ->where('id', $bet['id'])
                ->update([
                    'win_amount' => $winAmount,
                    'status' => $status,
                    'settle_time' => date('Y-m-d H:i:s')
                ]);
        }
    }

    /**
     * 计算中奖金额
     */
    private function calculateWin($game, $bet, $numbersArray)
    {
        $winAmount = 0;
        
        // 根据游戏类型和投注类型计算
        switch ($game) {
            case 'pk10':
                $winAmount = $this->calculatePk10Win($bet, $numbersArray);
                break;
            case 'ssc':
                $winAmount = $this->calculateSscWin($bet, $numbersArray);
                break;
            // 其他游戏类型...
        }
        
        return $winAmount;
    }

    /**
     * 计算PK10中奖
     */
    private function calculatePk10Win($bet, $numbersArray)
    {
        $betType = $bet['bet_type'];
        $betContent = $bet['bet_content'];
        $betAmount = $bet['bet_amount'];
        $odds = $bet['odds'];

        switch ($betType) {
            case 'champion': // 冠军
                if ($numbersArray[0] == $betContent) {
                    return $betAmount * $odds;
                }
                break;
            case 'runner_up': // 亚军
                if ($numbersArray[1] == $betContent) {
                    return $betAmount * $odds;
                }
                break;
            case 'third': // 季军
                if ($numbersArray[2] == $betContent) {
                    return $betAmount * $odds;
                }
                break;
            case 'champion_sum': // 冠亚和
                $sum = intval($numbersArray[0]) + intval($numbersArray[1]);
                if ($sum == intval($betContent)) {
                    return $betAmount * $odds;
                }
                break;
            // 更多投注类型...
        }

        return 0;
    }

    /**
     * 计算时时彩中奖
     */
    private function calculateSscWin($bet, $numbersArray)
    {
        // 时时彩中奖计算逻辑
        // 实现略...
        return 0;
    }

    /**
     * HTTP GET请求
     */
    private function httpGet($url, $timeout = 10)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200) {
                return $result;
            }
        } catch (\Exception $e) {
            Log::error("HTTP请求失败: " . $e->getMessage());
        }

        return false;
    }

    /**
     * 生成模拟数据
     */
    private function generateMockData($game)
    {
        $currentTime = time();
        
        switch ($game) {
            case 'pk10':
                // 生成期号：当前日期 + 序号
                $period = date('ymd') . sprintf('%03d', rand(1, 999));
                
                // 生成PK10号码(1-10不重复)
                $numbers = range(1, 10);
                shuffle($numbers);
                
                return [
                    'result' => [
                        'data' => [
                            [
                                'preDrawIssue' => $period,
                                'preDrawTime' => date('Y-m-d H:i:s', $currentTime),
                                'preDrawCode' => $numbers
                            ]
                        ]
                    ]
                ];
                
            case 'ssc':
                $period = date('ymd') . sprintf('%03d', rand(1, 999));
                $numbers = [rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9)];
                
                return [
                    'result' => [
                        'data' => [
                            [
                                'preDrawIssue' => $period,
                                'preDrawTime' => date('Y-m-d H:i:s', $currentTime),
                                'preDrawCode' => $numbers
                            ]
                        ]
                    ]
                ];
                
            default:
                return [
                    'result' => [
                        'data' => [
                            [
                                'preDrawIssue' => date('ymd') . sprintf('%03d', rand(1, 999)),
                                'preDrawTime' => date('Y-m-d H:i:s', $currentTime),
                                'sumNum' => rand(0, 27)
                            ]
                        ]
                    ]
                ];
        }
    }

    /**
     * 手动触发采集
     */
    public function manual()
    {
        $game = request()->param('game', 'pk10');
        
        if (!in_array($game, ['pk10', 'ssc', 'bj28', 'jnd28'])) {
            return json(['code' => 0, 'msg' => '不支持的游戏类型']);
        }

        $result = $this->collectGameData($game);
        
        return json([
            'code' => $result ? 1 : 0,
            'msg' => $result ? '采集成功' : '采集失败'
        ]);
    }

    /**
     * 获取采集状态
     */
    public function status()
    {
        $games = ['pk10', 'ssc', 'bj28', 'jnd28'];
        $status = [];

        foreach ($games as $game) {
            $latest = Db::table('caiji')
                ->where('game', $game)
                ->order('id', 'desc')
                ->find();

            $status[$game] = [
                'last_period' => $latest['periodnumber'] ?? 0,
                'last_time' => $latest['addtime'] ?? 0,
                'next_time' => $latest['next_time'] ?? 0,
                'status' => $latest ? '正常' : '未采集'
            ];
        }

        return json(['code' => 1, 'data' => $status]);
    }
}