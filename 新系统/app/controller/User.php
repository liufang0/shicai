<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Session;

/**
 * 用户中心控制器
 */
class User extends BaseController
{
    /**
     * 用户中心首页
     */
    public function index()
    {
        // 模拟用户信息
        $userinfo = [
            'id' => 1,
            'username' => '测试用户',
            'money' => 12500,
            'total_bet' => 45000,
            'total_win' => 38000,
            'commission' => 850,
            't_account' => 8,
            'ying' => -2500,
            'sum_add' => 15000
        ];
        
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>个人中心</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:"Microsoft YaHei",Arial;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;} .container{max-width:400px;margin:0 auto;padding:20px;} .header{background:rgba(255,255,255,0.95);border-radius:16px;padding:25px;margin-bottom:20px;text-align:center;backdrop-filter:blur(10px);} .avatar{width:80px;height:80px;border-radius:50%;background:linear-gradient(45deg,#667eea,#764ba2);margin:0 auto 15px;display:flex;align-items:center;justify-content:center;color:white;font-size:32px;} .username{font-size:20px;font-weight:600;color:#333;margin-bottom:8px;} .balance{font-size:32px;font-weight:700;color:#e74c3c;} .balance-label{color:#666;font-size:14px;margin-top:5px;} .stats{background:rgba(255,255,255,0.95);border-radius:16px;padding:20px;margin-bottom:20px;backdrop-filter:blur(10px);} .stats-title{font-size:16px;font-weight:600;color:#333;margin-bottom:15px;text-align:center;} .stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .stat-item{text-align:center;padding:15px;background:rgba(245,245,245,0.8);border-radius:12px;} .stat-value{font-size:20px;font-weight:600;color:#2c3e50;} .stat-label{font-size:12px;color:#7f8c8d;margin-top:5px;} .menu{background:rgba(255,255,255,0.95);border-radius:16px;padding:20px;margin-bottom:20px;backdrop-filter:blur(10px);} .menu-title{font-size:16px;font-weight:600;color:#333;margin-bottom:15px;text-align:center;} .menu-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;} .menu-item{background:rgba(245,245,245,0.8);border:none;border-radius:12px;padding:15px;text-decoration:none;color:#495057;text-align:center;transition:all 0.3s;font-size:14px;} .menu-item:hover{background:#e9ecef;transform:translateY(-2px);} .menu-item i{display:block;font-size:24px;margin-bottom:8px;} .nav-bottom{background:rgba(255,255,255,0.95);border-radius:16px;padding:15px;text-align:center;backdrop-filter:blur(10px);} .nav-bottom a{color:#667eea;text-decoration:none;margin:0 15px;font-size:14px;} .today-stats{margin-top:15px;padding-top:15px;border-top:1px solid #e9ecef;display:grid;grid-template-columns:repeat(3,1fr);gap:10px;text-align:center;} .today-item{padding:8px;} .today-value{font-size:16px;font-weight:600;color:#2c3e50;} .today-label{font-size:11px;color:#7f8c8d;margin-top:2px;}</style>';
        $html .= '</head><body>';
        $html .= '<div class="container">';
        
        $html .= '<div class="header">';
        $html .= '<div class="avatar">👤</div>';
        $html .= '<div class="username">' . $userinfo['username'] . '</div>';
        $html .= '<div class="balance">¥' . number_format($userinfo['money']) . '</div>';
        $html .= '<div class="balance-label">当前余额</div>';
        $html .= '</div>';
        
        $html .= '<div class="stats">';
        $html .= '<div class="stats-title">📊 账户统计</div>';
        $html .= '<div class="stats-grid">';
        $html .= '<div class="stat-item"><div class="stat-value">¥' . number_format($userinfo['total_bet']) . '</div><div class="stat-label">累计投注</div></div>';
        $html .= '<div class="stat-item"><div class="stat-value">¥' . number_format($userinfo['total_win']) . '</div><div class="stat-label">累计收益</div></div>';
        $html .= '<div class="stat-item"><div class="stat-value">¥' . number_format($userinfo['commission']) . '</div><div class="stat-label">推广佣金</div></div>';
        $html .= '<div class="stat-item"><div class="stat-value">' . $userinfo['t_account'] . '</div><div class="stat-label">推荐人数</div></div>';
        $html .= '</div>';
        
        $html .= '<div class="today-stats">';
        $html .= '<div class="today-item"><div class="today-value">¥' . number_format(abs($userinfo['ying'])) . '</div><div class="today-label">今日盈亏</div></div>';
        $html .= '<div class="today-item"><div class="today-value">¥' . number_format($userinfo['sum_add']) . '</div><div class="today-label">今日投注</div></div>';
        $html .= '<div class="today-item"><div class="today-value">12</div><div class="today-label">投注次数</div></div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="menu">';
        $html .= '<div class="menu-title">🛠️ 功能菜单</div>';
        $html .= '<div class="menu-grid">';
        $html .= '<a href="/user/recharge" class="menu-item">💳<br>账户充值</a>';
        $html .= '<a href="/user/withdraw" class="menu-item">💸<br>提现申请</a>';
        $html .= '<a href="/user/orders" class="menu-item">📋<br>投注记录</a>';
        $html .= '<a href="/user/profile" class="menu-item">⚙️<br>个人设置</a>';
        $html .= '<a href="/user/invite" class="menu-item">🎁<br>推广链接</a>';
        $html .= '<a href="/user/help" class="menu-item">❓<br>帮助中心</a>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="nav-bottom">';
        $html .= '<a href="/">🏠 返回首页</a>';
        $html .= '<a href="/run/fangjian?game=bj28">🎮 继续游戏</a>';
        $html .= '<a href="/user/logout">🚪 退出登录</a>';
        $html .= '</div>';
        
        $html .= '</div></body></html>';
        
        return $html;
    }
    
    /**
     * 用户登录
     */
    public function login()
    {
        if (request()->isPost()) {
            $username = input('username');
            $password = input('password');
            
            $user = Db::table('user')
                ->where('username', $username)
                ->find();
                
            if ($user && password_verify($password, $user['password'])) {
                Session::set('user', $user);
                return json(['code' => 1, 'msg' => '登录成功']);
            } else {
                return json(['code' => 0, 'msg' => '用户名或密码错误']);
            }
        }
        
        return View::fetch();
    }
    
    /**
     * 用户注册
     */
    public function register()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['create_time'] = time();
            
            try {
                Db::table('user')->insert($data);
                return json(['code' => 1, 'msg' => '注册成功']);
            } catch (\Exception $e) {
                return json(['code' => 0, 'msg' => '注册失败']);
            }
        }
        
