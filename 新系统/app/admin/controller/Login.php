<?php
declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Session;
use think\facade\View;

/**
 * 管理后台登录控制器
 */
class Login extends BaseController
{
    /**
     * 登录页面
     */
    public function index()
    {
        if (Session::has('admin.id')) {
            return redirect('/admin/index');
        }
        
        return View::fetch();
    }
    
    /**
     * 执行登录
     */
    public function login()
    {
        if (!request()->isPost()) {
            return json(['code' => 0, 'msg' => '请求方式错误']);
        }
        
        $username = request()->post('username', '');
        $password = request()->post('password', '');
        $remember = request()->post('remember', 0);
        
        if (empty($username) || empty($password)) {
            return json(['code' => 0, 'msg' => '用户名和密码不能为空']);
        }
        
        // 查找管理员
        $admin = Db::table('admin')
                  ->where('username', $username)
                  ->where('password', md5($password))
                  ->where('status', 1)
                  ->find();
        
        if (!$admin) {
            return json(['code' => 0, 'msg' => '用户名或密码错误']);
        }
        
        // 更新登录信息
        Db::table('admin')->where('id', $admin['id'])->update([
            'last_ip' => request()->ip(),
            'last_time' => time()
        ]);
        
        // 设置session
        if ($remember) {
            Session::set('admin', $admin, 3600 * 24 * 3); // 3天
        } else {
            Session::set('admin', $admin, 3600); // 1小时
        }
        
        return json(['code' => 1, 'msg' => '登录成功', 'url' => '/admin/index']);
    }
    
    /**
     * 退出登录
     */
    public function logout()
    {
        Session::delete('admin');
        return redirect('/admin/login');
    }
}