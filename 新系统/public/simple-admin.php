<?php
session_start();

// 简单直接的管理系统
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    header('Content-Type: application/json');
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        echo json_encode(['status' => 'success', 'message' => '登录成功']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => '用户名或密码错误']);
        exit;
    }
}

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>食彩管理后台</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .title { text-align: center; color: #333; margin-bottom: 30px; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: bold; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .btn { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .message { margin-top: 15px; padding: 10px; border-radius: 4px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .admin-panel { margin-top: 20px; }
        .header { background: #007cba; color: white; padding: 15px 0; margin-bottom: 30px; }
        .nav { background: white; padding: 0; margin-bottom: 20px; border-bottom: 1px solid #ddd; }
        .nav-item { display: inline-block; padding: 15px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .nav-item:hover { background: #f8f9fa; }
        .nav-item.active { border-bottom-color: #007cba; background: #e3f2fd; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; color: #007cba; }
        .stat-label { color: #666; margin-top: 5px; }
        .logout-btn { float: right; background: #dc3545; padding: 8px 16px; border-radius: 4px; }
        .logout-btn:hover { background: #c82333; }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- 登录页面 -->
        <div class="login-container">
            <div class="card">
                <h1 class="title">🎮 食彩管理后台</h1>
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
                <div id="message"></div>
            </div>
        </div>
        
        <script>
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.getElementById('message');
                    if (data.status === 'success') {
                        messageDiv.innerHTML = '<div class="message success">✅ ' + data.message + '，正在跳转...</div>';
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        messageDiv.innerHTML = '<div class="message error">❌ ' + data.message + '</div>';
                    }
                });
            });
        </script>
        
    <?php else: ?>
        <!-- 管理后台 -->
        <div class="header">
            <div class="container">
                <h1 style="display: inline-block;">🎮 食彩管理后台</h1>
                <a href="?logout=1" class="logout-btn" style="text-decoration: none; color: white;">退出登录</a>
                <div style="clear: both;"></div>
            </div>
        </div>
        
        <div class="container">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number">156</div>
                    <div class="stat-label">总用户数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">12</div>
                    <div class="stat-label">今日新增</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">85,420</div>
                    <div class="stat-label">系统余分</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">运行中</div>
                    <div class="stat-label">系统状态</div>
                </div>
            </div>
            
            <div class="nav">
                <div class="nav-item active" onclick="showTab('dashboard')">📊 控制面板</div>
                <div class="nav-item" onclick="showTab('users')">👥 用户管理</div>
                <div class="nav-item" onclick="showTab('points')">💰 分数管理</div>
                <div class="nav-item" onclick="showTab('games')">🎮 游戏管理</div>
                <div class="nav-item" onclick="showTab('reports')">📈 统计报表</div>
            </div>
            
            <div id="dashboard" class="tab-content active">
                <div class="card">
                    <h3>📊 系统概览</h3>
                    <p>欢迎使用食彩游戏管理后台！</p>
                    <br>
                    <h4>🎮 快速入口</h4>
                    <a href="/run/bj28" target="_blank" class="btn" style="width: auto; margin: 5px; text-decoration: none;">北京28</a>
                    <a href="/run/ssc" target="_blank" class="btn" style="width: auto; margin: 5px; text-decoration: none;">时时彩</a>
                    <a href="/run/幸运飞艇" target="_blank" class="btn" style="width: auto; margin: 5px; text-decoration: none;">幸运飞艇</a>
                </div>
            </div>
            
            <div id="users" class="tab-content">
                <div class="card">
                    <h3>👥 用户管理</h3>
                    <p>用户列表和管理功能</p>
                    <div style="margin-top: 20px;">
                        <button class="btn" style="width: auto;">查看用户列表</button>
                        <button class="btn" style="width: auto; margin-left: 10px;">添加用户</button>
                    </div>
                </div>
            </div>
            
            <div id="points" class="tab-content">
                <div class="card">
                    <h3>💰 分数管理</h3>
                    <p>用户上分下分管理</p>
                    <div style="margin-top: 20px;">
                        <button class="btn" style="width: auto;">上分申请</button>
                        <button class="btn" style="width: auto; margin-left: 10px;">下分申请</button>
                    </div>
                </div>
            </div>
            
            <div id="games" class="tab-content">
                <div class="card">
                    <h3>🎮 游戏管理</h3>
                    <p>游戏设置和开奖控制</p>
                    <div style="margin-top: 20px;">
                        <button class="btn" style="width: auto;">开奖设置</button>
                        <button class="btn" style="width: auto; margin-left: 10px;">游戏配置</button>
                    </div>
                </div>
            </div>
            
            <div id="reports" class="tab-content">
                <div class="card">
                    <h3>📈 统计报表</h3>
                    <p>数据统计和分析报表</p>
                    <div style="margin-top: 20px;">
                        <button class="btn" style="width: auto;">收益统计</button>
                        <button class="btn" style="width: auto; margin-left: 10px;">用户统计</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function showTab(tabName) {
                // 隐藏所有标签内容
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.nav-item').forEach(nav => {
                    nav.classList.remove('active');
                });
                
                // 显示选中的标签
                document.getElementById(tabName).classList.add('active');
                event.target.classList.add('active');
            }
        </script>
        
    <?php endif; ?>
    
    <?php
    // 处理退出登录
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
</body>
</html>