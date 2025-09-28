<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\View;

/**
 * 充值页面控制器
 */
class Fen extends BaseController
{
    /**
     * 充值页面
     */
    public function addpage()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            return redirect('/user/login');
        }
        
        // 获取支付配置
        $pay = [];
        
        if (config('agent_pay') == '1') {
            // 代理商支付
            $user = Db::table('user')->where('id', $user_id)->find();
            $agentinfo = Db::table('user')->where('id', $user['t_id'])->find();
            $pay = $agentinfo;
        } else {
            // 系统支付
            $wx_config = Db::table('config')->where('id', 2)->find();
            $pay['wx_paycode'] = $wx_config['kefu'];
            
            $zfb_config = Db::table('config')->where('id', 3)->find();
            $pay['zfb_paycode'] = $zfb_config['kefu'];
            
            $bank_config = Db::table('config')->where('id', 4)->find();
            $pay['bank_info'] = json_decode($bank_config['kefu'], true);
        }
        
        $user = Db::table('user')->where('id', $user_id)->find();
        
        View::assign('pay', $pay);
        View::assign('user', $user);
        return View::fetch();
    }
    
    /**
     * 提交充值订单
     */
    public function add()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '请先登录']);
        }
        
        $data = request()->post();
        $money = floatval($data['money'] ?? 0);
        
        if ($money <= 0) {
            return json(['code' => 0, 'msg' => '充值金额不能为0']);
        }
        
        // 创建充值订单
        $order_data = [
            'user_id' => $user_id,
            'money' => $money,
            'type' => $data['type'] ?? 'online',
            'status' => 0, // 待审核
            'create_time' => time(),
            'ip' => request()->ip()
        ];
        
        $order_id = Db::table('addmoney_log')->insertGetId($order_data);
        
        if ($order_id) {
            return json(['code' => 1, 'msg' => '充值申请提交成功，等待审核']);
        } else {
            return json(['code' => 0, 'msg' => '充值申请提交失败']);
        }
    }
    
    /**
     * 充值记录
     */
    public function log()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            return redirect('/user/login');
        }
        
        $page = request()->param('page', 1);
        $limit = 20;
        
        $logs = Db::table('addmoney_log')
                 ->where('user_id', $user_id)
                 ->order('create_time', 'desc')
                 ->page($page, $limit)
                 ->select();
        
        View::assign('logs', $logs);
        return View::fetch();
    }
}