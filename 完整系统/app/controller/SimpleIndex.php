<?php

namespace app\controller;

class SimpleIndex
{
    public function index()
    {
        return $this->showHomePage();
    }
    
    public function login()
    {
        if (request()->isPost()) {
            $username = input('post.username');
            $password = input('post.password');
            
            // 模拟登录验证
            if ($username && $password) {
                // 设置session
                session('user', [
                    'id' => 1,
                    'username' => $username,
                    'points' => 10000
                ]);
                
                return json(['code' => 1, 'msg' => '登录成功', 'url' => '/simple/index']);
            }
            
            return json(['code' => 0, 'msg' => '用户名或密码错误']);
        }
        
        return $this->showLoginPage();
    }
    
    private function showHomePage()
    {
        $userinfo = session('user');
        
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>🚀 蚂蚁数字科技 - 首页</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>
            body{font-family:"Microsoft YaHei",Arial;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;margin:0;color:#333;}
            .header{background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);padding:15px 20px;color:white;display:flex;justify-content:space-between;align-items:center;}
            .logo{font-size:20px;font-weight:700;}
            .user-info{font-size:14px;}
            .container{max-width:800px;margin:20px auto;padding:20px;}
            .welcome{background:rgba(255,255,255,0.95);border-radius:15px;padding:30px;margin-bottom:20px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.1);}
            .game-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;}
            .game-card{background:rgba(255,255,255,0.95);border-radius:15px;padding:25px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.1);transition:transform 0.3s;}
            .game-card:hover{transform:translateY(-5px);}
            .game-title{font-size:24px;font-weight:600;margin-bottom:10px;color:#333;}
            .game-desc{color:#666;margin-bottom:20px;font-size:14px;}
            .btn{background:linear-gradient(135deg,#667eea,#764ba2);border:none;padding:12px 25px;border-radius:25px;color:white;font-size:16px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block;transition:all 0.3s;}
            .btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(102,126,234,0.4);}
            .logout{background:transparent;border:1px solid rgba(255,255,255,0.5);color:white;padding:8px 15px;border-radius:20px;text-decoration:none;font-size:14px;}
            .logout:hover{background:rgba(255,255,255,0.1);}
        </style></head><body>';
        
        $html .= '<div class="header">';
        $html .= '<div class="logo">🚀 蚂蚁数字科技</div>';
        
        if ($userinfo) {
            $html .= '<div class="user-info">👤 ' . $userinfo['username'] . ' | 余额: ¥' . number_format($userinfo['points']) . ' | <a href="/simple/logout" class="logout">退出</a></div>';
        } else {
            $html .= '<div class="user-info"><a href="/simple/login" class="logout">登录</a></div>';
        }
        
        $html .= '</div>';
        
        $html .= '<div class="container">';
        
        if ($userinfo) {
            $html .= '<div class="welcome"><h2>🎉 欢迎回来，' . $userinfo['username'] . '！</h2><p>选择您喜欢的游戏开始体验吧</p></div>';
        } else {
            $html .= '<div class="welcome"><h2>🎮 欢迎来到蚂蚁数字科技</h2><p><a href="/simple/login" class="btn">立即登录</a> 开始您的游戏之旅</p></div>';
        }
        
        $html .= '<div class="game-grid">';
        
        // 北京28
        $html .= '<div class="game-card">';
        $html .= '<div class="game-title">🎲 北京28</div>';
        $html .= '<div class="game-desc">经典数字游戏，简单易懂，3分钟开奖</div>';
        $html .= '<a href="/run/fangjian?game=bj28" class="btn">进入游戏</a>';
        $html .= '</div>';
        
        // 加拿大28
        $html .= '<div class="game-card">';
        $html .= '<div class="game-title">🍁 加拿大28</div>';
        $html .= '<div class="game-desc">国际热门，公平公正，3分钟开奖</div>';
        $html .= '<a href="/run/fangjian?game=jnd28" class="btn">进入游戏</a>';
        $html .= '</div>';
        
        // 幸运飞艇
        $html .= '<div class="game-card">';
        $html .= '<div class="game-title">🏎️ 幸运飞艇</div>';
        $html .= '<div class="game-desc">赛车竞技，刺激好玩，2分钟开奖</div>';
        $html .= '<a href="/run/fangjian?game=幸运飞艇" class="btn">进入游戏</a>';
        $html .= '</div>';
        
        // 时时彩
        $html .= '<div class="game-card">';
        $html .= '<div class="game-title">⏰ 时时彩</div>';
        $html .= '<div class="game-desc">传统彩票，玩法多样，10分钟开奖</div>';
        $html .= '<a href="/run/fangjian?game=ssc" class="btn">进入游戏</a>';
        $html .= '</div>';
        
        $html .= '</div></div></body></html>';
        
        return $html;
    }
    
    private function showLoginPage()
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>🔐 用户登录 - 蚂蚁数字科技</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>
            body{font-family:"Microsoft YaHei",Arial;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center;}
            .login-container{background:rgba(255,255,255,0.95);border-radius:20px;padding:40px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;max-width:400px;backdrop-filter:blur(25px);}
            .title{text-align:center;font-size:28px;color:#333;margin-bottom:10px;font-weight:700;}
            .subtitle{text-align:center;color:#666;margin-bottom:30px;font-size:14px;}
            .form-group{margin-bottom:20px;}
            .form-group label{display:block;color:#555;margin-bottom:8px;font-weight:600;}
            .form-group input{width:100%;padding:15px;border:2px solid #e1e5e9;border-radius:10px;font-size:16px;transition:all 0.3s;background:#f8f9fa;}
            .form-group input:focus{outline:none;border-color:#667eea;background:white;box-shadow:0 0 0 3px rgba(102,126,234,0.1);}
            .btn-login{width:100%;padding:15px;background:linear-gradient(135deg,#667eea,#764ba2);border:none;border-radius:10px;color:white;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.3s;margin-top:10px;}
            .btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(102,126,234,0.4);}
            .links{text-align:center;margin-top:25px;padding-top:25px;border-top:1px solid #eee;}
            .links a{color:#667eea;text-decoration:none;margin:0 15px;font-size:14px;}
            .links a:hover{text-decoration:underline;}
            .demo{background:linear-gradient(135deg,#28a745,#20c997);color:white;margin-top:15px;}
            .loading{display:none;text-align:center;margin-top:20px;color:#666;}
        </style></head><body>';
        
        $html .= '<div class="login-container">';
        $html .= '<div class="title">🔐 用户登录</div>';
        $html .= '<div class="subtitle">登录您的蚂蚁数字科技账户</div>';
        
        $html .= '<form id="loginForm">';
        $html .= '<div class="form-group"><label>👤 用户名</label><input type="text" name="username" placeholder="请输入您的用户名" required></div>';
        $html .= '<div class="form-group"><label>🔒 密码</label><input type="password" name="password" placeholder="请输入您的密码" required></div>';
        $html .= '<button type="submit" class="btn-login">立即登录</button>';
        $html .= '<button type="button" class="btn-login demo" onclick="demoLogin()">体验账号登录</button>';
        $html .= '</form>';
        
        $html .= '<div class="loading" id="loading">🔄 登录中...</div>';
        
        $html .= '<div class="links">';
        $html .= '<a href="/simple/register">📝 注册新账号</a>';
        $html .= '<a href="/run/fangjian?game=bj28">🎮 游客试玩</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '<script>
        function demoLogin() {
            document.querySelector("input[name=username]").value = "demo";
            document.querySelector("input[name=password]").value = "123456";
            document.getElementById("loginForm").dispatchEvent(new Event("submit"));
        }
        
        document.getElementById("loginForm").addEventListener("submit", function(e) {
            e.preventDefault();
            var username = document.querySelector("input[name=username]").value;
            var password = document.querySelector("input[name=password]").value;
            
            if (!username || !password) {
                alert("请填写用户名和密码");
                return;
            }
            
            document.getElementById("loading").style.display = "block";
            
            fetch("/simple/login", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("loading").style.display = "none";
                if (data.code == 1) {
                    alert("🎉 登录成功!");
                    location.href = data.url || "/simple/index";
                } else {
                    alert("❌ " + data.msg);
                }
            })
            .catch(error => {
                document.getElementById("loading").style.display = "none";
                alert("❌ 登录失败，请重试");
            });
        });
        </script>';
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    public function logout()
    {
        session('user', null);
        return redirect('/simple/index');
    }
}