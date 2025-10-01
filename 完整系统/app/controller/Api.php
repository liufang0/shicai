<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\Response;

/**
 * API接口控制器
 */
class Api extends BaseController
{
    /**
     * 用户登录API
     */
    public function login()
    {
        $data = request()->post();
        
        if (empty($data['username']) || empty($data['password'])) {
            return json(['code' => 0, 'msg' => '用户名密码不能为空']);
        }
        
        $user = Db::table('user')->where('username', $data['username'])->find();
        
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return json(['code' => 0, 'msg' => '用户名或密码错误']);
        }
        
        if ($user['status'] != 1) {
            return json(['code' => 0, 'msg' => '账户已被禁用']);
        }
        
        // 更新登录信息
        Db::table('user')->where('id', $user['id'])->update([
            'last_login_time' => time(),
            'last_login_ip' => request()->ip()
        ]);
        
        session('user_id', $user['id']);
        session('username', $user['username']);
        
        return json([
            'code' => 1, 
            'msg' => '登录成功',
            'data' => [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'balance' => $user['money']
            ]
        ]);
    }
    
    /**
     * 获取用户余额
     */
    public function balance()
    {
        $user_id = session('user_id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '未登录']);
        }
        
        $balance = Db::table('user')->where('id', $user_id)->value('money');
        
        return json([
            'code' => 1,
            'data' => ['balance' => $balance ?: 0]
        ]);
    }
    
    /**
     * 投注API
     */
    public function bet()
    {
        $user_id = session('user_id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '未登录']);
        }
        
        $data = request()->post();
        
        // 验证投注数据
        if (empty($data['game']) || empty($data['issue']) || empty($data['bet_amount'])) {
            return json(['code' => 0, 'msg' => '投注信息不完整']);
        }
        
        $bet_amount = floatval($data['bet_amount']);
        if ($bet_amount <= 0) {
            return json(['code' => 0, 'msg' => '投注金额无效']);
        }
        
        // 检查余额
        $user = Db::table('user')->where('id', $user_id)->find();
        if ($user['money'] < $bet_amount) {
            return json(['code' => 0, 'msg' => '余额不足']);
        }
        
        // 检查期号是否可投注
        $game_status = Cache::get($data['game'] . '_status');
        if ($game_status !== 'betting') {
            return json(['code' => 0, 'msg' => '当前期号不可投注']);
        }
        
        // 创建投注记录
        $bet_id = Db::table('bet_log')->insertGetId([
            'user_id' => $user_id,
            'game' => $data['game'],
            'issue' => $data['issue'],
            'bet_type' => $data['bet_type'] ?? '',
            'bet_content' => $data['bet_content'] ?? '',
            'bet_amount' => $bet_amount,
            'odds' => $data['odds'] ?? 1,
            'status' => 0, // 未开奖
            'create_time' => time(),
            'ip' => request()->ip()
        ]);
        
        // 扣除余额
        Db::table('user')->where('id', $user_id)->dec('money', $bet_amount);
        
        return json([
            'code' => 1,
            'msg' => '投注成功',
            'data' => ['bet_id' => $bet_id]
        ]);
    }
    
    /**
     * 获取游戏数据
     */
    public function gameData()
    {
        $game = request()->param('game', '幸运飞艇');
        
        // 获取最新期号数据
        $latest = Db::table($game . '_caiji')
            ->order('issue', 'desc')
            ->limit(20)
            ->select();
        
        // 获取当前期号状态
        $current_status = Cache::get($game . '_status', 'waiting');
        $current_issue = Cache::get($game . '_current_issue');
        $next_time = Cache::get($game . '_next_time');
        
        return json([
            'code' => 1,
            'data' => [
                'latest' => $latest,
                'status' => $current_status,
                'current_issue' => $current_issue,
                'next_time' => $next_time
            ]
        ]);
    }
    
    /**
     * 获取投注记录
     */
    public function betHistory()
    {
        $user_id = session('user_id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '未登录']);
        }
        
        $page = request()->param('page', 1);
        $limit = request()->param('limit', 20);
        
        $list = Db::table('bet_log')
            ->where('user_id', $user_id)
            ->order('create_time', 'desc')
            ->page($page, $limit)
            ->select();
        
        return json([
            'code' => 1,
            'data' => $list
        ]);
    }
    
    /**
     * 获取公告信息
     */
    public function notice()
    {
        $notices = Db::table('notice')
            ->where('status', 1)
            ->order('create_time', 'desc')
            ->limit(10)
            ->select();
        
        return json([
            'code' => 1,
            'data' => $notices
        ]);
    }
    
    /**
     * 充值订单API
     */
    public function recharge()
    {
        $user_id = session('user_id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '未登录']);
        }
        
        $data = request()->post();
        $amount = floatval($data['amount'] ?? 0);
        
        if ($amount <= 0) {
            return json(['code' => 0, 'msg' => '充值金额无效']);
        }
        
        // 创建充值订单
        $order_no = 'R' . date('YmdHis') . mt_rand(1000, 9999);
        
        $order_id = Db::table('recharge_log')->insertGetId([
            'user_id' => $user_id,
            'order_no' => $order_no,
            'amount' => $amount,
            'status' => 0, // 待支付
            'create_time' => time(),
            'ip' => request()->ip()
        ]);
        
        return json([
            'code' => 1,
            'msg' => '订单创建成功',
            'data' => [
                'order_id' => $order_id,
                'order_no' => $order_no,
                'amount' => $amount
            ]
        ]);
    }
    
    /**
     * 提现申请API
     */
    public function withdraw()
    {
        $user_id = session('user_id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '未登录']);
        }
        
        $data = request()->post();
        $amount = floatval($data['amount'] ?? 0);
        
        if ($amount <= 0) {
            return json(['code' => 0, 'msg' => '提现金额无效']);
        }
        
        // 检查余额
        $user = Db::table('user')->where('id', $user_id)->find();
        if ($user['money'] < $amount) {
            return json(['code' => 0, 'msg' => '余额不足']);
        }
        
        // 检查银行卡信息
        if (empty($data['bank_name']) || empty($data['bank_account'])) {
            return json(['code' => 0, 'msg' => '请填写银行卡信息']);
        }
        
        // 创建提现申请
        $withdraw_id = Db::table('withdraw_log')->insertGetId([
            'user_id' => $user_id,
            'amount' => $amount,
            'bank_name' => $data['bank_name'],
            'bank_account' => $data['bank_account'],
            'status' => 0, // 待审核
            'create_time' => time(),
            'ip' => request()->ip()
        ]);
        
        // 冻结余额
        Db::table('user')->where('id', $user_id)->dec('money', $amount);
        
        return json([
            'code' => 1,
            'msg' => '提现申请成功，等待审核',
            'data' => ['withdraw_id' => $withdraw_id]
        ]);
    }
    
    /**
     * HTTP GET请求工具方法
     */
    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if (strpos($url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $result = curl_error($ch);
        }
        
        curl_close($ch);
        return $result;
    }
}