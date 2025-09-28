<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Session;
use think\facade\View;

/**
 * 管理后台控制器
 */
class Admin extends BaseController
{
    /**
     * 管理员登录页面
     */
    public function login()
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            if (empty($data['username']) || empty($data['password'])) {
                return json(['code' => 0, 'msg' => '用户名密码不能为空']);
            }
            
            $admin = Db::table('admin')->where('username', $data['username'])->find();
            
            if (!$admin || !password_verify($data['password'], $admin['password'])) {
                return json(['code' => 0, 'msg' => '用户名或密码错误']);
            }
            
            if ($admin['status'] != 1) {
                return json(['code' => 0, 'msg' => '账户已被禁用']);
            }
            
            // 更新登录信息
            Db::table('admin')->where('id', $admin['id'])->update([
                'last_login_time' => time(),
                'last_login_ip' => request()->ip()
            ]);
            
            Session::set('admin_id', $admin['id']);
            Session::set('admin_username', $admin['username']);
            
            return json(['code' => 1, 'msg' => '登录成功']);
        }
        
        return View::fetch();
    }
    
    /**
     * 管理员退出登录
     */
    public function logout()
    {
        Session::clear();
        return redirect('/admin/login');
    }
    
    /**
     * 管理后台首页
     */
    public function index()
    {
        $this->checkLogin();
        
        // 获取系统统计数据
        $stats = [
            'user_count' => Db::table('user')->count(),
            'online_count' => $this->getOnlineUserCount(),
            'today_bet_amount' => $this->getTodayBetAmount(),
            'today_recharge' => $this->getTodayRecharge(),
            'today_withdraw' => $this->getTodayWithdraw(),
        ];
        
        View::assign('stats', $stats);
        return View::fetch();
    }
    
    /**
     * 用户管理
     */
    public function userList()
    {
        $this->checkLogin();
        
        $page = request()->param('page', 1);
        $limit = request()->param('limit', 20);
        $keyword = request()->param('keyword', '');
        
        $query = Db::table('user');
        if ($keyword) {
            $query->where('username|phone', 'like', "%{$keyword}%");
        }
        
        $users = $query->order('id', 'desc')
                      ->page($page, $limit)
                      ->select()
                      ->toArray();
        
        $total = $query->count();
        
        if (request()->isAjax()) {
            return json(['code' => 0, 'msg' => '', 'count' => $total, 'data' => $users]);
        }
        
        View::assign('users', $users);
        return View::fetch();
    }
    
    /**
     * 编辑用户
     */
    public function userEdit()
    {
        $this->checkLogin();
        
        $id = request()->param('id');
        
        if (request()->isPost()) {
            $data = request()->post();
            
            $result = Db::table('user')->where('id', $id)->update([
                'username' => $data['username'],
                'phone' => $data['phone'] ?? '',
                'money' => $data['money'] ?? 0,
                'status' => $data['status'] ?? 1,
                'update_time' => time()
            ]);
            
            if ($result !== false) {
                return json(['code' => 1, 'msg' => '修改成功']);
            } else {
                return json(['code' => 0, 'msg' => '修改失败']);
            }
        }
        
        $user = Db::table('user')->where('id', $id)->find();
        View::assign('user', $user);
        return View::fetch();
    }
    
    /**
     * 投注记录管理
     */
    public function betList()
    {
        $this->checkLogin();
        
        $page = request()->param('page', 1);
        $limit = request()->param('limit', 20);
        $game = request()->param('game', '');
        $status = request()->param('status', '');
        
        $query = Db::table('bet_log')
                  ->alias('b')
                  ->leftJoin('user u', 'b.user_id = u.id')
                  ->field('b.*, u.username');
        
        if ($game) {
            $query->where('b.game', $game);
        }
        
        if ($status !== '') {
            $query->where('b.status', $status);
        }
        
        $bets = $query->order('b.create_time', 'desc')
                     ->page($page, $limit)
                     ->select()
                     ->toArray();
        
        $total = $query->count();
        
        if (request()->isAjax()) {
            return json(['code' => 0, 'msg' => '', 'count' => $total, 'data' => $bets]);
        }
        
        View::assign('bets', $bets);
        return View::fetch();
    }
    
    /**
     * 充值记录管理
     */
    public function rechargeList()
    {
        $this->checkLogin();
        
        $page = request()->param('page', 1);
        $limit = request()->param('limit', 20);
        $status = request()->param('status', '');
        
        $query = Db::table('recharge_log')
                  ->alias('r')
                  ->leftJoin('user u', 'r.user_id = u.id')
                  ->field('r.*, u.username');
        
        if ($status !== '') {
            $query->where('r.status', $status);
        }
        
        $recharges = $query->order('r.create_time', 'desc')
                          ->page($page, $limit)
                          ->select()
                          ->toArray();
        
        $total = $query->count();
        
        if (request()->isAjax()) {
            return json(['code' => 0, 'msg' => '', 'count' => $total, 'data' => $recharges]);
        }
        
        View::assign('recharges', $recharges);
        return View::fetch();
    }
    
    /**
     * 审核充值
     */
    public function rechargeAudit()
    {
        $this->checkLogin();
        
        $id = request()->param('id');
        $status = request()->param('status'); // 1:通过 2:拒绝
        
        $recharge = Db::table('recharge_log')->where('id', $id)->find();
        
        if (!$recharge || $recharge['status'] != 0) {
            return json(['code' => 0, 'msg' => '订单状态错误']);
        }
        
        Db::startTrans();
        try {
            // 更新充值状态
            Db::table('recharge_log')->where('id', $id)->update([
                'status' => $status,
                'audit_time' => time(),
                'audit_admin' => Session::get('admin_id')
            ]);
            
            // 如果审核通过，增加用户余额
            if ($status == 1) {
                Db::table('user')->where('id', $recharge['user_id'])->inc('money', $recharge['amount']);
            }
            
            Db::commit();
            return json(['code' => 1, 'msg' => '审核成功']);
            
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => '审核失败']);
        }
    }
    
    /**
     * 提现记录管理
     */
    public function withdrawList()
    {
        $this->checkLogin();
        
        $page = request()->param('page', 1);
        $limit = request()->param('limit', 20);
        $status = request()->param('status', '');
        
        $query = Db::table('withdraw_log')
                  ->alias('w')
                  ->leftJoin('user u', 'w.user_id = u.id')
                  ->field('w.*, u.username');
        
        if ($status !== '') {
            $query->where('w.status', $status);
        }
        
        $withdraws = $query->order('w.create_time', 'desc')
                          ->page($page, $limit)
                          ->select()
                          ->toArray();
        
        $total = $query->count();
        
        if (request()->isAjax()) {
            return json(['code' => 0, 'msg' => '', 'count' => $total, 'data' => $withdraws]);
        }
        
        View::assign('withdraws', $withdraws);
        return View::fetch();
    }
    
    /**
     * 审核提现
     */
    public function withdrawAudit()
    {
        $this->checkLogin();
        
        $id = request()->param('id');
        $status = request()->param('status'); // 1:通过 2:拒绝
        
        $withdraw = Db::table('withdraw_log')->where('id', $id)->find();
        
        if (!$withdraw || $withdraw['status'] != 0) {
            return json(['code' => 0, 'msg' => '订单状态错误']);
        }
        
        Db::startTrans();
        try {
            // 更新提现状态
            Db::table('withdraw_log')->where('id', $id)->update([
                'status' => $status,
                'audit_time' => time(),
                'audit_admin' => Session::get('admin_id')
            ]);
            
            // 如果审核拒绝，返还用户余额
            if ($status == 2) {
                Db::table('user')->where('id', $withdraw['user_id'])->inc('money', $withdraw['amount']);
            }
            
            Db::commit();
            return json(['code' => 1, 'msg' => '审核成功']);
            
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => '审核失败']);
        }
    }
    
    /**
     * 系统设置
     */
    public function setting()
    {
        $this->checkLogin();
        
        if (request()->isPost()) {
            $data = request()->post();
            
            foreach ($data as $key => $value) {
                Db::table('config')->where('name', $key)->update(['value' => $value]);
            }
            
            return json(['code' => 1, 'msg' => '设置成功']);
        }
        
        $configs = Db::table('config')->column('value', 'name');
        View::assign('configs', $configs);
        return View::fetch();
    }
    
    /**
     * 检查管理员登录状态
     */
    private function checkLogin()
    {
        $admin_id = Session::get('admin_id');
        if (!$admin_id) {
            if (request()->isAjax()) {
                return json(['code' => -1, 'msg' => '请先登录']);
            }
            redirect('/admin/login')->send();
            exit;
        }
    }
    
    /**
     * 获取在线用户数量
     */
    private function getOnlineUserCount()
    {
        // 这里可以通过session或cache来统计在线用户
        return Cache::get('online_user_count', 0);
    }
    
    /**
     * 获取今日投注总额
     */
    private function getTodayBetAmount()
    {
        $today_start = strtotime(date('Y-m-d'));
        $today_end = $today_start + 86400;
        
        return Db::table('bet_log')
                ->where('create_time', '>=', $today_start)
                ->where('create_time', '<', $today_end)
                ->sum('bet_amount') ?: 0;
    }
    
    /**
     * 获取今日充值总额
     */
    private function getTodayRecharge()
    {
        $today_start = strtotime(date('Y-m-d'));
        $today_end = $today_start + 86400;
        
        return Db::table('recharge_log')
                ->where('create_time', '>=', $today_start)
                ->where('create_time', '<', $today_end)
                ->where('status', 1)
                ->sum('amount') ?: 0;
    }
    
    /**
     * 获取今日提现总额
     */
    private function getTodayWithdraw()
    {
        $today_start = strtotime(date('Y-m-d'));
        $today_end = $today_start + 86400;
        
        return Db::table('withdraw_log')
                ->where('create_time', '>=', $today_start)
                ->where('create_time', '<', $today_end)
                ->where('status', 1)
                ->sum('amount') ?: 0;
    }
}