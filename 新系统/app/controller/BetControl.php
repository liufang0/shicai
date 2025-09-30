<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Log;

/**
 * 投注控制系统
 * 负责投注管理、风控、盈亏控制
 */
class BetControl extends Base
{
    /**
     * 用户投注接口
     */
    public function bet()
    {
        // 获取用户信息
        $userId = session('user.id');
        if (!$userId) {
            return json(['code' => 0, 'msg' => '请先登录']);
        }

        $user = Db::table('user')->where('id', $userId)->find();
        if (!$user || $user['status'] != 1) {
            return json(['code' => 0, 'msg' => '用户状态异常']);
        }

        // 获取投注参数
        $game = request()->param('game');
        $periodNumber = request()->param('period');
        $betType = request()->param('bet_type');
        $betContent = request()->param('bet_content');
        $betAmount = floatval(request()->param('bet_amount', 0));

        // 基础验证
        if (!in_array($game, ['pk10', 'ssc', 'bj28', 'jnd28'])) {
            return json(['code' => 0, 'msg' => '不支持的游戏类型']);
        }

        if ($betAmount <= 0) {
            return json(['code' => 0, 'msg' => '投注金额必须大于0']);
        }

        // 检查用户余额
        if ($user['money'] < $betAmount) {
            return json(['code' => 0, 'msg' => '账户余额不足']);
        }

        // 检查投注限额
        $limitCheck = $this->checkBetLimit($game, $userId, $betAmount);
        if (!$limitCheck['allow']) {
            return json(['code' => 0, 'msg' => $limitCheck['msg']]);
        }

        // 检查期号状态
        $periodCheck = $this->checkPeriodStatus($game, $periodNumber);
        if (!$periodCheck['allow']) {
            return json(['code' => 0, 'msg' => $periodCheck['msg']]);
        }

        // 风控检查
        $riskCheck = $this->riskControl($game, $userId, $periodNumber, $betType, $betContent, $betAmount);
        if (!$riskCheck['allow']) {
            return json(['code' => 0, 'msg' => $riskCheck['msg']]);
        }

        // 计算赔率
        $odds = $this->calculateOdds($game, $betType, $betContent);

        Db::startTrans();
        try {
            // 扣除用户余额
            Db::table('user')
                ->where('id', $userId)
                ->dec('money', $betAmount)
                ->inc('total_bet', $betAmount);

            // 插入投注记录
            $betData = [
                'user_id' => $userId,
                'game' => $game,
                'periodnumber' => $periodNumber,
                'bet_type' => $betType,
                'bet_content' => $betContent,
                'bet_amount' => $betAmount,
                'odds' => $odds,
                'status' => 0, // 未开奖
                'bet_time' => date('Y-m-d H:i:s'),
                'ip' => request()->ip()
            ];

            $betId = Db::table('bet')->insertGetId($betData);

            // 记录投注日志
            $this->logBet($userId, $betId, $betData);

            Db::commit();

            return json([
                'code' => 1,
                'msg' => '投注成功',
                'data' => [
                    'bet_id' => $betId,
                    'odds' => $odds,
                    'max_win' => $betAmount * $odds
                ]
            ]);

        } catch (\Exception $e) {
            Db::rollback();
            Log::error("投注失败: " . $e->getMessage());
            return json(['code' => 0, 'msg' => '投注失败，请重试']);
        }
    }

    /**
     * 检查投注限额
     */
    private function checkBetLimit($game, $userId, $betAmount)
    {
        // 获取游戏配置
        $minBet = floatval(Db::table('game_config')
            ->where('game', $game)
            ->where('config_key', 'min_bet')
            ->value('config_value') ?? 10);

        $maxBet = floatval(Db::table('game_config')
            ->where('game', $game)  
            ->where('config_key', 'max_bet')
            ->value('config_value') ?? 100000);

        if ($betAmount < $minBet) {
            return ['allow' => false, 'msg' => "单注最小投注金额为{$minBet}元"];
        }

        if ($betAmount > $maxBet) {
            return ['allow' => false, 'msg' => "单注最大投注金额为{$maxBet}元"];
        }

        // 检查用户单期投注总额
        $periodTotal = Db::table('bet')
            ->where('user_id', $userId)
            ->where('game', $game)
            ->where('status', 0)
            ->sum('bet_amount');

        if (($periodTotal + $betAmount) > $maxBet * 10) {
            return ['allow' => false, 'msg' => '单期投注总额超限'];
        }

        return ['allow' => true, 'msg' => ''];
    }

