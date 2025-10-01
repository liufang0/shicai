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
class Admin
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
            
            // 默认管理员账户 admin/123456
            if ($data['username'] == 'admin' && $data['password'] == '123456') {
                Session::set('admin_id', 1);
                Session::set('admin_username', 'admin');
                return json(['code' => 1, 'msg' => '登录成功']);
            }
            
            return json(['code' => 0, 'msg' => '用户名或密码错误']);
        }
        
        // 登录页面HTML
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>管理后台登录</title>';
        $html .= '<style>body{font-family:Arial;margin:0;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;} .login-box{background:white;border-radius:16px;padding:40px;width:100%;max-width:400px;box-shadow:0 20px 40px rgba(0,0,0,0.1);} .title{text-align:center;margin-bottom:30px;color:#333;} .form-group{margin-bottom:20px;} .form-group label{display:block;margin-bottom:5px;color:#555;font-weight:500;} .form-group input{width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:8px;font-size:16px;transition:border-color 0.3s;} .form-group input:focus{outline:none;border-color:#667eea;} .login-btn{width:100%;padding:14px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;margin-top:20px;} .login-btn:hover{opacity:0.9;} .tips{text-align:center;margin-top:20px;color:#666;font-size:14px;}</style>';
        $html .= '</head><body><div class="login-box">';
        $html .= '<div class="title"><h2>🔐 管理后台</h2></div>';
        $html .= '<form id="loginForm">';
        $html .= '<div class="form-group"><label>管理员账户</label><input type="text" name="username" placeholder="请输入用户名" required></div>';
        $html .= '<div class="form-group"><label>登录密码</label><input type="password" name="password" placeholder="请输入密码" required></div>';
        $html .= '<button type="submit" class="login-btn">立即登录</button>';
        $html .= '</form>';
        $html .= '<div class="tips">默认账户: admin / 123456</div>';
        $html .= '</div>';
        $html .= '<script>document.getElementById("loginForm").addEventListener("submit",function(e){e.preventDefault();const formData=new FormData(this);fetch("/admin/login",{method:"POST",body:formData}).then(response=>response.json()).then(data=>{if(data.code===1){alert("登录成功!");window.location.href="/admin";}else{alert(data.msg);}}).catch(error=>{alert("登录失败!");});});</script>';
        $html .= '</body></html>';
        
        return $html;
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
        
        // 模拟系统统计数据
        $stats = [
            'user_count' => 1250,
            'online_count' => 85,
            'today_bet_amount' => 125000,
            'today_recharge' => 85000,
            'today_withdraw' => 35000,
        ];
        
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>管理后台</title>';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:#f5f7fa;} .header{background:#2c3e50;color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;} .header h1{font-size:24px;} .user-info{color:#ecf0f1;} .main{padding:30px;} .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:30px;} .stat-card{background:white;border-radius:12px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.1);display:flex;align-items:center;} .stat-icon{width:60px;height:60px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:15px;} .stat-info h3{font-size:32px;color:#2c3e50;margin-bottom:5px;} .stat-info p{color:#7f8c8d;} .icon-users{background:linear-gradient(135deg,#667eea,#764ba2);} .icon-online{background:linear-gradient(135deg,#f093fb,#f5576c);} .icon-bet{background:linear-gradient(135deg,#4facfe,#00f2fe);} .icon-money{background:linear-gradient(135deg,#43e97b,#38f9d7);} .icon-withdraw{background:linear-gradient(135deg,#fa709a,#fee140);} .menu{background:white;border-radius:12px;padding:30px;box-shadow:0 4px 15px rgba(0,0,0,0.1);} .menu h3{margin-bottom:20px;color:#2c3e50;} .menu-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;} .menu-item{background:#f8f9fa;border:2px solid #e9ecef;border-radius:8px;padding:20px;text-align:center;text-decoration:none;color:#495057;transition:all 0.3s;} .menu-item:hover{background:#e9ecef;transform:translateY(-2px);border-color:#667eea;} .menu-item i{font-size:24px;margin-bottom:10px;display:block;} .logout{background:#e74c3c;color:white;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;}</style>';
        $html .= '</head><body>';
        
        $html .= '<div class="header">';
        $html .= '<h1>🏢 管理后台</h1>';
        $html .= '<div class="user-info">欢迎，' . Session::get('admin_username') . ' <button class="logout" onclick="logout()">退出</button></div>';
        $html .= '</div>';
        
        $html .= '<div class="main">';
        $html .= '<div class="stats">';
        $html .= '<div class="stat-card">';
        $html .= '<div class="stat-icon icon-users">👥</div>';
        $html .= '<div class="stat-info"><h3>' . number_format($stats['user_count']) . '</h3><p>注册用户</p></div>';
        $html .= '</div>';
        
        $html .= '<div class="stat-card">';
        $html .= '<div class="stat-icon icon-online">🟢</div>';
        $html .= '<div class="stat-info"><h3>' . $stats['online_count'] . '</h3><p>在线用户</p></div>';
        $html .= '</div>';
        
        $html .= '<div class="stat-card">';
        $html .= '<div class="stat-icon icon-bet">🎮</div>';
        $html .= '<div class="stat-info"><h3>¥' . number_format($stats['today_bet_amount']) . '</h3><p>今日投注</p></div>';
        $html .= '</div>';
        
        $html .= '<div class="stat-card">';
        $html .= '<div class="stat-icon icon-money">💰</div>';
        $html .= '<div class="stat-info"><h3>¥' . number_format($stats['today_recharge']) . '</h3><p>今日充值</p></div>';
        $html .= '</div>';
        
        $html .= '<div class="stat-card">';
        $html .= '<div class="stat-icon icon-withdraw">💸</div>';
        $html .= '<div class="stat-info"><h3>¥' . number_format($stats['today_withdraw']) . '</h3><p>今日提现</p></div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="menu">';
        $html .= '<h3>📋 管理菜单</h3>';
        $html .= '<div class="menu-grid">';
        $html .= '<a href="/admin/userList" class="menu-item">👤<br>用户管理</a>';
        $html .= '<a href="/admin/betList" class="menu-item">🎯<br>投注管理</a>';
        $html .= '<a href="/admin/rechargeList" class="menu-item">💳<br>充值管理</a>';
        $html .= '<a href="/admin/withdrawList" class="menu-item">💸<br>提现管理</a>';
        $html .= '<a href="/admin/setting" class="menu-item">⚙️<br>系统设置</a>';
        $html .= '<a href="/run/fangjian?game=bj28" class="menu-item">🎮<br>游戏大厅</a>';
        $html .= '<a href="/" class="menu-item">🏠<br>前台首页</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<script>function logout(){if(confirm("确定退出登录吗？")){fetch("/admin/logout").then(()=>{window.location.href="/admin/login";});}}</script>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * 用户管理
     */
    public function userList()
    {
        $this->checkLogin();
        
        // 模拟用户数据
        $users = [];
        for ($i = 1; $i <= 20; $i++) {
            $users[] = [
                'id' => $i,
                'username' => 'user' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'phone' => '138' . rand(10000000, 99999999),
                'money' => rand(1000, 50000),
                'total_bet' => rand(10000, 100000),
                'total_recharge' => rand(5000, 80000),
                'status' => rand(0, 1) ? '正常' : '冻结',
                'register_time' => date('Y-m-d H:i:s', time() - rand(86400, 2592000)),
                'last_login_time' => date('Y-m-d H:i:s', time() - rand(3600, 86400))
            ];
        }
        
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>用户管理</title>';
        $html .= '<style>body{font-family:Arial;margin:0;background:#f5f7fa;padding:20px;} .container{max-width:1200px;margin:0 auto;} .header{background:white;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 4px 15px rgba(0,0,0,0.1);display:flex;justify-content:space-between;align-items:center;} .search{display:flex;gap:10px;} .search input{padding:8px 12px;border:2px solid #e2e8f0;border-radius:6px;} .search button{padding:8px 16px;background:#667eea;color:white;border:none;border-radius:6px;cursor:pointer;} .table-container{background:white;border-radius:12px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.1);overflow-x:auto;} table{width:100%;border-collapse:collapse;} th,td{padding:12px;text-align:left;border-bottom:1px solid #e2e8f0;} th{background:#f8f9fa;font-weight:600;color:#495057;} tr:hover{background:#f8f9fa;} .status{padding:4px 8px;border-radius:4px;font-size:12px;} .status-normal{background:#d4edda;color:#155724;} .status-frozen{background:#f8d7da;color:#721c24;} .btn{padding:4px 8px;border:none;border-radius:4px;cursor:pointer;font-size:12px;margin-right:5px;} .btn-edit{background:#17a2b8;color:white;} .btn-delete{background:#dc3545;color:white;} .back-btn{background:#6c757d;color:white;padding:8px 16px;border:none;border-radius:6px;text-decoration:none;}</style>';
        $html .= '</head><body><div class="container">';
        
        $html .= '<div class="header">';
        $html .= '<h2>👤 用户管理</h2>';
        $html .= '<div class="search">';
        $html .= '<input type="text" placeholder="搜索用户名或手机号" id="searchInput">';
        $html .= '<button onclick="searchUser()">搜索</button>';
        $html .= '<a href="/admin" class="back-btn">返回首页</a>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="table-container">';
        $html .= '<table>';
        $html .= '<thead><tr><th>ID</th><th>用户名</th><th>手机号</th><th>余额</th><th>累计投注</th><th>累计充值</th><th>状态</th><th>注册时间</th><th>最后登录</th><th>操作</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($users as $user) {
            $status_class = $user['status'] == '正常' ? 'status-normal' : 'status-frozen';
            $html .= '<tr>';
            $html .= '<td>' . $user['id'] . '</td>';
            $html .= '<td>' . $user['username'] . '</td>';
            $html .= '<td>' . $user['phone'] . '</td>';
            $html .= '<td>¥' . number_format($user['money']) . '</td>';
            $html .= '<td>¥' . number_format($user['total_bet']) . '</td>';
            $html .= '<td>¥' . number_format($user['total_recharge']) . '</td>';
            $html .= '<td><span class="status ' . $status_class . '">' . $user['status'] . '</span></td>';
            $html .= '<td>' . $user['register_time'] . '</td>';
            $html .= '<td>' . $user['last_login_time'] . '</td>';
            $html .= '<td>';
            $html .= '<button class="btn btn-edit" onclick="editUser(' . $user['id'] . ')">编辑</button>';
            $html .= '<button class="btn btn-delete" onclick="deleteUser(' . $user['id'] . ')">删除</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<script>';
        $html .= 'function searchUser(){const keyword=document.getElementById("searchInput").value;if(keyword){alert("搜索功能："+keyword);}}';
        $html .= 'function editUser(id){alert("编辑用户ID："+id);}';
        $html .= 'function deleteUser(id){if(confirm("确定删除该用户吗？")){alert("删除用户ID："+id);}}';
        $html .= '</script>';
        $html .= '</body></html>';
        
        return $html;
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