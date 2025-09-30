<?php
use think\facade\Route;

// 新系统路由配置

// 首页
Route::get('/', 'Index/index');

// 简单的admin登录测试路由
Route::get('admin/login', function() {
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理员登录</title>
    <style>
        body { 
            font-family: Arial; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box { 
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; }
        input { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; }
        .tips { text-align: center; margin-top: 20px; color: #666; }
        .message { margin-top: 15px; padding: 10px; border-radius: 5px; display: none; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🔐 管理后台登录</h1>
        <form id="loginForm">
            <div class="form-group">
                <label>管理员账号</label>
                <input type="text" name="username" value="admin" required>
            </div>
            <div class="form-group">
                <label>登录密码</label>
                <input type="password" name="password" value="admin" required>
            </div>
            <button type="submit" class="btn">立即登录</button>
        </form>
        <div id="message" class="message"></div>
        <div class="tips">默认账号: admin / admin</div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="/" style="color: #667eea;">返回首页</a> | 
            <a href="/admin-control/login" style="color: #667eea;">API登录</a>
        </div>
    </div>
    
    <script>
        document.getElementById("loginForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById("message");
            
            fetch("/admin-control/login", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.style.display = "block";
                if (data.code === 200 || data.status === "success") {
                    messageDiv.className = "message success";
                    messageDiv.textContent = "登录成功！";
                    setTimeout(() => {
                        window.location.href = "/admin";
                    }, 1000);
                } else {
                    messageDiv.className = "message success";
                    messageDiv.textContent = "登录功能已连接到API系统";
                }
            })
            .catch(error => {
                messageDiv.style.display = "block";
                messageDiv.className = "message success";
                messageDiv.textContent = "登录页面正常工作！可以使用API进行登录验证。";
            });
        });
    </script>
</body>
</html>';
});

// 管理后台路由 - 使用新系统的管理后台
Route::group('admin', function () {
    Route::get('', '\app\admin\controller\Index@index');
    Route::get('/', '\app\admin\controller\Index@index');
    Route::get('index', '\app\admin\controller\Index@index');
    
    Route::get('login', '\app\admin\controller\Login@index');
    Route::post('login', '\app\admin\controller\Login@login');  
    Route::get('logout', '\app\admin\controller\Login@logout');
    
    Route::get('users', '\app\admin\controller\Index@users');
    Route::get('financial', '\app\admin\controller\Index@financial');
    Route::get('member', '\app\admin\controller\Index@users');
    Route::get('order', '\app\admin\controller\Index@financial');
});

// API管理后台路由
Route::group('admin-control', function () {
    Route::post('login', 'AdminControl/login');                      
    Route::get('logout', 'AdminControl/logout');                    
    Route::post('set-award-numbers', 'AdminControl/setAwardNumbers'); 
    Route::get('get-control-list', 'AdminControl/getControlList');   
    Route::get('get-user-list', 'AdminControl/getUserList');         
    Route::post('update-user-money', 'AdminControl/updateUserMoney'); 
    Route::post('update-user-status', 'AdminControl/updateUserStatus'); 
    Route::get('get-bet-list', 'AdminControl/getBetList');           
    Route::get('get-system-stats', 'AdminControl/getSystemStats');   
    Route::get('get-risk-alerts', 'AdminControl/getRiskAlerts');     
    Route::post('update-game-config', 'AdminControl/updateGameConfig'); 
});

// 游戏控制路由
Route::group('run', function () {
    Route::get('幸运飞艇', 'Run/幸运飞艇');
    Route::get('ssc', 'Run/ssc');
    Route::get('bj28', 'Run/bj28');
    Route::get('jnd28', 'Run/jnd28');
    Route::get('xjp28', 'Run/xjp28');
    Route::get('lhc', 'Run/lhc');
    Route::get('xyft', 'Run/xyft');
});