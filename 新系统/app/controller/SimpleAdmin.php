<?php
namespace app\controller;

class SimpleAdmin
{
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
        .form-group label { display: block; margin-bottom: 5px; }
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
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>管理员登录</h2>
        <form method="post">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">登录</button>
        </form>
        <p style="text-align: center; margin-top: 20px; color: #666;">
            默认账号: admin / 123456
        </p>
    </div>
</body>
</html>';
    }
    
    public function index()
    {
        return '<h1>管理后台首页</h1><p><a href="/simple-admin/login">返回登录</a></p>';
    }
}