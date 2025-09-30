<?php
/**
 * 食彩游戏管理系统 - 完整版
 * 适用于服务器部署
 * 包含所有原系统功能
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 数据库配置
$config = [
    'db_path' => dirname(__FILE__) . '/shicai.db',  // SQLite数据库路径
    'upload_path' => dirname(__FILE__) . '/uploads/', // 上传文件路径
    'site_name' => '食彩游戏平台',
    'version' => 'v7.7'
];

// 创建上传目录
if (!file_exists($config['upload_path'])) {
    mkdir($config['upload_path'], 0755, true);
}

// 数据库连接
try {
    $pdo = new PDO('sqlite:' . $config['db_path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die('数据库连接失败: ' . $e->getMessage());
}

// 初始化数据库表
function initDatabase($pdo) {
    $tables = [
        // 管理员表
        "CREATE TABLE IF NOT EXISTS admin (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(32) NOT NULL,
            nickname VARCHAR(50),
            status INTEGER DEFAULT 1,
            created_at INTEGER DEFAULT 0,
            updated_at INTEGER DEFAULT 0
        )",
        
        // 用户表
        "CREATE TABLE IF NOT EXISTS user (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE,
            nickname VARCHAR(50),
            password VARCHAR(32),
            phone VARCHAR(20),
            email VARCHAR(100),
            points DECIMAL(15,2) DEFAULT 0.00,
            total_bet DECIMAL(15,2) DEFAULT 0.00,
            total_win DECIMAL(15,2) DEFAULT 0.00,
            status INTEGER DEFAULT 1,
            is_robot INTEGER DEFAULT 0,
            is_agent INTEGER DEFAULT 0,
            parent_id INTEGER DEFAULT 0,
            reg_time INTEGER DEFAULT 0,
            last_login_time INTEGER DEFAULT 0
        )",
        
        // 订单表
        "CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            game_type VARCHAR(20),
            bet_amount DECIMAL(10,2),
            win_amount DECIMAL(10,2),
            status INTEGER DEFAULT 0,
            created_at INTEGER DEFAULT 0
        )",
        
        // 上下分记录表
        "CREATE TABLE IF NOT EXISTS point_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            type INTEGER, -- 1上分 0下分
            amount DECIMAL(10,2),
            status INTEGER DEFAULT 0, -- 0待审核 1已完成 2已拒绝
            remark TEXT,
            admin_id INTEGER,
            created_at INTEGER DEFAULT 0,
            updated_at INTEGER DEFAULT 0
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    
    // 检查是否有默认管理员
    $admin = $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
    if ($admin == 0) {
        $pdo->exec("INSERT INTO admin (username, password, nickname, created_at, updated_at) 
                   VALUES ('admin', '" . md5('admin') . "', '超级管理员', " . time() . ", " . time() . ")");
    }
}

initDatabase($pdo);

// API处理函数
function handleAPI($pdo) {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? AND status = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && $admin['password'] === md5($password)) {
                $_SESSION['admin'] = $admin;
                echo json_encode(['status' => 'success', 'message' => '登录成功']);
            } else {
                echo json_encode(['status' => 'error', 'message' => '用户名或密码错误']);
            }
            exit;
            
        case 'logout':
            session_destroy();
            echo json_encode(['status' => 'success', 'message' => '退出成功']);
            exit;
            
        case 'get_stats':
            $stats = [];
            
            // 总用户数
            $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
            
            // 今日新增用户
            $today_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $today_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE reg_time >= ? AND reg_time <= ?");
            $stmt->execute([$today_start, $today_end]);
            $stats['today_users'] = $stmt->fetchColumn();
            
            // 系统总余分
            $stats['total_points'] = $pdo->query("SELECT SUM(points) FROM user")->fetchColumn() ?? 0;
            
            // 待处理上分
            $stats['pending_recharge'] = $pdo->query("SELECT COUNT(*) FROM point_records WHERE type = 1 AND status = 0")->fetchColumn();
            
            // 待处理下分
            $stats['pending_withdraw'] = $pdo->query("SELECT COUNT(*) FROM point_records WHERE type = 0 AND status = 0")->fetchColumn();
            
            echo json_encode(['status' => 'success', 'data' => $stats]);
            exit;
            
        case 'get_users':
            $page = intval($_GET['page'] ?? 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? '';
            
            $where = '';
            $params = [];
            if ($search) {
                $where = "WHERE username LIKE ? OR nickname LIKE ?";
                $params = ["%$search%", "%$search%"];
            }
            
            // 总数
            $countSql = "SELECT COUNT(*) FROM user $where";
            $stmt = $pdo->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetchColumn();
            
            // 用户列表
            $sql = "SELECT id, username, nickname, points, status, is_robot, is_agent, reg_time 
                    FROM user $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'users' => $users,
                    'total' => $total,
                    'page' => $page,
                    'pages' => ceil($total / $limit)
                ]
            ]);
            exit;
            
        case 'user_action':
            $userId = intval($_POST['user_id']);
            $operation = $_POST['operation'];
            
            $sql = '';
            switch ($operation) {
                case 'disable':
                    $sql = "UPDATE user SET status = 0 WHERE id = ?";
                    break;
                case 'enable':
                    $sql = "UPDATE user SET status = 1 WHERE id = ?";
                    break;
                case 'set_robot':
                    $sql = "UPDATE user SET is_robot = 1 WHERE id = ?";
                    break;
                case 'cancel_robot':
                    $sql = "UPDATE user SET is_robot = 0 WHERE id = ?";
                    break;
                case 'set_agent':
                    $sql = "UPDATE user SET is_agent = 1 WHERE id = ?";
                    break;
                case 'cancel_agent':
                    $sql = "UPDATE user SET is_agent = 0 WHERE id = ?";
                    break;
                case 'delete':
                    $sql = "DELETE FROM user WHERE id = ?";
                    break;
            }
            
            if ($sql) {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userId]);
                echo json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                echo json_encode(['status' => 'error', 'message' => '未知操作']);
            }
            exit;
            
        case 'add_points':
            $userId = intval($_POST['user_id']);
            $points = floatval($_POST['points']);
            $remark = $_POST['remark'] ?? '管理员上分';
            
            if ($points <= 0) {
                echo json_encode(['status' => 'error', 'message' => '点数必须大于0']);
                exit;
            }
            
            $pdo->beginTransaction();
            try {
                // 更新用户点数
                $stmt = $pdo->prepare("UPDATE user SET points = points + ? WHERE id = ?");
                $stmt->execute([$points, $userId]);
                
                // 添加记录
                $stmt = $pdo->prepare("INSERT INTO point_records (user_id, type, amount, status, remark, admin_id, created_at, updated_at) VALUES (?, 1, ?, 1, ?, ?, ?, ?)");
                $stmt->execute([$userId, $points, $remark, $_SESSION['admin']['id'], time(), time()]);
                
                $pdo->commit();
                echo json_encode(['status' => 'success', 'message' => "上分成功，增加{$points}点"]);
            } catch (Exception $e) {
                $pdo->rollback();
                echo json_encode(['status' => 'error', 'message' => '上分失败: ' . $e->getMessage()]);
            }
            exit;
            
        case 'sub_points':
            $userId = intval($_POST['user_id']);
            $points = floatval($_POST['points']);
            $remark = $_POST['remark'] ?? '管理员下分';
            
            if ($points <= 0) {
                echo json_encode(['status' => 'error', 'message' => '点数必须大于0']);
                exit;
            }
            
            // 检查余额
            $stmt = $pdo->prepare("SELECT points FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || $user['points'] < $points) {
                echo json_encode(['status' => 'error', 'message' => '用户余额不足']);
                exit;
            }
            
            $pdo->beginTransaction();
            try {
                // 更新用户点数
                $stmt = $pdo->prepare("UPDATE user SET points = points - ? WHERE id = ?");
                $stmt->execute([$points, $userId]);
                
                // 添加记录
                $stmt = $pdo->prepare("INSERT INTO point_records (user_id, type, amount, status, remark, admin_id, created_at, updated_at) VALUES (?, 0, ?, 1, ?, ?, ?, ?)");
                $stmt->execute([$userId, $points, $remark, $_SESSION['admin']['id'], time(), time()]);
                
                $pdo->commit();
                echo json_encode(['status' => 'success', 'message' => "下分成功，扣除{$points}点"]);
            } catch (Exception $e) {
                $pdo->rollback();
                echo json_encode(['status' => 'error', 'message' => '下分失败: ' . $e->getMessage()]);
            }
            exit;
    }
}

// 处理AJAX请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    handleAPI($pdo);
}

// 检查登录状态
$isLoggedIn = isset($_SESSION['admin']);

// 处理退出登录
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['site_name']; ?> - 管理系统 <?php echo $config['version']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Microsoft YaHei", "PingFang SC", "Hiragino Sans GB", sans-serif; background: #f0f2f5; line-height: 1.6; }
        
        /* 登录页面 */
        .login-container { 
            min-height: 100vh; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px;
        }
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 400px;
        }
        .login-title { 
            text-align: center; 
            color: #333; 
            margin-bottom: 30px; 
            font-size: 28px; 
            font-weight: 700;
        }
        .form-group { margin-bottom: 25px; }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            color: #555; 
            font-weight: 600; 
            font-size: 14px;
        }
        .form-group input { 
            width: 100%; 
            padding: 15px; 
            border: 2px solid #e1e5e9; 
            border-radius: 8px; 
            font-size: 16px; 
            transition: border-color 0.3s;
        }
        .form-group input:focus { 
            outline: none; 
            border-color: #667eea; 
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-login { 
            width: 100%; 
            padding: 15px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: transform 0.2s;
        }
        .btn-login:hover { transform: translateY(-2px); }
        .message { 
            margin-top: 15px; 
            padding: 12px; 
            border-radius: 6px; 
            text-align: center; 
            font-weight: 600;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* 管理后台 */
        .admin-wrapper { height: 100vh; display: flex; background: #f0f2f5; }
        .sidebar { 
            width: 260px; 
            background: #1e293b; 
            color: white; 
            overflow-y: auto; 
            box-shadow: 2px 0 8px rgba(0,0,0,0.15);
        }
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .top-navbar { 
            background: white; 
            padding: 20px 30px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .content-area { flex: 1; padding: 30px; overflow-y: auto; }
        
        /* 侧边栏样式 */
        .logo { 
            padding: 25px 20px; 
            text-align: center; 
            border-bottom: 1px solid #334155; 
            background: #0f172a;
        }
        .logo h3 { color: #f1f5f9; margin: 0; font-size: 18px; font-weight: 700; }
        .logo p { margin-top: 8px; color: #94a3b8; font-size: 13px; }
        
        .nav-menu { list-style: none; padding: 10px 0; }
        .nav-item { margin-bottom: 2px; }
        .nav-link { 
            display: flex; 
            align-items: center; 
            padding: 15px 20px; 
            color: #cbd5e1; 
            text-decoration: none; 
            transition: all 0.3s; 
            border-left: 3px solid transparent;
        }
        .nav-link:hover { 
            background: #334155; 
            color: white; 
            border-left-color: #3b82f6;
        }
        .nav-link.active { 
            background: #1e40af; 
            color: white; 
            border-left-color: #60a5fa;
        }
        .nav-link i { margin-right: 12px; width: 20px; text-align: center; }
        .nav-submenu { 
            background: #0f172a; 
            display: none; 
            border-left: 3px solid #334155;
        }
        .nav-submenu .nav-link { 
            padding-left: 45px; 
            font-size: 14px; 
            border-left: none;
        }
        .nav-item.active .nav-submenu { display: block; }
        .submenu-toggle { 
            margin-left: auto; 
            font-size: 12px; 
            transition: transform 0.3s;
        }
        .nav-item.active .submenu-toggle { transform: rotate(180deg); }
        
        /* 内容区域 */
        .page-title { 
            font-size: 32px; 
            color: #1e293b; 
            margin-bottom: 8px; 
            font-weight: 700;
        }
        .page-subtitle { 
            color: #64748b; 
            margin-bottom: 30px; 
            font-size: 16px;
        }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 25px; 
            margin-bottom: 40px; 
        }
        .stat-card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); 
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .stat-header { display: flex; align-items: center; margin-bottom: 15px; }
        .stat-icon { 
            width: 50px; 
            height: 50px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 24px; 
            margin-right: 15px;
        }
        .stat-number { 
            font-size: 36px; 
            font-weight: 700; 
            color: #1e293b; 
            margin-bottom: 5px;
        }
        .stat-label { color: #64748b; font-size: 14px; font-weight: 500; }
        .stat-change { 
            margin-top: 10px; 
            font-size: 13px; 
            font-weight: 600;
        }
        .stat-change.up { color: #059669; }
        .stat-change.down { color: #dc2626; }
        
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); 
            margin-bottom: 25px; 
            border: 1px solid #e2e8f0;
        }
        .card h3 { 
            color: #1e293b; 
            margin-bottom: 20px; 
            font-size: 20px; 
            font-weight: 600;
        }
        
        .btn { 
            display: inline-flex; 
            align-items: center; 
            padding: 10px 16px; 
            background: #3b82f6; 
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            margin: 5px; 
            border: none; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: 500; 
            transition: all 0.2s;
        }
        .btn:hover { background: #2563eb; transform: translateY(-1px); }
        .btn-success { background: #059669; } .btn-success:hover { background: #047857; }
        .btn-warning { background: #d97706; } .btn-warning:hover { background: #b45309; }
        .btn-danger { background: #dc2626; } .btn-danger:hover { background: #b91c1c; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .logout-btn { 
            background: #dc2626; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 500;
            transition: all 0.2s;
        }
        .logout-btn:hover { background: #b91c1c; }
        
        .page-content { display: none; }
        .page-content.active { display: block; }
        
        .table-container { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0;
        }
        .table th { 
            background: #f8fafc; 
            font-weight: 600; 
            color: #374151; 
            font-size: 14px;
        }
        .table tr:hover { background: #f8fafc; }
        
        .modal { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content { 
            background: white; 
            padding: 30px; 
            width: 90%; 
            max-width: 500px; 
            border-radius: 12px; 
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
        }
        .modal-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px; 
        }
        .modal-title { font-size: 20px; font-weight: 600; color: #1e293b; }
        .close { 
            font-size: 24px; 
            cursor: pointer; 
            color: #9ca3af; 
            background: none; 
            border: none;
        }
        .close:hover { color: #374151; }
        
        .search-bar { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 25px; 
            align-items: center;
        }
        .search-input { 
            flex: 1; 
            padding: 12px; 
            border: 2px solid #e2e8f0; 
            border-radius: 6px; 
            font-size: 14px;
        }
        .search-input:focus { 
            outline: none; 
            border-color: #3b82f6; 
        }
        
        .badge { 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 12px; 
            font-weight: 500;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        
        /* 响应式 */
        @media (max-width: 768px) {
            .admin-wrapper { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .stats-grid { grid-template-columns: 1fr; }
            .top-navbar { flex-direction: column; gap: 15px; }
        }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- 登录页面 -->
        <div class="login-container">
            <div class="login-card">
                <h1 class="login-title">🎮 食彩管理系统</h1>
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
        
    <?php else: ?>
        <!-- 管理后台 -->
        <div class="admin-wrapper">
            <!-- 侧边栏 -->
            <div class="sidebar">
                <div class="logo">
                    <h3><?php echo $config['site_name']; ?></h3>
                    <p><?php echo isset($_SESSION['admin']['nickname']) ? $_SESSION['admin']['nickname'] : '管理员'; ?> - 管理系统 <?php echo $config['version']; ?></p>
                </div>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" onclick="showPage('dashboard')">
                            <i>📊</i> <span>系统概览</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showPage('members')">
                            <i>👥</i> <span>会员管理</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showPage('orders')">
                            <i>🎯</i> <span>竞猜记录</span>
                        </a>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>💰</i> <span>上下分管理</span>
                            <span class="submenu-toggle">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('recharge')">上分申请</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('withdraw')">下分申请</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('point-records')">上下分记录</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('payment')">收款设置</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🏆</i> <span>代理管理</span>
                            <span class="submenu-toggle">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('agents')">代理列表</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('agent-settings')">代理设置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('dividends')">代理分红</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🎲</i> <span>游戏管理</span>
                            <span class="submenu-toggle">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('lottery-settings')">开奖预设</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('game-config')">游戏配置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('data-collect')">数据采集</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>📈</i> <span>统计分析</span>
                            <span class="submenu-toggle">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('platform-stats')">平台统计</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('user-stats')">用户统计</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('win-lose')">输赢统计</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>🤖</i> <span>机器人管理</span>
                            <span class="submenu-toggle">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('robots')">机器人设置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('robot-betting')">机器人竞猜</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item" onclick="toggleSubmenu(this)">
                        <a href="#" class="nav-link">
                            <i>⚙️</i> <span>系统设置</span>
                            <span class="submenu-toggle">▼</span>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="#" class="nav-link" onclick="showPage('site-config')">网站配置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('admin-users')">管理员管理</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('admin-logs')">操作日志</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            
            <!-- 主内容区域 -->
            <div class="main-content">
                <!-- 顶部导航栏 -->
                <div class="top-navbar">
                    <div>
                        <h2 id="page-title">系统概览</h2>
                        <p id="page-subtitle" style="margin: 0; color: #64748b;">欢迎使用食彩游戏管理系统</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <span style="color: #64748b;">欢迎，<?php echo isset($_SESSION['admin']['nickname']) ? $_SESSION['admin']['nickname'] : '管理员'; ?>！</span>
                        <a href="?logout=1" class="logout-btn">退出登录</a>
                    </div>
                </div>
                
                <!-- 内容区域 -->
                <div class="content-area">
                    <!-- 系统概览页面 -->
                    <div id="dashboard" class="page-content active">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">👥</div>
                                    <div>
                                        <div class="stat-number" id="totalUsers">-</div>
                                        <div class="stat-label">总用户数</div>
                                    </div>
                                </div>
                                <div class="stat-change up" id="todayUsersChange">今日新增: <span id="todayUsers">-</span></div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #d1fae5; color: #065f46;">💰</div>
                                    <div>
                                        <div class="stat-number" id="totalPoints">-</div>
                                        <div class="stat-label">系统总余分</div>
                                    </div>
                                </div>
                                <div class="stat-change">实时数据</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #fef3c7; color: #92400e;">⏳</div>
                                    <div>
                                        <div class="stat-number" id="pendingRecharge">-</div>
                                        <div class="stat-label">待处理上分</div>
                                    </div>
                                </div>
                                <div class="stat-change" id="pendingWithdrawChange">待处理下分: <span id="pendingWithdraw">-</span></div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #dcfce7; color: #166534;">✅</div>
                                    <div>
                                        <div class="stat-number">正常</div>
                                        <div class="stat-label">系统状态</div>
                                    </div>
                                </div>
                                <div class="stat-change">服务运行正常</div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>🎮 游戏大厅快速入口</h3>
                            <p style="color: #64748b; margin-bottom: 20px;">快速访问各个游戏大厅</p>
                            <a href="/run/bj28" target="_blank" class="btn">螞蟻收益</a>
                            <a href="/run/ssc" target="_blank" class="btn">时时彩</a>
                            <a href="/run/幸运飞艇" target="_blank" class="btn">急速飞艇</a>
                            <a href="#" class="btn btn-warning" onclick="refreshStats()">刷新数据</a>
                        </div>
                    </div>
                    
                    <!-- 会员管理页面 -->
                    <div id="members" class="page-content">
                        <div class="search-bar">
                            <input type="text" id="userSearch" class="search-input" placeholder="搜索用户名或昵称...">
                            <button class="btn" onclick="loadUsers()">搜索</button>
                            <button class="btn btn-success" onclick="refreshUsers()">刷新</button>
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
                                        <th>标签</th>
                                        <th>注册时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody id="userList">
                                    <tr><td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">正在加载...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="userPagination" style="margin-top: 20px; text-align: center;"></div>
                    </div>
                    
                    <!-- 其他页面内容 -->
                    <div id="other-page" class="page-content">
                        <div class="card">
                            <h3>功能开发中</h3>
                            <p style="color: #64748b;">该功能正在开发中，敬请期待！</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 上分模态框 -->
        <div id="addPointsModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">用户上分</h3>
                    <button class="close" onclick="closeModal('addPointsModal')">&times;</button>
                </div>
                <form id="addPointsForm">
                    <div class="form-group">
                        <label>用户ID</label>
                        <input type="number" id="addUserId" readonly>
                    </div>
                    <div class="form-group">
                        <label>上分点数</label>
                        <input type="number" id="addPoints" min="1" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>备注</label>
                        <input type="text" id="addRemark" value="管理员上分">
                    </div>
                    <div style="text-align: right; margin-top: 25px;">
                        <button type="button" class="btn" onclick="closeModal('addPointsModal')">取消</button>
                        <button type="submit" class="btn btn-success">确认上分</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- 下分模态框 -->
        <div id="subPointsModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">用户下分</h3>
                    <button class="close" onclick="closeModal('subPointsModal')">&times;</button>
                </div>
                <form id="subPointsForm">
                    <div class="form-group">
                        <label>用户ID</label>
                        <input type="number" id="subUserId" readonly>
                    </div>
                    <div class="form-group">
                        <label>下分点数</label>
                        <input type="number" id="subPoints" min="1" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>备注</label>
                        <input type="text" id="subRemark" value="管理员下分">
                    </div>
                    <div style="text-align: right; margin-top: 25px;">
                        <button type="button" class="btn" onclick="closeModal('subPointsModal')">取消</button>
                        <button type="submit" class="btn btn-danger">确认下分</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // 登录处理
        <?php if (!$isLoggedIn): ?>
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
            })
            .catch(error => {
                document.getElementById('message').innerHTML = '<div class="message error">❌ 网络错误，请重试</div>';
            });
        });
        <?php else: ?>
        
        // 管理后台JavaScript
        let currentPage = 1;
        
        // 切换子菜单
        function toggleSubmenu(element) {
            element.classList.toggle('active');
        }
        
        // 显示页面
        function showPage(pageId) {
            // 更新导航状态
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // 隐藏所有页面
            document.querySelectorAll('.page-content').forEach(page => {
                page.classList.remove('active');
            });
            
            // 显示目标页面
            const targetPage = document.getElementById(pageId);
            if (targetPage) {
                targetPage.classList.add('active');
            } else {
                document.getElementById('other-page').classList.add('active');
            }
            
            // 更新页面标题
            const titles = {
                'dashboard': '系统概览',
                'members': '会员管理',
                'orders': '竞猜记录',
                'recharge': '上分申请',
                'withdraw': '下分申请',
                'point-records': '上下分记录',
                'payment': '收款设置',
                'agents': '代理列表',
                'agent-settings': '代理设置',
                'dividends': '代理分红',
                'lottery-settings': '开奖预设',
                'game-config': '游戏配置',
                'data-collect': '数据采集',
                'platform-stats': '平台统计',
                'user-stats': '用户统计',
                'win-lose': '输赢统计',
                'robots': '机器人设置',
                'robot-betting': '机器人竞猜',
                'site-config': '网站配置',
                'admin-users': '管理员管理',
                'admin-logs': '操作日志'
            };
            
            const pageTitle = titles[pageId] || '功能开发中';
            document.getElementById('page-title').textContent = pageTitle;
            
            // 根据页面类型加载数据
            if (pageId === 'members') {
                loadUsers();
            } else if (pageId === 'dashboard') {
                refreshStats();
            }
        }
        
        // 刷新统计数据
        function refreshStats() {
            fetch('?action=get_stats')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const stats = data.data;
                    document.getElementById('totalUsers').textContent = stats.total_users;
                    document.getElementById('todayUsers').textContent = stats.today_users;
                    document.getElementById('totalPoints').textContent = Number(stats.total_points).toLocaleString();
                    document.getElementById('pendingRecharge').textContent = stats.pending_recharge;
                    document.getElementById('pendingWithdraw').textContent = stats.pending_withdraw;
                }
            })
            .catch(error => console.error('刷新统计失败:', error));
        }
        
        // 加载用户列表
        function loadUsers(page = 1) {
            const search = document.getElementById('userSearch').value;
            
            fetch(`?action=get_users&page=${page}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderUserList(data.data.users);
                    renderPagination(data.data.page, data.data.pages);
                    currentPage = page;
                }
            })
            .catch(error => console.error('加载用户列表失败:', error));
        }
        
        // 渲染用户列表
        function renderUserList(users) {
            const tbody = document.getElementById('userList');
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">暂无数据</td></tr>';
                return;
            }
            
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.username || '未设置'}</td>
                    <td>${user.nickname || '未设置'}</td>
                    <td>${Number(user.points || 0).toLocaleString()}</td>
                    <td>
                        ${user.status == 1 ? 
                            '<span class="badge badge-success">正常</span>' : 
                            '<span class="badge badge-danger">禁用</span>'}
                    </td>
                    <td>
                        ${user.is_robot == 1 ? '<span class="badge badge-info">机器人</span>' : ''}
                        ${user.is_agent == 1 ? '<span class="badge badge-warning">代理</span>' : ''}
                    </td>
                    <td>${user.reg_time ? new Date(user.reg_time * 1000).toLocaleDateString() : '未知'}</td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="showAddPoints(${user.id})">上分</button>
                        <button class="btn btn-sm btn-warning" onclick="showSubPoints(${user.id})">下分</button>
                        ${user.status == 1 ? 
                            `<button class="btn btn-sm btn-danger" onclick="userAction(${user.id}, 'disable')">禁用</button>` :
                            `<button class="btn btn-sm btn-success" onclick="userAction(${user.id}, 'enable')">启用</button>`}
                        ${user.is_robot == 1 ? 
                            `<button class="btn btn-sm" onclick="userAction(${user.id}, 'cancel_robot')">取消机器人</button>` :
                            `<button class="btn btn-sm" onclick="userAction(${user.id}, 'set_robot')">设为机器人</button>`}
                    </td>
                </tr>
            `).join('');
        }
        
        // 渲染分页
        function renderPagination(current, total) {
            const container = document.getElementById('userPagination');
            let html = '';
            
            if (current > 1) {
                html += `<button class="btn btn-sm" onclick="loadUsers(${current - 1})">上一页</button>`;
            }
            
            for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
                const activeClass = i === current ? 'btn-success' : '';
                html += `<button class="btn btn-sm ${activeClass}" onclick="loadUsers(${i})">${i}</button>`;
            }
            
            if (current < total) {
                html += `<button class="btn btn-sm" onclick="loadUsers(${current + 1})">下一页</button>`;
            }
            
            container.innerHTML = html;
        }
        
        // 用户操作
        function userAction(userId, operation) {
            if (!confirm('确定要执行此操作吗？')) return;
            
            const formData = new FormData();
            formData.append('action', 'user_action');
            formData.append('user_id', userId);
            formData.append('operation', operation);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    loadUsers(currentPage);
                }
            })
            .catch(error => console.error('操作失败:', error));
        }
        
        // 显示上分模态框
        function showAddPoints(userId) {
            document.getElementById('addUserId').value = userId;
            document.getElementById('addPointsModal').classList.add('active');
        }
        
        // 显示下分模态框
        function showSubPoints(userId) {
            document.getElementById('subUserId').value = userId;
            document.getElementById('subPointsModal').classList.add('active');
        }
        
        // 关闭模态框
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        // 上分表单提交
        document.getElementById('addPointsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'add_points');
            formData.append('user_id', document.getElementById('addUserId').value);
            formData.append('points', document.getElementById('addPoints').value);
            formData.append('remark', document.getElementById('addRemark').value);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    closeModal('addPointsModal');
                    loadUsers(currentPage);
                    refreshStats();
                }
            })
            .catch(error => console.error('上分失败:', error));
        });
        
        // 下分表单提交
        document.getElementById('subPointsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'sub_points');
            formData.append('user_id', document.getElementById('subUserId').value);
            formData.append('points', document.getElementById('subPoints').value);
            formData.append('remark', document.getElementById('subRemark').value);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    closeModal('subPointsModal');
                    loadUsers(currentPage);
                    refreshStats();
                }
            })
            .catch(error => console.error('下分失败:', error));
        });
        
        // 刷新用户列表
        function refreshUsers() {
            loadUsers(currentPage);
        }
        
        // 搜索用户
        document.getElementById('userSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loadUsers(1);
            }
        });
        
        // 页面加载完成后初始化
        window.addEventListener('load', function() {
            refreshStats();
        });
        
        <?php endif; ?>
    </script>
</body>
</html>