    /**
     * 检查期号状态
     */
    private function checkPeriodStatus($game, $periodNumber)
    {
        // 获取当前期号信息
        $current = Db::table('caiji')
            ->where('game', $game)
            ->order('id', 'desc')
            ->find();

        if (!$current) {
            return ['allow' => false, 'msg' => '当前暂无可投注期号'];
        }

        // 检查期号是否匹配
        if ($current['next_term'] != $periodNumber) {
            return ['allow' => false, 'msg' => '期号不正确'];
        }

        // 检查是否还在投注时间内（开奖前30秒停止投注）
        $currentTime = time();
        $stopTime = $current['next_time'] - 30;

        if ($currentTime >= $stopTime) {
            return ['allow' => false, 'msg' => '当前期号已停止投注'];
        }

        return ['allow' => true, 'msg' => ''];
    }

    /**
     * 风控检查
     */
    private function riskControl($game, $userId, $periodNumber, $betType, $betContent, $betAmount)
    {
        // 1. 检查用户风控标记
        $user = Db::table('user')->where('id', $userId)->find();
        if (isset($user['risk_level']) && $user['risk_level'] > 5) {
            return ['allow' => false, 'msg' => '账户风险等级过高，暂停投注'];
        }

        // 2. 检查异常投注模式
        $recentBets = Db::table('bet')
            ->where('user_id', $userId)
            ->where('game', $game)
            ->where('bet_time', '>', date('Y-m-d H:i:s', time() - 3600))
            ->count();

        if ($recentBets > 100) {
            return ['allow' => false, 'msg' => '投注频率过高，请稍后再试'];
        }

        // 3. 检查该期投注分布（防止一边倒）
        $totalBetAmount = Db::table('bet')
            ->where('game', $game)
            ->where('periodnumber', $periodNumber)
            ->where('bet_type', $betType)
            ->where('bet_content', $betContent)
            ->sum('bet_amount');

        $periodTotalAmount = Db::table('bet')
            ->where('game', $game)
            ->where('periodnumber', $periodNumber)
            ->sum('bet_amount');

        // 如果某个选项投注金额占比超过70%，进行风控
        if ($periodTotalAmount > 0) {
            $ratio = ($totalBetAmount + $betAmount) / ($periodTotalAmount + $betAmount);
            if ($ratio > 0.7 && $betAmount > 1000) {
                // 触发风控，可能需要人工干预或自动调整开奖结果
                $this->triggerRiskAlert($game, $periodNumber, $betType, $betContent, $ratio);
            }
        }

        // 4. 检查盈利控制
        $profitCheck = $this->checkProfitControl($userId, $betAmount);
        if (!$profitCheck['allow']) {
            return $profitCheck;
        }

        return ['allow' => true, 'msg' => ''];
    }

    /**
     * 盈利控制检查
     */
    private function checkProfitControl($userId, $betAmount)
    {
        // 获取用户今日盈亏
        $todayStart = date('Y-m-d 00:00:00');
        $todayProfit = Db::table('bet')
            ->where('user_id', $userId)
            ->where('bet_time', '>=', $todayStart)
            ->where('status', '>', 0)
            ->sum('win_amount - bet_amount');

        // 如果用户今日盈利超过一定金额，增加风控
        if ($todayProfit > 50000 && $betAmount > 5000) {
            return ['allow' => false, 'msg' => '今日盈利较多，建议适度投注'];
        }

        // 检查连胜次数
        $recentWins = Db::table('bet')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->order('id', 'desc')
            ->limit(10)
            ->count();

        if ($recentWins >= 8 && $betAmount > 3000) {
            return ['allow' => false, 'msg' => '连续中奖较多，建议休息一下'];
        }

        return ['allow' => true, 'msg' => ''];
    }

    /**
     * 计算赔率
     */
    private function calculateOdds($game, $betType, $betContent)
    {
        // 基础赔率表
        $baseOdds = [
            'pk10' => [
                'champion' => 9.8,      // 冠军
                'runner_up' => 9.8,     // 亚军
                'third' => 9.8,         // 季军
                'champion_sum' => 19.0, // 冠亚和
                'big_small' => 1.95,    // 大小
                'odd_even' => 1.95      // 单双
            ],
            'ssc' => [
                'first' => 9.8,        // 第一球
                'second' => 9.8,       // 第二球
                'third' => 9.8,        // 第三球
                'fourth' => 9.8,       // 第四球
                'fifth' => 9.8,        // 第五球
                'sum_big_small' => 1.95 // 总和大小
            ],
            'bj28' => [
                'big_small' => 1.95,   // 大小
                'odd_even' => 1.95,    // 单双
                'combo' => 3.8         // 组合
            ],
            'jnd28' => [
                'big_small' => 1.95,   // 大小  
                'odd_even' => 1.95,    // 单双
                'combo' => 3.8         // 组合
            ]
        ];

        $odds = $baseOdds[$game][$betType] ?? 1.95;

        // 动态调整赔率（基于投注分布）
        $adjustedOdds = $this->adjustOdds($game, $betType, $betContent, $odds);

        return $adjustedOdds;
    }