        return View::fetch();
    }
    
    /**
     * 用户退出
     */
    public function logout()
    {
        Session::clear();
        return redirect('/index/index');
    }
    
    /**
     * 投注记录
     */
    public function orders()
    {
        // 模拟投注记录数据
        $records = [];
        for ($i = 1; $i <= 15; $i++) {
            $games = ['BJ28', 'JND28', '幸运飞艇', 'SSC'];
            $types = ['大', '小', '单', '双', '豹子', '顺子'];
            $statuses = ['中奖', '未中奖', '等待开奖'];
            $game = $games[array_rand($games)];
            $type = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            $amount = rand(50, 1000);
            $win_amount = $status == '中奖' ? $amount * rand(15, 30) / 10 : 0;
            
            $records[] = [
                'id' => $i,
                'game' => $game,
                'issue' => date('md') . sprintf('%03d', 100 - $i),
                'bet_type' => $type,
                'bet_amount' => $amount,
                'win_amount' => $win_amount,
                'status' => $status,
                'bet_time' => date('Y-m-d H:i:s', time() - rand(3600, 86400 * $i)),
                'result' => $game . ':' . rand(1, 27)
            ];
        }
        
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>投注记录</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;padding:20px;} .container{max-width:800px;margin:0 auto;} .header{background:rgba(255,255,255,0.95);border-radius:16px;padding:20px;margin-bottom:20px;text-align:center;backdrop-filter:blur(10px);} .header h2{color:#333;margin-bottom:10px;} .filter{display:flex;gap:10px;justify-content:center;margin-bottom:20px;flex-wrap:wrap;} .filter-btn{padding:8px 16px;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);border-radius:20px;color:white;cursor:pointer;transition:all 0.3s;} .filter-btn:hover,.filter-btn.active{background:rgba(255,255,255,0.9);color:#333;} .records{background:rgba(255,255,255,0.95);border-radius:16px;padding:20px;margin-bottom:20px;backdrop-filter:blur(10px);} .record{background:#f8f9fa;border-radius:12px;padding:15px;margin-bottom:15px;border-left:4px solid #667eea;} .record-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:10px;} .game-info{display:flex;align-items:center;gap:10px;} .game-tag{background:#667eea;color:white;padding:2px 8px;border-radius:12px;font-size:12px;font-weight:600;} .issue{color:#666;font-size:14px;} .status{padding:3px 8px;border-radius:12px;font-size:12px;font-weight:600;} .status-win{background:#d4edda;color:#155724;} .status-lose{background:#f8d7da;color:#721c24;} .status-wait{background:#fff3cd;color:#856404;} .record-details{display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;color:#666;font-size:14px;} .detail-item{text-align:center;} .detail-label{font-size:11px;color:#999;} .detail-value{font-weight:600;color:#333;margin-top:2px;} .back-nav{text-align:center;background:rgba(255,255,255,0.1);border-radius:16px;padding:15px;} .back-nav a{color:white;text-decoration:none;margin:0 15px;} .no-records{text-align:center;padding:40px;color:#666;}</style>';
        $html .= '</head><body><div class="container">';
        
        $html .= '<div class="header"><h2>📋 投注记录</h2></div>';
        
        $html .= '<div class="filter">';
        $html .= '<div class="filter-btn active" onclick="filterRecords(\'all\')">全部</div>';
        $html .= '<div class="filter-btn" onclick="filterRecords(\'bj28\')">BJ28</div>';
        $html .= '<div class="filter-btn" onclick="filterRecords(\'jnd28\')">JND28</div>';
        $html .= '<div class="filter-btn" onclick="filterRecords(\'幸运飞艇\')">幸运飞艇</div>';
        $html .= '<div class="filter-btn" onclick="filterRecords(\'ssc\')">SSC</div>';
        $html .= '</div>';
        
        $html .= '<div class="records">';
        foreach ($records as $record) {
            $status_class = $record['status'] == '中奖' ? 'status-win' : ($record['status'] == '未中奖' ? 'status-lose' : 'status-wait');
            
            $html .= '<div class="record" data-game="' . strtolower($record['game']) . '">';
            $html .= '<div class="record-header">';
            $html .= '<div class="game-info">';
            $html .= '<span class="game-tag">' . $record['game'] . '</span>';
            $html .= '<span class="issue">第' . $record['issue'] . '期</span>';
            $html .= '</div>';
            $html .= '<span class="status ' . $status_class . '">' . $record['status'] . '</span>';
            $html .= '</div>';
            
            $html .= '<div class="record-details">';
            $html .= '<div class="detail-item"><div class="detail-label">投注内容</div><div class="detail-value">' . $record['bet_type'] . '</div></div>';
            $html .= '<div class="detail-item"><div class="detail-label">投注金额</div><div class="detail-value">¥' . $record['bet_amount'] . '</div></div>';
            if ($record['win_amount'] > 0) {
                $html .= '<div class="detail-item"><div class="detail-label">中奖金额</div><div class="detail-value">¥' . number_format($record['win_amount']) . '</div></div>';
            }
            $html .= '<div class="detail-item"><div class="detail-label">开奖结果</div><div class="detail-value">' . $record['result'] . '</div></div>';
            $html .= '<div class="detail-item"><div class="detail-label">投注时间</div><div class="detail-value">' . $record['bet_time'] . '</div></div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        $html .= '<div class="back-nav">';
        $html .= '<a href="/user">👤 个人中心</a>';
        $html .= '<a href="/run/fangjian?game=bj28">🎮 继续游戏</a>';
        $html .= '<a href="/">🏠 返回首页</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '<script>';
        $html .= 'function filterRecords(game){document.querySelectorAll(".filter-btn").forEach(btn=>btn.classList.remove("active"));event.target.classList.add("active");document.querySelectorAll(".record").forEach(record=>{if(game==="all"||record.dataset.game===game){record.style.display="block";}else{record.style.display="none";}});}';
        $html .= '</script>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * 充值
     */
    public function recharge()
    {
        if (request()->isPost()) {
            $amount = input('amount');
            $payType = input('pay_type', 'alipay');
            
            if ($amount < 10) {
                return json(['code' => 0, 'msg' => '充值金额最低10元']);
            }
            
            return json(['code' => 1, 'msg' => '充值申请已提交，请等待处理']);
        }
        
        // 充值页面
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>账户充值</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;} .recharge-box{background:white;border-radius:20px;padding:30px;width:100%;max-width:400px;box-shadow:0 20px 40px rgba(0,0,0,0.15);} .title{text-align:center;margin-bottom:25px;} .title h2{color:#333;font-size:24px;margin-bottom:8px;} .title p{color:#666;font-size:14px;} .amount-section{margin-bottom:25px;} .amount-section h4{margin-bottom:15px;color:#333;} .amount-buttons{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:15px;} .amount-btn{padding:12px 8px;border:2px solid #e2e8f0;background:white;border-radius:8px;cursor:pointer;text-align:center;transition:all 0.3s;font-size:14px;} .amount-btn:hover,.amount-btn.active{border-color:#667eea;background:#f0f4ff;color:#667eea;} .custom-amount{width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:8px;font-size:16px;} .pay-section{margin-bottom:25px;} .pay-section h4{margin-bottom:15px;color:#333;} .pay-methods{} .pay-method{display:flex;align-items:center;padding:12px;border:2px solid #e2e8f0;border-radius:8px;margin-bottom:8px;cursor:pointer;transition:all 0.3s;} .pay-method:hover{background:#f8f9fa;} .pay-method input{margin-right:12px;} .pay-method span{font-size:16px;margin-right:8px;} .submit-btn{width:100%;padding:15px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:12px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.3s;} .submit-btn:hover{transform:translateY(-2px);} .back-link{text-align:center;margin-top:20px;} .back-link a{color:#667eea;text-decoration:none;font-size:14px;}</style>';
        $html .= '</head><body><div class="recharge-box">';
        $html .= '<div class="title"><h2>💳 账户充值</h2><p>选择充值金额和支付方式</p></div>';
        
        $html .= '<div class="amount-section"><h4>选择金额</h4>';
        $html .= '<div class="amount-buttons">';
        $amounts = [100, 500, 1000, 2000, 5000, 10000];
        foreach ($amounts as $amount) {
            $html .= '<div class="amount-btn" onclick="selectAmount(' . $amount . ')">¥' . $amount . '</div>';
        }
        $html .= '</div>';
        $html .= '<input type="number" class="custom-amount" placeholder="或输入其他金额（最低10元）" min="10" max="100000" id="amountInput">';
        $html .= '</div>';
        
        $html .= '<div class="pay-section"><h4>支付方式</h4>';
        $html .= '<div class="pay-methods">';
        $html .= '<label class="pay-method"><input type="radio" name="pay" value="alipay" checked><span>💰</span>支付宝</label>';
        $html .= '<label class="pay-method"><input type="radio" name="pay" value="wechat"><span>💚</span>微信支付</label>';
        $html .= '<label class="pay-method"><input type="radio" name="pay" value="bank"><span>🏦</span>银行卡</label>';
        $html .= '</div></div>';
        
        $html .= '<button class="submit-btn" onclick="submitRecharge()">立即充值</button>';
        $html .= '<div class="back-link"><a href="/user">← 返回个人中心</a></div>';
        $html .= '</div>';
        
        $html .= '<script>';
        $html .= 'function selectAmount(amount){document.getElementById("amountInput").value=amount;document.querySelectorAll(".amount-btn").forEach(btn=>btn.classList.remove("active"));event.target.classList.add("active");}';
        $html .= 'function submitRecharge(){const amount=document.getElementById("amountInput").value;const payType=document.querySelector("input[name=pay]:checked").value;if(!amount||amount<10){alert("请输入有效金额（最低10元）");return;}const formData=new FormData();formData.append("amount",amount);formData.append("pay_type",payType);fetch("/user/recharge",{method:"POST",body:formData}).then(response=>response.json()).then(data=>{alert(data.msg);if(data.code===1){window.location.href="/user";}}).catch(error=>{alert("充值失败，请重试");});}';
        $html .= '</script>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * 提现
     */
    public function withdraw()
    {
        if (request()->isPost()) {
            $amount = input('amount');
            $bankCard = input('bank_card');
            $realName = input('real_name');
            
            if ($amount < 100) {
                return json(['code' => 0, 'msg' => '提现金额最低100元']);
            }
            
            if ($amount > 12500) {
                return json(['code' => 0, 'msg' => '余额不足']);
            }
            
            return json(['code' => 1, 'msg' => '提现申请已提交，请等待审核']);
        }
        
        // 提现页面
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>申请提现</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:linear-gradient(135deg,#fa709a,#fee140);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;} .withdraw-box{background:white;border-radius:20px;padding:30px;width:100%;max-width:400px;box-shadow:0 20px 40px rgba(0,0,0,0.15);} .title{text-align:center;margin-bottom:25px;} .title h2{color:#333;font-size:24px;margin-bottom:8px;} .title p{color:#666;font-size:14px;} .balance-info{background:#f8f9fa;border-radius:12px;padding:15px;margin-bottom:20px;text-align:center;} .balance-label{color:#666;font-size:14px;margin-bottom:5px;} .balance-amount{color:#e74c3c;font-size:24px;font-weight:700;} .form-group{margin-bottom:20px;} .form-group label{display:block;margin-bottom:8px;color:#333;font-weight:500;} .form-group input{width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:8px;font-size:16px;transition:border-color 0.3s;} .form-group input:focus{outline:none;border-color:#fa709a;} .amount-tips{background:#fff3cd;border:1px solid #ffeaa7;border-radius:6px;padding:10px;margin-bottom:20px;font-size:14px;color:#856404;} .submit-btn{width:100%;padding:15px;background:linear-gradient(135deg,#fa709a,#fee140);color:white;border:none;border-radius:12px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.3s;} .submit-btn:hover{transform:translateY(-2px);} .back-link{text-align:center;margin-top:20px;} .back-link a{color:#fa709a;text-decoration:none;font-size:14px;}</style>';
        $html .= '</head><body><div class="withdraw-box">';
        $html .= '<div class="title"><h2>💸 申请提现</h2><p>填写提现信息，等待审核</p></div>';
        
        $html .= '<div class="balance-info">';
        $html .= '<div class="balance-label">当前余额</div>';
        $html .= '<div class="balance-amount">¥12,500</div>';
        $html .= '</div>';
        
        $html .= '<div class="amount-tips">💡 提示：最低提现金额100元，手续费3%，1-3个工作日到账</div>';
        
        $html .= '<form id="withdrawForm">';
        $html .= '<div class="form-group"><label>提现金额</label><input type="number" name="amount" placeholder="请输入提现金额" min="100" max="12500" required></div>';
        $html .= '<div class="form-group"><label>真实姓名</label><input type="text" name="real_name" placeholder="请输入银行卡开户姓名" required></div>';
        $html .= '<div class="form-group"><label>银行卡号</label><input type="text" name="bank_card" placeholder="请输入银行卡号" required></div>';
        $html .= '<button type="submit" class="submit-btn">提交申请</button>';
        $html .= '</form>';
        
        $html .= '<div class="back-link"><a href="/user">← 返回个人中心</a></div>';
        $html .= '</div>';
        
        $html .= '<script>';
        $html .= 'document.getElementById("withdrawForm").addEventListener("submit",function(e){e.preventDefault();const formData=new FormData(this);const amount=formData.get("amount");if(!amount||amount<100){alert("提现金额最低100元");return;}if(amount>12500){alert("提现金额超过可用余额");return;}fetch("/user/withdraw",{method:"POST",body:formData}).then(response=>response.json()).then(data=>{alert(data.msg);if(data.code===1){window.location.href="/user";}}).catch(error=>{alert("提现申请失败，请重试");});});';
        $html .= '</script>';
        $html .= '</body></html>';
        
        return $html;
    }
}