<?php
namespace app\controller;

class AdminTest 
{
    public function index()
    {
        return '<h1>Admin测试页面</h1>
        <p>如果您看到这个页面，说明系统运行正常</p>
        <p><a href="/admin-test/login">登录页面测试</a></p>
        <p><a href="/index.php/admin/login">完整admin登录</a></p>';
    }
    
    public function login()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理员登录</title>
    <style>
        body { font-family: Arial; background: #f5f7fa; padding: 50px; }
        .login-box { 
            max-width: 400px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
        .form-group { margin: 15px 0; }
        .form-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }
        .btn { 
            width: 100%; 
            padding: 12px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 管理员登录</h2>
        <p>测试访问地址：</p>
        <ul>
            <li><a href="http://localhost:8080/index.php/admin/login">完整路径登录</a></li>
            <li><a href="http://localhost:8080/admin/login">简化路径登录</a></li>
        </ul>
        <hr>
        <form>
            <div class="form-group">
                <input type="text" placeholder="用户名: admin" value="admin">
            </div>
            <div class="form-group">
                <input type="password" placeholder="密码: admin">
            </div>
            <button type="button" class="btn" onclick="testLogin()">测试登录</button>
        </form>
        <div id="result" style="margin-top: 20px;"></div>
    </div>
    
    <script>
        function testLogin() {
            document.getElementById("result").innerHTML = "正在测试登录功能...";
            
            fetch("/admin/login", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "username=admin&password=admin"
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("result").innerHTML = 
                    "<strong>测试结果:</strong><br>" + JSON.stringify(data, null, 2);
            })
            .catch(error => {
                document.getElementById("result").innerHTML = 
                    "<strong style=\"color:red;\">错误:</strong> " + error.message;
            });
        }
    </script>
</body>
</html>';
    }
}