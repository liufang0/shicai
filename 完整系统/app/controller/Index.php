<?php

namespace app\controller;

use think\facade\View;

class Index 
{
    /**
     * 首页 - 完整的游戏大厅
     */
    public function index()
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>食彩游戏平台</title>
    <style>
        body { 
            font-family: "Microsoft YaHei", Arial, sans-serif; 
            margin: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .header {
            background: rgba(255,255,255,0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .nav {
            display: flex;
            gap: 20px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav a:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .games {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .game-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s;
            cursor: pointer;
        }
        .game-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
        }
        .game-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        h1 { color: #333; text-align: center; margin-bottom: 20px; }
        h3 { color: #555; margin-bottom: 10px; }
        .status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            margin-top: 10px;
        }
        .status.online {
            background: #d4edda;
            color: #155724;
        }
        .admin-panel {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🎮 食彩游戏平台</div>
        <div class="nav">
            <a href="/admin/login">管理后台</a>
            <a href="/run/bj28">北京28</a>
            <a href="/run/ssc">时时彩</a>
        </div>
    </div>
    
    <div class="container">
        <h1>欢迎访问食彩游戏平台</h1>
        
        <div class="games">
            <div class="game-card" onclick="window.open(\'/run/bj28\')">
                <div class="game-icon">🎲</div>
                <h3>北京28</h3>
                <p>经典数字竞猜游戏</p>
                <div class="status online">运行中</div>
            </div>
            
            <div class="game-card" onclick="window.open(\'/run/ssc\')">
                <div class="game-icon">⏰</div>
                <h3>时时彩</h3>
                <p>快节奏数字游戏</p>
                <div class="status online">运行中</div>
            </div>
            
            <div class="game-card" onclick="window.open(\'/run/幸运飞艇\')">
                <div class="game-icon">🚁</div>
                <h3>幸运飞艇</h3>
                <p>刺激飞艇竞速</p>
                <div class="status online">运行中</div>
            </div>
            
            <div class="game-card" onclick="window.open(\'/run/jnd28\')">
                <div class="game-icon">💎</div>
                <h3>加拿大28</h3>
                <p>国际热门游戏</p>
                <div class="status online">运行中</div>
            </div>
        </div>
        
        <div class="admin-panel">
            <h3>🔧 管理功能</h3>
            <p>
                <a href="/admin/login" style="display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin:5px;">管理后台登录</a>
                <a href="/admin-control/get-system-stats" style="display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;margin:5px;">系统统计API</a>
                <a href="/admin-control/get-user-list" style="display:inline-block;padding:10px 20px;background:#17a2b8;color:white;text-decoration:none;border-radius:5px;margin:5px;">用户管理API</a>
            </p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
}