    /**
     * 动态调整赔率
     */
    private function adjustOdds($game, $betType, $betContent, $baseOdds)
    {
        // 获取当前期投注分布
        $currentPeriod = Db::table('caiji')
            ->where('game', $game)
            ->order('id', 'desc')
            ->value('next_term');

        if (!$currentPeriod) {
            return $baseOdds;
        }

        // 计算该选项的投注比例
        $optionAmount = Db::table('bet')
            ->where('game', $game)
            ->where('periodnumber', $currentPeriod)
            ->where('bet_type', $betType)
            ->where('bet_content', $betContent)
            ->sum('bet_amount');

        $totalAmount = Db::table('bet')
            ->where('game', $game)
            ->where('periodnumber', $currentPeriod)
            ->sum('bet_amount');

        if ($totalAmount > 0) {
            $ratio = $optionAmount / $totalAmount;
            
            // 投注比例高的选项降低赔率，比例低的提高赔率
            if ($ratio > 0.5) {
                $baseOdds *= (1 - ($ratio - 0.5) * 0.2); // 最多降低10%
            } elseif ($ratio < 0.2) {
                $baseOdds *= (1 + (0.2 - $ratio) * 0.1); // 最多提高2%
            }
        }

        return round($baseOdds, 2);
    }

    /**
     * 触发风控警报
     */
    private function triggerRiskAlert($game, $periodNumber, $betType, $betContent, $ratio)
    {
        $alertData = [
            'type' => 'bet_risk_alert',
            'content' => json_encode([
                'game' => $game,
                'period' => $periodNumber,
                'bet_type' => $betType,
                'bet_content' => $betContent,
                'ratio' => $ratio,
                'alert_time' => date('Y-m-d H:i:s')
            ]),
            'create_time' => date('Y-m-d H:i:s')
        ];

        Db::table('system_log')->insert($alertData);

        // 可以在这里发送通知给管理员
        // $this->notifyAdmin($alertData);
    }

    /**
     * 记录投注日志
     */
    private function logBet($userId, $betId, $betData)
    {
        $logData = [
            'type' => 'user_bet',
            'user_id' => $userId,
            'content' => "投注ID:{$betId}, 游戏:{$betData['game']}, 期号:{$betData['periodnumber']}, 类型:{$betData['bet_type']}, 内容:{$betData['bet_content']}, 金额:{$betData['bet_amount']}",
            'ip' => request()->ip(),
            'create_time' => date('Y-m-d H:i:s')
        ];

        Db::table('system_log')->insert($logData);
    }

    /**
     * 获取用户投注历史
     */
    public function betHistory()
    {
        $userId = session('user.id');
        if (!$userId) {
            return json(['code' => 0, 'msg' => '请先登录']);
        }

        $page = intval(request()->param('page', 1));
        $limit = intval(request()->param('limit', 20));
        $game = request()->param('game', '');

        $where = [['user_id', '=', $userId]];
        if ($game) {
            $where[] = ['game', '=', $game];
        }

        $list = Db::table('bet')
            ->where($where)
            ->order('id', 'desc')
            ->page($page, $limit)
            ->select();

        $total = Db::table('bet')
            ->where($where)
            ->count();

        return json([
            'code' => 1,
            'data' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * 撤销投注（未开奖状态下）
     */
    public function cancelBet()
    {
        $userId = session('user.id');
        $betId = request()->param('bet_id');

        if (!$userId || !$betId) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $bet = Db::table('bet')
            ->where('id', $betId)
            ->where('user_id', $userId)
            ->find();

        if (!$bet) {
            return json(['code' => 0, 'msg' => '投注记录不存在']);
        }

        if ($bet['status'] != 0) {
            return json(['code' => 0, 'msg' => '该投注已开奖，无法撤销']);
        }

        // 检查是否还能撤销（距离开奖时间）
        $current = Db::table('caiji')
            ->where('game', $bet['game'])
            ->order('id', 'desc')
            ->find();

        if ($current && time() >= ($current['next_time'] - 60)) {
            return json(['code' => 0, 'msg' => '开奖前1分钟不允许撤销']);
        }

        Db::startTrans();
        try {
            // 返还用户余额
            Db::table('user')
                ->where('id', $userId)
                ->inc('money', $bet['bet_amount'])
                ->dec('total_bet', $bet['bet_amount']);

            // 更新投注状态
            Db::table('bet')
                ->where('id', $betId)
                ->update(['status' => 3]); // 3=已撤单

            Db::commit();

            return json(['code' => 1, 'msg' => '撤销成功']);

        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => '撤销失败']);
        }
    }
}