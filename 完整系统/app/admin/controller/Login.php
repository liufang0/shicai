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
        if (Session::has('admin')) {
            return redirect('/admin/index');
        }
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理系统</title>
    <style>
        body {
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI","Microsoft YaHei",sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: opacity 0.3s;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .tips {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 管理后台</h1>
        <form id="LoginForm" method="post">
            <div class="form-group">
                <label for="username">管理员账号</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">登录密码</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">立即登录</button>
        </form>
        <div id="message" class="message"></div>
        <div class="tips">
            默认账号: admin / admin
        </div>
    </div>
    
    <script>
        document.getElementById("LoginForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById("message");
            
            fetch("/admin/login", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.style.display = "block";
                if (data.code === 1) {
                    messageDiv.className = "message success";
                    messageDiv.textContent = data.msg;
                    setTimeout(() => {
                        window.location.href = data.url || "/admin";
                    }, 1000);
                } else {
                    messageDiv.className = "message error";
                    messageDiv.textContent = data.msg;
                }
            })
            .catch(error => {
                messageDiv.style.display = "block";
                messageDiv.className = "message error";
                messageDiv.textContent = "登录请求失败，请检查网络连接";
            });
        });
    </script>
</body>
</html>';
        
        return $html;
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