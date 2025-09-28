<?php
declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;

/**
 * 管理后台首页
 */
class Index extends BaseController
{
    public function index()
    {
        // 获取统计数据
        $stats = [
            // 今日统计
            'today_user_count' => $this->getTodayUserCount(),
            'today_bet_amount' => $this->getTodayBetAmount(),
            'today_recharge' => $this->getTodayRecharge(),
            'today_withdraw' => $this->getTodayWithdraw(),
            'today_profit' => $this->getTodayProfit(),
            
            // 总统计
            'total_users' => Db::table('user')->count(),
            'total_bet_amount' => Db::table('bet_log')->sum('bet_amount'),
            'total_recharge' => Db::table('recharge_log')->where('status', 1)->sum('amount'),
            'total_withdraw' => Db::table('withdraw_log')->where('status', 1)->sum('amount'),
            
            // 在线统计
            'online_users' => $this->getOnlineUsers(),
        ];
        
        // 最近投注记录
        $recent_bets = Db::table('bet_log')
                        ->alias('b')
                        ->leftJoin('user u', 'b.user_id = u.id')
                        ->field('b.*, u.username')
                        ->order('b.create_time', 'desc')
                        ->limit(10)
                        ->select();
        
        View::assign('stats', $stats);
        View::assign('recent_bets', $recent_bets);
        return View::fetch();
    }
    
    /**
     * 获取今日新增用户数
     */
    private function getTodayUserCount()
    {
        $today_start = strtotime(date('Y-m-d'));
        return Db::table('user')
                ->where('create_time', '>=', $today_start)
                ->count();
    }
    
    /**
     * 获取今日投注总额
     */
    private function getTodayBetAmount()
    {
        $today_start = strtotime(date('Y-m-d'));
        return Db::table('bet_log')
                ->where('create_time', '>=', $today_start)
                ->sum('bet_amount') ?: 0;
    }
    
    /**
     * 获取今日充值总额
     */
    private function getTodayRecharge()
    {
        $today_start = strtotime(date('Y-m-d'));
        return Db::table('recharge_log')
                ->where('create_time', '>=', $today_start)
                ->where('status', 1)
                ->sum('amount') ?: 0;
    }
    
    /**
     * 获取今日提现总额
     */
    private function getTodayWithdraw()
    {
        $today_start = strtotime(date('Y-m-d'));
        return Db::table('withdraw_log')
                ->where('create_time', '>=', $today_start)
                ->where('status', 1)
                ->sum('amount') ?: 0;
    }
    
    /**
     * 获取今日盈利
     */
    private function getTodayProfit()
    {
        $today_start = strtotime(date('Y-m-d'));
        
        // 今日中奖金额
        $win_amount = Db::table('bet_log')
                       ->where('create_time', '>=', $today_start)
                       ->where('status', 1)
                       ->sum('win_amount') ?: 0;
        
        // 今日投注总额
        $bet_amount = $this->getTodayBetAmount();
        
        return $bet_amount - $win_amount;
    }
    
    /**
     * 获取在线用户数
     */
    private function getOnlineUsers()
    {
        // 15分钟内有活动的用户视为在线
        $online_time = time() - 900;
        return Db::table('user')
                ->where('last_login_time', '>=', $online_time)
                ->count();
    }
}