<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\View;

/**
 * 收银台控制器
 */
class Shou extends BaseController
{
    /**
     * 收银台首页
     */
    public function index()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            return redirect('/user/login');
        }
        
        return View::fetch();
    }
    
    /**
     * 支付页面
     */
    public function pay()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            return redirect('/user/login');
        }
        
        $order_id = request()->param('order_id');
        if (!$order_id) {
            return '订单不存在';
        }
        
        // 获取订单信息
        $order = Db::table('addmoney_log')->where('id', $order_id)->where('user_id', $user_id)->find();
        if (!$order) {
            return '订单不存在或无权限';
        }
        
        View::assign('order', $order);
        return View::fetch();
    }
    
    /**
     * 支付回调
     */
    public function callback()
    {
        // 这里处理第三方支付回调逻辑
        $data = request()->post();
        
        // 验证回调数据
        // TODO: 根据实际支付平台实现验证逻辑
        
        return 'success';
    }
}