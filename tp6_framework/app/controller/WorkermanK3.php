<?php
declare(strict_types=1);

namespace app\controller;

/**
 * K3游戏 WebSocket控制器
 */
class WorkermanK3 extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15538';
    protected $game = 'k3';
    
    protected function initializeGame()
    {
        // K3特有初始化逻辑
        $this->gameConfig = config('site.k3', []);
        $this->minBet = floatval($this->gameConfig['min_point'] ?? 10);
        $this->maxBet = floatval($this->gameConfig['max_point'] ?? 100000);
    }
    
    protected function formatGameData($caiji)
    {
        return k3_format($caiji);
    }
    
    /**
     * K3游戏特有的投注验证
     */
    protected function validateBet($bet_data)
    {
        $bet_type = $bet_data['bet_type'] ?? '';
        $bet_amount = floatval($bet_data['bet_amount'] ?? 0);
        
        // 基础验证
        if (!parent::validateBet($bet_data)) {
            return false;
        }
        
        // K3特有投注类型验证
        $valid_types = [
            'hz',      // 和值
            'dxds',    // 大小单双
            'thtx',    // 三同号通选
            'duizi',   // 对子
            'shunzi',  // 顺子
            'santong', // 三同号
            'budui',   // 不对
        ];
        
        if (!in_array($bet_type, $valid_types)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * K3开奖逻辑
     */
    protected function processGameResult($result)
    {
        $numbers = explode(',', $result['awardnumbers']);
        if (count($numbers) !== 3) {
            return false;
        }
        
        $n1 = intval($numbers[0]);
        $n2 = intval($numbers[1]);
        $n3 = intval($numbers[2]);
        $sum = $n1 + $n2 + $n3;
        
        // 获取本期投注
        $bets = $this->getCurrentPeriodBets($result['periodnumber']);
        
        foreach ($bets as $bet) {
            $win_amount = 0;
            $bet_type = $bet['bet_type'];
            $bet_content = $bet['bet_content'];
            $bet_amount = floatval($bet['bet_amount']);
            
            switch ($bet_type) {
                case 'hz': // 和值
                    if ($bet_content == $sum) {
                        $win_amount = $bet_amount * floatval(config('site.k3_hz', 1.98));
                    }
                    break;
                    
                case 'dxds': // 大小单双
                    $is_big = $sum >= 11;
                    $is_single = $sum % 2 == 1;
                    
                    if (($bet_content == 'big' && $is_big) ||
                        ($bet_content == 'small' && !$is_big) ||
                        ($bet_content == 'single' && $is_single) ||
                        ($bet_content == 'double' && !$is_single)) {
                        $win_amount = $bet_amount * floatval(config('site.k3_dxds', 1.98));
                    }
                    break;
                    
                case 'thtx': // 三同号通选
                    if ($n1 == $n2 && $n2 == $n3) {
                        $win_amount = $bet_amount * floatval(config('site.k3_thtx', 230));
                    }
                    break;
                    
                case 'duizi': // 对子
                    $target = intval($bet_content);
                    if (($n1 == $target && $n2 == $target) ||
                        ($n1 == $target && $n3 == $target) ||
                        ($n2 == $target && $n3 == $target)) {
                        $win_amount = $bet_amount * floatval(config('site.k3_duizi', 10));
                    }
                    break;
                    
                case 'shunzi': // 顺子
                    $sorted = [$n1, $n2, $n3];
                    sort($sorted);
                    $is_sequence = ($sorted[1] - $sorted[0] == 1) && ($sorted[2] - $sorted[1] == 1);
                    
                    if ($is_sequence) {
                        $win_amount = $bet_amount * floatval(config('site.k3_shunzi', 35));
                    }
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
}