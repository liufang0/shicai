<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 演示控制器 - 展示系统完整功能
 */
class Demo 
{
    /**
     * 系统功能演示页面
     */
    public function index()
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>🎮 数字竞猜游戏平台 - 功能演示</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:"Microsoft YaHei",Arial;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;padding:20px;} .container{max-width:1200px;margin:0 auto;} .header{background:rgba(255,255,255,0.95);border-radius:20px;padding:30px;margin-bottom:30px;text-align:center;backdrop-filter:blur(10px);box-shadow:0 10px 30px rgba(0,0,0,0.2);} .header h1{font-size:36px;color:#2c3e50;margin-bottom:10px;} .header p{color:#7f8c8d;font-size:18px;} .demo-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:25px;margin-bottom:30px;} .demo-card{background:rgba(255,255,255,0.95);border-radius:16px;padding:25px;backdrop-filter:blur(10px);box-shadow:0 8px 25px rgba(0,0,0,0.15);transition:all 0.3s;} .demo-card:hover{transform:translateY(-5px);box-shadow:0 15px 40px rgba(0,0,0,0.2);} .card-icon{font-size:48px;text-align:center;margin-bottom:15px;} .card-title{font-size:20px;font-weight:600;color:#2c3e50;margin-bottom:10px;text-align:center;} .card-desc{color:#7f8c8d;font-size:14px;margin-bottom:20px;text-align:center;line-height:1.6;} .demo-links{display:flex;flex-direction:column;gap:10px;} .demo-link{display:block;padding:12px 20px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border-radius:8px;text-align:center;font-weight:500;transition:all 0.3s;} .demo-link:hover{opacity:0.9;transform:translateY(-2px);} .status-bar{background:rgba(255,255,255,0.95);border-radius:16px;padding:20px;backdrop-filter:blur(10px);box-shadow:0 8px 25px rgba(0,0,0,0.15);} .status-title{font-size:18px;font-weight:600;color:#2c3e50;margin-bottom:15px;text-align:center;} .status-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:15px;} .status-item{text-align:center;padding:10px;background:rgba(245,245,245,0.8);border-radius:8px;} .status-value{font-size:20px;font-weight:700;color:#e74c3c;} .status-label{font-size:12px;color:#7f8c8d;margin-top:5px;}</style>';
        $html .= '</head><body><div class="container">';
        
        $html .= '<div class="header">';
        $html .= '<h1>🎮 数字竞猜游戏平台</h1>';
        $html .= '<p>完整的在线游戏系统 - 前台用户界面 + 后台管理系统</p>';
        $html .= '</div>';
        
        $html .= '<div class="demo-grid">';
        
        // 前台功能卡片
        $html .= '<div class="demo-card">';
        $html .= '<div class="card-icon">🏠</div>';
        $html .= '<div class="card-title">前台用户系统</div>';
        $html .= '<div class="card-desc">用户注册登录、游戏大厅、个人中心、充值提现等完整功能</div>';
        $html .= '<div class="demo-links">';
        $html .= '<a href="/" class="demo-link">🏠 系统首页</a>';
        $html .= '<a href="/run/fangjian?game=bj28" class="demo-link">🎮 游戏大厅</a>';
        $html .= '<a href="/user" class="demo-link">👤 用户中心</a>';
        $html .= '</div>';
        $html .= '</div>';
        
        // 游戏功能卡片
        $html .= '<div class="demo-card">';
        $html .= '<div class="card-icon">🎯</div>';
        $html .= '<div class="card-title">游戏中心</div>';
        $html .= '<div class="card-desc">北京28、加拿大28、幸运飞艇、时时彩等多种竞猜游戏</div>';
        $html .= '<div class="demo-links">';
        $html .= '<a href="/run/bj28?room=1" class="demo-link">🎲 北京28</a>';
        $html .= '<a href="/run/jnd28?room=1" class="demo-link">🍀 加拿大28</a>';
        $html .= '<a href="/run/幸运飞艇?room=1" class="demo-link">🏎️ 幸运飞艇</a>';
        $html .= '<a href="/run/ssc?room=1" class="demo-link">⏰ 时时彩</a>';
        $html .= '</div>';
        $html .= '</div>';
        
        // 用户功能卡片
        $html .= '<div class="demo-card">';
        $html .= '<div class="card-icon">💰</div>';
        $html .= '<div class="card-title">用户服务</div>';
        $html .= '<div class="card-desc">账户充值、提现申请、投注记录、个人设置等服务</div>';
        $html .= '<div class="demo-links">';
        $html .= '<a href="/user/recharge" class="demo-link">💳 账户充值</a>';
        $html .= '<a href="/user/withdraw" class="demo-link">💸 提现申请</a>';
        $html .= '<a href="/user/orders" class="demo-link">📋 投注记录</a>';
        $html .= '</div>';
        $html .= '</div>';
        
        // 管理后台卡片
        $html .= '<div class="demo-card">';
        $html .= '<div class="card-icon">⚙️</div>';
        $html .= '<div class="card-title">管理后台</div>';
        $html .= '<div class="card-desc">用户管理、投注管理、财务管理、系统设置等管理功能</div>';
        $html .= '<div class="demo-links">';
        $html .= '<a href="/admin/login" class="demo-link">🔐 后台登录</a>';
        $html .= '<a href="/admin" class="demo-link">📊 管理首页</a>';
        $html .= '<a href="/admin/userList" class="demo-link">👥 用户管理</a>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // 系统状态
        $html .= '<div class="status-bar">';
        $html .= '<div class="status-title">🚀 系统运行状态</div>';
        $html .= '<div class="status-grid">';
        $html .= '<div class="status-item"><div class="status-value">✅</div><div class="status-label">前台系统</div></div>';
        $html .= '<div class="status-item"><div class="status-value">✅</div><div class="status-label">游戏引擎</div></div>';
        $html .= '<div class="status-item"><div class="status-value">✅</div><div class="status-label">用户中心</div></div>';
        $html .= '<div class="status-item"><div class="status-value">✅</div><div class="status-label">管理后台</div></div>';
        $html .= '<div class="status-item"><div class="status-value">✅</div><div class="status-label">数据库</div></div>';
        $html .= '<div class="status-item"><div class="status-value">8080</div><div class="status-label">运行端口</div></div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div></body></html>';
        
        return $html;
    }
}