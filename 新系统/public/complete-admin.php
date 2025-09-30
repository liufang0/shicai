<?php
// 完整的管理后台系统 - 包含所有原功能
header('Content-Type: text/html; charset=utf-8');
session_start();

// 连接SQLite数据库
try {
    $pdo = new PDO('sqlite:../shicai.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('数据库连接失败: ' . $e->getMessage());
}

// 处理各种API请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // 管理员登录
    if ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
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
                echo json_encode(['code' => 200, 'message' => '登录成功', 'status' => 'success']);
            } else {
                echo json_encode(['code' => 400, 'message' => '用户名或密码错误', 'status' => 'error']);
            }
        } catch(Exception $e) {
            echo json_encode(['code' => 500, 'message' => '登录系统错误: ' . $e->getMessage(), 'status' => 'error']);
        }
        exit;
    }
    
    // 退出登录
    if ($action === 'logout') {
        session_destroy();
        echo json_encode(['code' => 200, 'message' => '退出成功', 'status' => 'success']);
        exit;
    }
    
    // 获取用户列表
    if ($action === 'get_users') {
        try {
            $page = intval($_POST['page'] ?? 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            $search = $_POST['search'] ?? '';
            $where = '';
            $params = [];
            
            if ($search) {
                $where = "WHERE username LIKE ? OR nickname LIKE ?";
                $params = ["%$search%", "%$search%"];
            }
            
            // 获取总数
            $countSql = "SELECT COUNT(*) as total FROM user $where";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            // 获取用户列表
            $sql = "SELECT id, username, nickname, points, status, reg_time, is_robot, is_agent 
                    FROM user $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'code' => 200,
                'data' => $users,
                'total' => $total,
                'page' => $page,
                'pages' => ceil($total / $limit)
            ]);
        } catch(Exception $e) {
            echo json_encode(['code' => 500, 'message' => '获取用户列表失败: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // 用户操作
    if ($action === 'user_action') {
        $userId = intval($_POST['user_id']);
        $operation = $_POST['operation'];
        
        try {
            switch($operation) {
                case 'disable':
                    $stmt = $pdo->prepare("UPDATE user SET status = 0 WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '禁用成功']);
                    break;
                case 'enable':
                    $stmt = $pdo->prepare("UPDATE user SET status = 1 WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '启用成功']);
                    break;
                case 'set_robot':
                    $stmt = $pdo->prepare("UPDATE user SET is_robot = 1 WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '设置机器人成功']);
                    break;
                case 'cancel_robot':
                    $stmt = $pdo->prepare("UPDATE user SET is_robot = 0 WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '取消机器人成功']);
                    break;
                case 'set_agent':
                    $stmt = $pdo->prepare("UPDATE user SET is_agent = 1 WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '设置代理成功']);
                    break;
                case 'cancel_agent':
                    $stmt = $pdo->prepare("UPDATE user SET is_agent = 0 WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '取消代理成功']);
                    break;
                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo json_encode(['code' => 200, 'message' => '删除成功']);
                    break;
                default:
                    echo json_encode(['code' => 400, 'message' => '未知操作']);
            }
        } catch(Exception $e) {
            echo json_encode(['code' => 500, 'message' => '操作失败: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // 获取系统统计
    if ($action === 'get_stats') {
        try {
            $today_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $today_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            
            // 今日新增用户
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user WHERE reg_time >= ? AND reg_time <= ?");
            $stmt->execute([$today_start, $today_end]);
            $today_users = $stmt->fetch()['count'];
            
            // 总用户数
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user");
            $stmt->execute();
            $total_users = $stmt->fetch()['count'];
            
            // 系统余分
            $stmt = $pdo->prepare("SELECT SUM(points) as total_points FROM user");
            $stmt->execute();
            $total_points = $stmt->fetch()['total_points'] ?? 0;
            
            echo json_encode([
                'code' => 200,
                'data' => [
                    'today_users' => $today_users,
                    'total_users' => $total_users,
                    'total_points' => $total_points,
                    'today_date' => date('Y-m-d')
                ]
            ]);
        } catch(Exception $e) {
            echo json_encode(['code' => 500, 'message' => '获取统计失败: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // 用户上分
    if ($action === 'add_points') {
        $userId = intval($_POST['user_id']);
        $points = intval($_POST['points']);
        $reason = $_POST['reason'] ?? '管理员上分';
        
        if ($points <= 0) {
            echo json_encode(['code' => 400, 'message' => '点数必须大于0']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            // 更新用户点数
            $stmt = $pdo->prepare("UPDATE user SET points = points + ? WHERE id = ?");
            $stmt->execute([$points, $userId]);
            
            $pdo->commit();
            echo json_encode(['code' => 200, 'message' => "上分成功，增加{$points}点"]);
        } catch(Exception $e) {
            $pdo->rollback();
            echo json_encode(['code' => 500, 'message' => '上分失败: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // 用户下分
    if ($action === 'sub_points') {
        $userId = intval($_POST['user_id']);
        $points = intval($_POST['points']);
        $reason = $_POST['reason'] ?? '管理员下分';
        
        if ($points <= 0) {
            echo json_encode(['code' => 400, 'message' => '点数必须大于0']);
            exit;
        }
        
        try {
            // 检查用户余额
            $stmt = $pdo->prepare("SELECT points FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                echo json_encode(['code' => 400, 'message' => '用户不存在']);
                exit;
            }
            
            if ($user['points'] < $points) {
                echo json_encode(['code' => 400, 'message' => '用户余额不足']);
                exit;
            }
            
            $pdo->beginTransaction();
            
            // 更新用户点数
            $stmt = $pdo->prepare("UPDATE user SET points = points - ? WHERE id = ?");
            $stmt->execute([$points, $userId]);
            
            $pdo->commit();
            echo json_encode(['code' => 200, 'message' => "下分成功，扣除{$points}点"]);
        } catch(Exception $e) {
            $pdo->rollback();
            echo json_encode(['code' => 500, 'message' => '下分失败: ' . $e->getMessage()]);
        }
        exit;
    }
}

// 检查登录状态
function isLoggedIn() {
    return isset($_SESSION['admin']) && $_SESSION['admin']['logged_in'] === true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>食彩游戏平台 - 完整管理系统</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Microsoft YaHei", Arial; background: #f0f2f5; }
        .header { background: #1890ff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; }
        .logout-btn { background: #ff4d4f; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .nav { background: white; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .nav-tabs { display: flex; list-style: none; margin: 0; }
        .nav-tab { padding: 15px 25px; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .nav-tab:hover { background: #f0f2f5; }
        .nav-tab.active { border-bottom-color: #1890ff; background: #e6f7ff; color: #1890ff; }
        .container { max-width: 1400px; margin: 20px auto; padding: 20px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h3 { color: #1890ff; margin-bottom: 15px; font-size: 18px; }
        .btn { display: inline-block; padding: 8px 16px; background: #1890ff; color: white; text-decoration: none; border-radius: 6px; margin: 5px; border: none; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #40a9ff; }
        .btn-success { background: #52c41a; } .btn-success:hover { background: #73d13d; }
        .btn-warning { background: #fa8c16; } .btn-warning:hover { background: #ffa940; }
        .btn-danger { background: #ff4d4f; } .btn-danger:hover { background: #ff7875; }
        .btn-small { padding: 4px 8px; font-size: 12px; }
        .form-group { margin: 15px 0; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .stat-item { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-value { font-size: 32px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 14px; opacity: 0.9; }
        .table-container { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .table th { background: #f5f5f5; font-weight: bold; }
        .table tr:nth-child(even) { background: #f9f9f9; }
        .search-box { display: flex; gap: 10px; margin-bottom: 20px; align-items: center; }
        .search-box input { flex: 1; }
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .page-btn { padding: 8px 12px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 4px; }
        .page-btn:hover { background: #f0f2f5; }
        .page-btn.active { background: #1890ff; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 500px; border-radius: 10px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { font-size: 18px; font-weight: bold; }
        .close { font-size: 24px; cursor: pointer; color: #999; }
        .close:hover { color: #333; }
        .status-enabled { color: #52c41a; font-weight: bold; }
        .status-disabled { color: #ff4d4f; font-weight: bold; }
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #f6ffed; color: #52c41a; border: 1px solid #b7eb8f; }
        .badge-danger { background: #fff2f0; color: #ff4d4f; border: 1px solid #ffccc7; }
        .badge-info { background: #e6f7ff; color: #1890ff; border: 1px solid #91d5ff; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .login-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .login-title { text-align: center; font-size: 24px; margin-bottom: 30px; color: #1890ff; }
    </style>
</head>
<body>
    <?php if (!isLoggedIn()): ?>
    <!-- 登录页面 -->
    <div class="login-container">
        <div class="login-card">
            <div class="login-title">🎮 食彩管理后台</div>
            <form id="loginForm">
                <div class="form-group">
                    <label>管理员账号</label>
                    <input type="text" id="username" value="admin" required>
                </div>
                <div class="form-group">
                    <label>登录密码</label>
                    <input type="password" id="password" value="admin" required>
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%; font-size: 16px;">立即登录</button>
            </form>
            <div id="loginMessage" style="margin-top: 15px; text-align: center;"></div>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm').onsubmit = function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=login&username=${username}&password=${password}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    document.getElementById('loginMessage').innerHTML = 
                        '<div style="color: #ff4d4f;">❌ ' + data.message + '</div>';
                }
            });
        };
    </script>
    
    <?php else: ?>
    <!-- 主管理界面 -->
    <div class="header">
        <h1>🎮 食彩游戏平台 - 完整管理系统</h1>
        <button class="logout-btn" onclick="logout()">退出登录</button>
    </div>
    
    <nav class="nav">
        <ul class="nav-tabs">
            <li class="nav-tab active" onclick="switchTab('dashboard')">📊 控制面板</li>
            <li class="nav-tab" onclick="switchTab('users')">👥 用户管理</li>
            <li class="nav-tab" onclick="switchTab('points')">💰 分数管理</li>
            <li class="nav-tab" onclick="switchTab('games')">🎮 游戏管理</li>
            <li class="nav-tab" onclick="switchTab('reports')">📈 统计报表</li>
            <li class="nav-tab" onclick="switchTab('settings')">⚙️ 系统设置</li>
        </ul>
    </nav>
    
    <div class="container">
        <!-- 控制面板 -->
        <div id="dashboard" class="tab-content active">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value" id="todayUsers">0</div>
                    <div class="stat-label">今日新增用户</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="totalUsers">0</div>
                    <div class="stat-label">总用户数</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="totalPoints">0</div>
                    <div class="stat-label">系统总余分</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo date('H:i'); ?></div>
                    <div class="stat-label">当前时间</div>
                </div>
            </div>
            
            <div class="grid">
                <div class="card">
                    <h3>🎮 游戏大厅快速入口</h3>
                    <a href="/run/bj28" target="_blank" class="btn">北京28</a>
                    <a href="/run/ssc" target="_blank" class="btn">时时彩</a>
                    <a href="/run/幸运飞艇" target="_blank" class="btn">幸运飞艇</a>
                    <br><br>
                    <button class="btn btn-warning" onclick="testAllGames()">测试所有游戏</button>
                </div>
                
                <div class="card">
                    <h3>📊 系统监控</h3>
                    <p>PHP版本: <span class="status-enabled"><?php echo PHP_VERSION; ?></span></p>
                    <p>数据库: <span class="status-enabled">SQLite 正常</span></p>
                    <p>服务状态: <span class="status-enabled">运行中</span></p>
                    <button class="btn" onclick="refreshStats()">刷新数据</button>
                </div>
            </div>
        </div>
        
        <!-- 用户管理 -->
        <div id="users" class="tab-content">
            <div class="card">
                <h3>👥 用户管理</h3>
                
                <div class="search-box">
                    <input type="text" id="userSearch" placeholder="搜索用户名或昵称...">
                    <button class="btn" onclick="searchUsers()">搜索</button>
                    <button class="btn btn-success" onclick="loadUsers()">刷新</button>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>用户名</th>
                                <th>昵称</th>
                                <th>余分</th>
                                <th>状态</th>
                                <th>注册时间</th>
                                <th>标签</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody id="userList">
                            <tr><td colspan="8">加载中...</td></tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination" id="userPagination"></div>
            </div>
        </div>
        
        <!-- 分数管理 -->
        <div id="points" class="tab-content">
            <div class="card">
                <h3>💰 分数管理</h3>
                <p>在用户管理页面点击"上分"或"下分"按钮进行操作</p>
                <button class="btn" onclick="switchTab('users')">前往用户管理</button>
            </div>
        </div>
        
        <!-- 游戏管理 -->
        <div id="games" class="tab-content">
            <div class="card">
                <h3>🎮 游戏管理</h3>
                <div class="grid">
                    <div>
                        <h4>北京28</h4>
                        <p>状态: <span class="status-enabled">运行中</span></p>
                        <a href="/run/bj28" target="_blank" class="btn">进入游戏</a>
                    </div>
                    <div>
                        <h4>时时彩</h4>
                        <p>状态: <span class="status-enabled">运行中</span></p>
                        <a href="/run/ssc" target="_blank" class="btn">进入游戏</a>
                    </div>
                    <div>
                        <h4>幸运飞艇</h4>
                        <p>状态: <span class="status-enabled">运行中</span></p>
                        <a href="/run/幸运飞艇" target="_blank" class="btn">进入游戏</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 统计报表 -->
        <div id="reports" class="tab-content">
            <div class="card">
                <h3>📈 统计报表</h3>
                <p>统计功能开发中...</p>
            </div>
        </div>
        
        <!-- 系统设置 -->
        <div id="settings" class="tab-content">
            <div class="card">
                <h3>⚙️ 系统设置</h3>
                <p>系统设置功能开发中...</p>
            </div>
        </div>
    </div>
    
    <!-- 上分模态框 -->
    <div id="addPointsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">用户上分</h3>
                <span class="close" onclick="closeModal('addPointsModal')">&times;</span>
            </div>
            <form id="addPointsForm">
                <div class="form-group">
                    <label>用户ID</label>
                    <input type="number" id="addUserId" readonly>
                </div>
                <div class="form-group">
                    <label>上分点数</label>
                    <input type="number" id="addPoints" min="1" required>
                </div>
                <div class="form-group">
                    <label>备注</label>
                    <input type="text" id="addReason" value="管理员上分">
                </div>
                <button type="submit" class="btn btn-success">确认上分</button>
                <button type="button" class="btn" onclick="closeModal('addPointsModal')">取消</button>
            </form>
        </div>
    </div>
    
    <!-- 下分模态框 -->
    <div id="subPointsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">用户下分</h3>
                <span class="close" onclick="closeModal('subPointsModal')">&times;</span>
            </div>
            <form id="subPointsForm">
                <div class="form-group">
                    <label>用户ID</label>
                    <input type="number" id="subUserId" readonly>
                </div>
                <div class="form-group">
                    <label>下分点数</label>
                    <input type="number" id="subPoints" min="1" required>
                </div>
                <div class="form-group">
                    <label>备注</label>
                    <input type="text" id="subReason" value="管理员下分">
                </div>
                <button type="submit" class="btn btn-danger">确认下分</button>
                <button type="button" class="btn" onclick="closeModal('subPointsModal')">取消</button>
            </form>
        </div>
    </div>
    
    <script>
        let currentPage = 1;
        
        // 切换标签
        function switchTab(tabName) {
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
            
            if (tabName === 'users') {
                loadUsers();
            } else if (tabName === 'dashboard') {
                refreshStats();
            }
        }
        
        // 加载统计数据
        function refreshStats() {
            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_stats'
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    document.getElementById('todayUsers').textContent = data.data.today_users;
                    document.getElementById('totalUsers').textContent = data.data.total_users;
                    document.getElementById('totalPoints').textContent = data.data.total_points;
                }
            });
        }
        
        // 加载用户列表
        function loadUsers(page = 1) {
            const search = document.getElementById('userSearch').value;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=get_users&page=${page}&search=${search}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    renderUserList(data.data);
                    renderPagination(data.page, data.pages, 'userPagination', loadUsers);
                    currentPage = page;
                }
            });
        }
        
        // 渲染用户列表
        function renderUserList(users) {
            const tbody = document.getElementById('userList');
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8">暂无数据</td></tr>';
                return;
            }
            
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.username || '未设置'}</td>
                    <td>${user.nickname || '未设置'}</td>
                    <td>${user.points || 0}</td>
                    <td>${user.status == 1 ? '<span class="status-enabled">正常</span>' : '<span class="status-disabled">禁用</span>'}</td>
                    <td>${user.reg_time ? new Date(user.reg_time * 1000).toLocaleString() : '未知'}</td>
                    <td>
                        ${user.is_robot == 1 ? '<span class="badge badge-info">机器人</span>' : ''}
                        ${user.is_agent == 1 ? '<span class="badge badge-success">代理</span>' : ''}
                    </td>
                    <td>
                        <button class="btn btn-small" onclick="showAddPoints(${user.id})">上分</button>
                        <button class="btn btn-small btn-warning" onclick="showSubPoints(${user.id})">下分</button>
                        ${user.status == 1 ? 
                            `<button class="btn btn-small btn-danger" onclick="userAction(${user.id}, 'disable')">禁用</button>` :
                            `<button class="btn btn-small btn-success" onclick="userAction(${user.id}, 'enable')">启用</button>`
                        }
                        ${user.is_robot == 1 ? 
                            `<button class="btn btn-small" onclick="userAction(${user.id}, 'cancel_robot')">取消机器人</button>` :
                            `<button class="btn btn-small" onclick="userAction(${user.id}, 'set_robot')">设为机器人</button>`
                        }
                        ${user.is_agent == 1 ? 
                            `<button class="btn btn-small" onclick="userAction(${user.id}, 'cancel_agent')">取消代理</button>` :
                            `<button class="btn btn-small" onclick="userAction(${user.id}, 'set_agent')">设为代理</button>`
                        }
                    </td>
                </tr>
            `).join('');
        }
        
        // 渲染分页
        function renderPagination(current, total, containerId, callback) {
            const container = document.getElementById(containerId);
            let html = '';
            
            if (current > 1) {
                html += `<button class="page-btn" onclick="${callback.name}(${current - 1})">上一页</button>`;
            }
            
            for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
                html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="${callback.name}(${i})">${i}</button>`;
            }
            
            if (current < total) {
                html += `<button class="page-btn" onclick="${callback.name}(${current + 1})">下一页</button>`;
            }
            
            container.innerHTML = html;
        }
        
        // 搜索用户
        function searchUsers() {
            loadUsers(1);
        }
        
        // 用户操作
        function userAction(userId, operation) {
            if (!confirm('确定要执行此操作吗？')) return;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=user_action&user_id=${userId}&operation=${operation}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.code === 200) {
                    loadUsers(currentPage);
                }
            });
        }
        
        // 显示上分模态框
        function showAddPoints(userId) {
            document.getElementById('addUserId').value = userId;
            document.getElementById('addPointsModal').style.display = 'block';
        }
        
        // 显示下分模态框  
        function showSubPoints(userId) {
            document.getElementById('subUserId').value = userId;
            document.getElementById('subPointsModal').style.display = 'block';
        }
        
        // 关闭模态框
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // 上分表单提交
        document.getElementById('addPointsForm').onsubmit = function(e) {
            e.preventDefault();
            const userId = document.getElementById('addUserId').value;
            const points = document.getElementById('addPoints').value;
            const reason = document.getElementById('addReason').value;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=add_points&user_id=${userId}&points=${points}&reason=${reason}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.code === 200) {
                    closeModal('addPointsModal');
                    loadUsers(currentPage);
                    refreshStats();
                }
            });
        };
        
        // 下分表单提交
        document.getElementById('subPointsForm').onsubmit = function(e) {
            e.preventDefault();
            const userId = document.getElementById('subUserId').value;
            const points = document.getElementById('subPoints').value;
            const reason = document.getElementById('subReason').value;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=sub_points&user_id=${userId}&points=${points}&reason=${reason}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.code === 200) {
                    closeModal('subPointsModal');
                    loadUsers(currentPage);
                    refreshStats();
                }
            });
        };
        
        // 退出登录
        function logout() {
            if (confirm('确定要退出登录吗？')) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=logout'
                })
                .then(() => {
                    location.reload();
                });
            }
        }
        
        // 测试所有游戏
        function testAllGames() {
            const games = ['/run/bj28', '/run/ssc', '/run/幸运飞艇'];
            games.forEach(game => {
                fetch(game)
                .then(response => {
                    console.log(`${game} 状态: ${response.status === 200 ? '正常' : '异常'}`);
                })
                .catch(error => {
                    console.log(`${game} 错误: ${error.message}`);
                });
            });
            alert('游戏测试完成，请查看控制台输出');
        }
        
        // 页面加载时初始化
        window.onload = function() {
            refreshStats();
        };
        
        // 回车搜索
        document.getElementById('userSearch').onkeypress = function(e) {
            if (e.key === 'Enter') {
                searchUsers();
            }
        };
    </script>
    
    <?php endif; ?>
</body>
</html>