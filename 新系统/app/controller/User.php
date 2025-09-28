<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Session;

class User extends BaseController
{
    /**
     * 会员中心首页
     */
    public function index()
    {
        $userid = Session::get('user');
        
        // 统计用户积分数据
        $points_tj = Db::table('order')
            ->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where([
                'state' => 1,
                'userid' => $userid['id']
            ])
            ->find();
            
        $points_tj['sum_add'] = $points_tj['sum_add'] ?: 0;
        $points_tj['sum_del'] = $points_tj['sum_del'] ?: 0;
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
        
        View::assign('points_tj', $points_tj);
        
        // 授权检查
        $auth = auth_check(config('auth_code'), $_SERVER['HTTP_HOST']);
        if (!$auth) {
            echo "未授权或授权已过期";
            exit;
        }
        
        $userinfo = Db::table('user')->where("id", $userid['id'])->find();
        
        // 下线总数
        $userinfo['t_account'] = Db::table('user')->where("t_id", $userid['id'])->count() ?: 0;
        
        // 今日盈亏
        $beginToday = mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'));
        $endToday = mktime(0, 0, 0, (int)date('m'), (int)date('d') + 1, (int)date('Y')) - 1;
        
        $yinkui = Db::table('order')
            ->field("sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where([
                ['time', '>=', $beginToday],
                ['time', '<=', $endToday],
                ['state', '=', 1],
                ['userid', '=', $userid['id']]
            ])
            ->find();
            
        $userinfo['ying'] = $yinkui['sum_add'] - $yinkui['sum_del'];
        $userinfo['sum_add'] = $yinkui['sum_add'];
        
        // 今日推广佣金
        $commission = Db::table('commission')
            ->where([
                ['time', '>=', $beginToday],
                ['time', '<=', $endToday],
                ['t_uid', '=', $userid['id']]
            ])
            ->sum('money');
            
        $userinfo['commission'] = $commission ?: 0;
        
        View::assign('userinfo', $userinfo);
        
        return View::fetch();
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
     * 用户资金明细
     */
    public function money()
    {
        $userid = Session::get('user');
        
        $list = Db::table('order')
            ->where('userid', $userid['id'])
            ->order('id desc')
            ->paginate(20);
            
        View::assign('list', $list);
        
        return View::fetch();
    }
    
    /**
     * 充值
     */
    public function recharge()
    {
        if (request()->isPost()) {
            $userid = Session::get('user');
            $amount = input('amount');
            
            // 创建充值订单
            $order = [
                'userid' => $userid['id'],
                'amount' => $amount,
                'type' => 'recharge',
                'status' => 0,
                'create_time' => time()
            ];
            
            Db::table('recharge')->insert($order);
            
            return json(['code' => 1, 'msg' => '充值申请已提交']);
        }
        
        return View::fetch();
    }
    
    /**
     * 提现
     */
    public function withdraw()
    {
        if (request()->isPost()) {
            $userid = Session::get('user');
            $amount = input('amount');
            
            $userinfo = Db::table('user')->where('id', $userid['id'])->find();
            
            if ($userinfo['money'] < $amount) {
                return json(['code' => 0, 'msg' => '余额不足']);
            }
            
            // 创建提现订单
            $order = [
                'userid' => $userid['id'],
                'amount' => $amount,
                'type' => 'withdraw',
                'status' => 0,
                'create_time' => time()
            ];
            
            Db::table('withdraw')->insert($order);
            
            // 扣除用户余额
            Db::table('user')->where('id', $userid['id'])->dec('money', $amount);
            
            return json(['code' => 1, 'msg' => '提现申请已提交']);
        }
        
        return View::fetch();
    }
}