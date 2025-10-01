<?php
declare(strict_types=1);

namespace app\controller;

/**
 * XYFT新疆时时彩/幸运飞艇 WebSocket控制器
 */
class WorkermanXyft extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15537';
    protected $game = 'xyft';
    
    protected function initializeGame()
    {
        // XYFT特有初始化逻辑
        $this->gameConfig = config('site.xyft', []);
        $this->minBet = floatval($this->gameConfig['min_point'] ?? 10);
        $this->maxBet = floatval($this->gameConfig['max_point'] ?? 100000);
    }
    
    protected function formatGameData($caiji)
    {
        return xyft_format($caiji);
    }
    
    /**
     * XYFT游戏特有的投注验证
     */
    protected function validateBet($bet_data)
    {
        $bet_type = $bet_data['bet_type'] ?? '';
        $bet_amount = floatval($bet_data['bet_amount'] ?? 0);
        
        // 基础验证
        if (!parent::validateBet($bet_data)) {
            return false;
        }
        
        // XYFT特有投注类型验证
        $valid_types = [
            'wanqianbaishige',   // 万千百十个
            'zuxuanwu',         // 组选5
            'zuxuansan',        // 组选3
            'renxuan',          // 任选
            'qiansan',          // 前三
            'zhongsan',         // 中三
            'housan',           // 后三
            'dingweidan',       // 定位胆
            'daxiaodanshuang',  // 大小单双
            'longhu',           // 龙虎
        ];
        
        if (!in_array($bet_type, $valid_types)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * XYFT开奖逻辑
     */
    protected function processGameResult($result)
    {
        $numbers = explode(',', $result['awardnumbers']);
        if (count($numbers) !== 5) {
            return false;
        }
        
        // 获取本期投注
        $bets = $this->getCurrentPeriodBets($result['periodnumber']);
        
        foreach ($bets as $bet) {
            $win_amount = 0;
            $bet_type = $bet['bet_type'];
            $bet_content = $bet['bet_content'];
            $bet_amount = floatval($bet['bet_amount']);
            $position = $bet['position'] ?? '';
            
            switch ($bet_type) {
                case 'wanqianbaishige':
                    $win_amount = $this->processDirectBet($numbers, $bet_content, $position, $bet_amount);
                    break;
                    
                case 'qiansan':
                    $win_amount = $this->processQianSanBet($numbers, $bet_content, $bet_amount);
                    break;
                    
                case 'zhongsan':
                    $win_amount = $this->processZhongSanBet($numbers, $bet_content, $bet_amount);
                    break;
                    
                case 'housan':
                    $win_amount = $this->processHouSanBet($numbers, $bet_content, $bet_amount);
                    break;
                    
                case 'daxiaodanshuang':
                    $win_amount = $this->processDxdsBet($numbers, $bet_content, $position, $bet_amount);
                    break;
                    
                case 'longhu':
                    $win_amount = $this->processLongHuBet($numbers, $bet_content, $bet_amount);
                    break;
                    
                case 'dingweidan':
                    $win_amount = $this->processDingWeiDanBet($numbers, $bet_content, $position, $bet_amount);
                    break;
            }
            
            // 更新投注结果
            $this->updateBetResult($bet['id'], $win_amount > 0 ? 1 : 2, $win_amount);
            
            // 中奖则增加用户余额
            if ($win_amount > 0) {
                $this->addUserMoney($bet['user_id'], $win_amount);
            }
        }
        
        return true;
    }
    
    /**
     * 直选投注处理
     */
    private function processDirectBet($numbers, $bet_content, $position, $bet_amount)
    {
        $position_map = [
            'wan' => 0,
            'qian' => 1,
            'bai' => 2,
            'shi' => 3,
            'ge' => 4
        ];
        
        if (!isset($position_map[$position])) {
            return 0;
        }
        
        $index = $position_map[$position];
        if ($numbers[$index] == $bet_content) {
            return $bet_amount * floatval(config('site.xyft_zhixuan', 9.8));
        }
        
        return 0;
    }
    
    /**
     * 前三投注处理
     */
    private function processQianSanBet($numbers, $bet_content, $bet_amount)
    {
        $front_three = implode('', array_slice($numbers, 0, 3));
        if ($front_three == $bet_content) {
            return $bet_amount * floatval(config('site.xyft_qiansan', 980));
        }
        return 0;
    }
    
    /**
     * 中三投注处理
     */
    private function processZhongSanBet($numbers, $bet_content, $bet_amount)
    {
        $middle_three = implode('', array_slice($numbers, 1, 3));
        if ($middle_three == $bet_content) {
            return $bet_amount * floatval(config('site.xyft_zhongsan', 980));
        }
        return 0;
    }
    
    /**
     * 后三投注处理
     */
    private function processHouSanBet($numbers, $bet_content, $bet_amount)
    {
        $back_three = implode('', array_slice($numbers, 2, 3));
        if ($back_three == $bet_content) {
            return $bet_amount * floatval(config('site.xyft_housan', 980));
        }
        return 0;
    }
    
    /**
     * 大小单双投注处理
     */
    private function processDxdsBet($numbers, $bet_content, $position, $bet_amount)
    {
        $position_map = [
            'wan' => 0,
            'qian' => 1,
            'bai' => 2,
            'shi' => 3,
            'ge' => 4
        ];
        
        if (!isset($position_map[$position])) {
            return 0;
        }
        
        $index = $position_map[$position];
        $number = intval($numbers[$index]);
        
        $is_big = $number >= 5;
        $is_single = $number % 2 == 1;
        
        $win = false;
        if ($bet_content == 'big' && $is_big) $win = true;
        if ($bet_content == 'small' && !$is_big) $win = true;
        if ($bet_content == 'single' && $is_single) $win = true;
        if ($bet_content == 'double' && !$is_single) $win = true;
        
        if ($win) {
            return $bet_amount * floatval(config('site.xyft_dxds', 1.98));
        }
        
        return 0;
    }
    
    /**
     * 龙虎投注处理
     */
    private function processLongHuBet($numbers, $bet_content, $bet_amount)
    {
        $wan = intval($numbers[0]);
        $ge = intval($numbers[4]);
        
        $win = false;
        if ($bet_content == 'long' && $wan > $ge) $win = true;
        if ($bet_content == 'hu' && $wan < $ge) $win = true;
        if ($bet_content == 'he' && $wan == $ge) $win = true;
        
        if ($win) {
            $rate = ($bet_content == 'he') ? 
                floatval(config('site.xyft_longhu_he', 8.8)) : 
                floatval(config('site.xyft_longhu', 1.98));
            return $bet_amount * $rate;
        }
        
        return 0;
    }
    
    /**
     * 定位胆投注处理
     */
    private function processDingWeiDanBet($numbers, $bet_content, $position, $bet_amount)
    {
        return $this->processDirectBet($numbers, $bet_content, $position, $bet_amount);
    }
}