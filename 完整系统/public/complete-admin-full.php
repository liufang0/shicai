<?php
session_start();

// 连接SQLite数据库
try {
    $pdo = new PDO('sqlite:../shicai.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('数据库连接失败: ' . $e->getMessage());
}

// 处理登录
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    header('Content-Type: application/json');
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 从数据库验证管理员账号
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && $admin['password'] === md5($password)) {
        $_SESSION['admin'] = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'logged_in' => true
        ];
        echo json_encode(['status' => 'success', 'message' => '登录成功']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '用户名或密码错误']);
    }
    exit;
}

// 处理退出登录
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$isLoggedIn = isset($_SESSION['admin']) && $_SESSION['admin']['logged_in'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>食彩游戏管理系统 v7.7</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Microsoft YaHei", Arial; background: #f8f8f9; }
        
        /* 登录样式 */
        .login-container { max-width: 400px; margin: 100px auto; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .login-title { text-align: center; color: #333; margin-bottom: 30px; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: bold; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .btn-login { width: 100%; padding: 12px; background: #5a67d8; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn-login:hover { background: #4c51bf; }
        .message { margin-top: 15px; padding: 10px; border-radius: 4px; text-align: center; }
        .success { background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; }
        .error { background: #fed7d7; color: #e53e3e; border: 1px solid #feb2b2; }
        
        /* 管理后台样式 */
        .admin-wrapper { height: 100vh; display: flex; }
        .sidebar { width: 250px; background: #2d3748; color: white; overflow-y: auto; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .top-navbar { background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .content-area { flex: 1; padding: 30px; overflow-y: auto; }
        
        /* 侧边栏 */
        .logo { padding: 20px; text-align: center; border-bottom: 1px solid #4a5568; }
        .logo h3 { color: #e2e8f0; margin: 0; }
        .nav-menu { list-style: none; }
        .nav-item { border-bottom: 1px solid #4a5568; }
        .nav-link { display: block; padding: 15px 20px; color: #e2e8f0; text-decoration: none; transition: all 0.3s; }
        .nav-link:hover { background: #4a5568; color: white; }
        .nav-link i { margin-right: 10px; width: 16px; }
        .nav-submenu { background: #1a202c; }
        .nav-submenu .nav-link { padding-left: 40px; font-size: 14px; }
        
        /* 内容区域 */
        .page-title { font-size: 24px; color: #2d3748; margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-number { font-size: 32px; font-weight: bold; color: #5a67d8; margin-bottom: 5px; }
        .stat-label { color: #718096; font-size: 14px; }
        
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h3 { color: #2d3748; margin-bottom: 15px; }
        
        .btn { display: inline-block; padding: 8px 16px; background: #5a67d8; color: white; text-decoration: none; border-radius: 4px; margin: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #4c51bf; }
        .btn-success { background: #38a169; } .btn-success:hover { background: #2f855a; }
        .btn-warning { background: #ed8936; } .btn-warning:hover { background: #dd6b20; }
        .btn-danger { background: #e53e3e; } .btn-danger:hover { background: #c53030; }
        
        .logout-btn { background: #e53e3e; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; }
        .logout-btn:hover { background: #c53030; }
        
        /* 隐藏子菜单 */
        .nav-submenu { display: none; }
        .nav-item.active .nav-submenu { display: block; }
        
        /* 响应式 */
        @media (max-width: 768px) {
            .admin-wrapper { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- 登录页面 -->
        <div class="login-container">
            <div class="login-card">
                <h1 class="login-title">🎮 食彩游戏管理系统</h1>
                <form id="loginForm">
                    <div class="form-group">
                        <label>管理员账号</label>
                        <input type="text" name="username" value="admin" required>
                    </div>
                    <div class="form-group">
                        <label>登录密码</label>
                        <input type="password" name="password" value="admin" required>
                    </div>
                    <button type="submit" class="btn-login">立即登录</button>
                </form>
                <div id="message"></div>
                <p style="margin-top: 20px; text-align: center; color: #666; font-size: 14px;">
                    默认账号: admin / admin
                </p>
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
        <div class="admin-wrapper">
            <!-- 侧边栏 -->
            <div class="sidebar">
                <div class="logo">
                    <h3>食彩管理系统 v7.7</h3>
                    <p style="margin-top: 5px; color: #a0aec0; font-size: 14px;">
                        <?php echo $_SESSION['admin']['username']; ?> - 超级GM管理员
                    </p>
                </div>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showPage('dashboard')">
                            <i>🏠</i> 主页
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showPage('site')">
                            <i>⚙️</i> 网站设置
                        </a>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🔧</i> 系统配置 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('config-basic')">基本配置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('config-wechat')">微信客服二维码</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('config-xyft')">急速飞艇配置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('config-ssc')">时时彩配置</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showPage('members')">
                            <i>👥</i> 会员管理
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showPage('orders')">
                            <i>🎯</i> 竞猜记录
                        </a>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>💰</i> 上下分管理 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('recharge-apply')">上分申请</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('withdraw-apply')">下分申请</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('points-records')">上下分记录</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('payment-wechat')">微信收款设置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('payment-alipay')">支付宝收款设置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('payment-bank')">银行转账收款设置</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🏆</i> 代理管理 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('agent-list')">代理列表</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('agent-settings')">代理设置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('agent-dividend')">代理分红</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🎲</i> 开奖预设 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('lottery-bj28')">螞蟻收益</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('lottery-xyft')">急速飞艇</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>📊</i> 数据采集 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('collect-bj28')">螞蟻收益</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('collect-ssc')">时时彩</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>📈</i> 输赢统计 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('stats-platform')">平台输赢</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('stats-users')">客户输赢</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🤖</i> 机器人管理 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('robot-manage')">机器人管理</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('robot-betting')">机器人竞猜</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>👨‍💼</i> 管理员管理 <span style="float: right;">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('admin-list')">管理员列表</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('admin-logs')">管理员日志</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            
            <!-- 主内容区域 -->
            <div class="main-content">
                <!-- 顶部导航栏 -->
                <div class="top-navbar">
                    <h2 id="page-title">食彩游戏管理系统</h2>
                    <div>
                        <span>欢迎，<?php echo $_SESSION['admin']['username']; ?>！</span>
                        <a href="?logout=1" class="logout-btn">退出登录</a>
                    </div>
                </div>
                
                <!-- 内容区域 -->
                <div class="content-area">
                    <!-- 主页 -->
                    <div id="dashboard" class="page-content">
                        <h1 class="page-title">系统概览</h1>
                        
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number" id="totalUsers">0</div>
                                <div class="stat-label">总用户数</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="todayUsers">0</div>
                                <div class="stat-label">今日新增用户</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="totalPoints">0</div>
                                <div class="stat-label">系统总余分</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">运行中</div>
                                <div class="stat-label">系统状态</div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>🎮 游戏大厅快速入口</h3>
                            <a href="/run/bj28" target="_blank" class="btn">螞蟻收益</a>
                            <a href="/run/ssc" target="_blank" class="btn">时时彩</a>
                            <a href="/run/幸运飞艇" target="_blank" class="btn">急速飞艇</a>
                        </div>
                    </div>
                    
                    <!-- 其他页面内容 -->
                    <div id="members" class="page-content" style="display: none;">
                        <h1 class="page-title">会员管理</h1>
                        <div class="card">
                            <h3>会员列表</h3>
                            <p>会员管理功能正在开发中...</p>
                        </div>
                    </div>
                    
                    <div id="orders" class="page-content" style="display: none;">
                        <h1 class="page-title">竞猜记录</h1>
                        <div class="card">
                            <h3>竞猜记录</h3>
                            <p>竞猜记录功能正在开发中...</p>
                        </div>
                    </div>
                    
                    <!-- 更多页面... -->
                    <div id="other-page" class="page-content" style="display: none;">
                        <h1 class="page-title">功能开发中</h1>
                        <div class="card">
                            <h3>该功能正在开发中</h3>
                            <p>我们正在努力完善系统功能，敬请期待！</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function toggleSubmenu(element) {
                element.classList.toggle('active');
            }
            
            function showPage(pageId) {
                // 隐藏所有页面
                document.querySelectorAll('.page-content').forEach(page => {
                    page.style.display = 'none';
                });
                
                // 显示选中的页面
                const targetPage = document.getElementById(pageId);
                if (targetPage) {
                    targetPage.style.display = 'block';
                } else {
                    document.getElementById('other-page').style.display = 'block';
                }
                
                // 更新页面标题
                const titles = {
                    'dashboard': '系统概览',
                    'members': '会员管理',
                    'orders': '竞猜记录',
                    'site': '网站设置'
                };
                document.getElementById('page-title').textContent = titles[pageId] || '功能开发中';
            }
            
            // 页面加载时显示主页
            showPage('dashboard');
            
            // 加载统计数据
            <?php
            // 获取统计数据
            try {
                $totalUsers = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
                $todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $todayEnd = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
                $todayUsers = $pdo->prepare("SELECT COUNT(*) FROM user WHERE reg_time >= ? AND reg_time <= ?");
                $todayUsers->execute([$todayStart, $todayEnd]);
                $todayUsersCount = $todayUsers->fetchColumn();
                $totalPoints = $pdo->query("SELECT SUM(points) FROM user")->fetchColumn() ?? 0;
                
                echo "document.getElementById('totalUsers').textContent = '$totalUsers';";
                echo "document.getElementById('todayUsers').textContent = '$todayUsersCount';";
                echo "document.getElementById('totalPoints').textContent = '$totalPoints';";
            } catch (Exception $e) {
                echo "console.log('统计数据加载失败: " . $e->getMessage() . "');";
            }
            ?>
        </script>
        
    <?php endif; ?>
</body>
</html>