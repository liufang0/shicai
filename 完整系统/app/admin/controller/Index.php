<?php

namespace app\admin\controller;

use app\admin\controller\BaseController;

class Index extends BaseController
{
    public function index()
    {
        // 统计数据（模拟）
        $stats = [
            'today_users' => rand(150, 300),
            'today_orders' => rand(500, 1200), 
            'today_amount' => rand(50000, 120000),
            'online_users' => rand(80, 200)
        ];
        
        $html = '<!DOCTYPE html><html lang="zh-CN"><head>';
        $html .= '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<title>后台管理系统</title>';
        $html .= '<style>
            *{margin:0;padding:0;box-sizing:border-box;}
            body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","Microsoft YaHei",sans-serif;background:#f0f2f5;}
            .header{background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between;}
            .logo{font-size:20px;font-weight:600;color:#1890ff;}
            .nav{display:flex;gap:30px;}
            .nav a{color:#333;text-decoration:none;padding:5px 0;border-bottom:2px solid transparent;transition:all 0.3s;}
            .nav a:hover,.nav a.active{color:#1890ff;border-bottom-color:#1890ff;}
            .main{display:flex;min-height:calc(100vh - 60px);}
            .sidebar{width:200px;background:#fff;box-shadow:2px 0 8px rgba(0,0,0,0.1);}
            .menu{padding:20px 0;}
            .menu a{display:block;padding:12px 20px;color:#333;text-decoration:none;border-left:3px solid transparent;transition:all 0.3s;}
            .menu a:hover,.menu a.active{background:#f0f7ff;color:#1890ff;border-left-color:#1890ff;}
            .content{flex:1;padding:20px;}
            .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;}
            .stat-card{background:#fff;border-radius:8px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
            .stat-card h3{color:#666;font-size:14px;margin:0 0 8px;}
            .stat-card .number{font-size:28px;font-weight:600;color:#333;}
            .stat-card .trend{font-size:12px;color:#52c41a;margin-top:8px;}
            .card{background:#fff;border-radius:8px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:20px;}
            .card-title{font-size:16px;font-weight:600;margin:0 0 16px;color:#333;}
            .game-controls{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;}
            .game-control{border:1px solid #d9d9d9;border-radius:8px;padding:16px;}
            .game-control h4{margin:0 0 12px;color:#333;}
            .control-btn{padding:8px 16px;border:none;border-radius:4px;cursor:pointer;margin:4px;font-size:14px;transition:all 0.3s;}
            .btn-start{background:#52c41a;color:#fff;}
            .btn-stop{background:#ff4d4f;color:#fff;}
            .btn-config{background:#1890ff;color:#fff;}
            .status{display:inline-block;padding:4px 8px;border-radius:12px;font-size:12px;font-weight:600;}
            .status.running{background:#f6ffed;color:#52c41a;}
            .status.stopped{background:#fff2f0;color:#ff4d4f;}
        </style></head><body>';
        
        $html .= '<div class="header">';
        $html .= '<div class="logo">🎮 游戏管理后台</div>';
        $html .= '<div class="nav">';
        $html .= '<a href="/admin" class="active">首页</a>';
        $html .= '<a href="/" target="_blank">前台</a>';
        $html .= '<a href="/admin/logout">退出</a>';
        $html .= '</div></div>';
        
        $html .= '<div class="main">';
        $html .= '<div class="sidebar"><div class="menu">';
        $html .= '<a href="/admin" class="active">📊 数据概览</a>';
        $html .= '<a href="/admin/game/control">🎮 游戏控制</a>';
        $html .= '<a href="/admin/lottery/control">🎲 开奖控制</a>';
        $html .= '<a href="/admin/users">👥 用户管理</a>';
        $html .= '<a href="/admin/financial">💰 财务管理</a>';
        $html .= '</div></div>';
        
        $html .= '<div class="content">';
        $html .= '<div class="stats">';
        $html .= '<div class="stat-card"><h3>今日新增用户</h3><div class="number">' . number_format($stats['today_users']) . '</div><div class="trend">↗ +12.5%</div></div>';
        $html .= '<div class="stat-card"><h3>今日订单</h3><div class="number">' . number_format($stats['today_orders']) . '</div><div class="trend">↗ +8.3%</div></div>';
        $html .= '<div class="stat-card"><h3>今日流水</h3><div class="number">¥' . number_format($stats['today_amount']) . '</div><div class="trend">↗ +15.7%</div></div>';
        $html .= '<div class="stat-card"><h3>在线用户</h3><div class="number">' . number_format($stats['online_users']) . '</div><div class="trend">实时数据</div></div>';
        $html .= '</div>';
        
        $html .= '<div class="card">';
        $html .= '<div class="card-title">🎮 游戏控制面板</div>';
        $html .= '<div class="game-controls">';
        
        $games = [
            'bj28' => ['name' => '北京28', 'status' => 'running'],
            'ssc' => ['name' => '时时彩', 'status' => 'running']
        ];
        
        foreach ($games as $code => $game) {
            $html .= '<div class="game-control">';
            $html .= '<h4>🎲 ' . $game['name'] . ' <span class="status ' . $game['status'] . '">' . ($game['status'] == 'running' ? '运行中' : '已停止') . '</span></h4>';
            $html .= '<button class="control-btn btn-start" onclick="controlGame(\'' . $code . '\', \'start\')">启动游戏</button>';
            $html .= '<button class="control-btn btn-stop" onclick="controlGame(\'' . $code . '\', \'stop\')">停止游戏</button>';
            $html .= '<button class="control-btn btn-config" onclick="window.open(\'/rule/' . $code . '\')">查看规则</button>';
            $html .= '</div>';
        }
        
        $html .= '</div></div></div></div>';
        
        $html .= '<script>
            function controlGame(game, action) {
                alert("游戏控制：" + game + " - " + action);
            }
        </script></body></html>';
        
        return $html;
    }
    
    public function users()
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>用户管理</title></head>';
        $html .= '<body><h1>用户管理页面</h1><p>此页面用于管理用户信息、充值记录等</p></body></html>';
        return $html;
    }
    
    public function financial()
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>财务管理</title></head>';
        $html .= '<body><h1>财务管理页面</h1><p>此页面用于管理充值、提现、佣金等财务数据</p></body></html>';
        return $html;
    }
}