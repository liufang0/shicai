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
    'db_path' => dirname(dirname(__FILE__)) . '/shicai.db',  // SQLite数据库路径
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
        
        /* 游戏标签样式 */
        .game-tabs {
            display: flex;
            gap: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }
        
        .game-tab {
            padding: 12px 24px;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            color: #6b7280;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .game-tab:hover {
            border-color: #6366f1;
            color: #6366f1;
        }
        
        .game-tab.active {
            background: #6366f1;
            color: white;
            border-color: #6366f1;
        }
        
        .game-panel {
            margin-top: 25px;
        }
        
        .form-row {
            display: flex;
            align-items: end;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .lottery-numbers {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 16px;
            color: #dc2626;
            letter-spacing: 2px;
        }
        
        /* 支付方式标签样式 */
        .payment-tabs {
            display: flex;
            gap: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }
        
        .payment-tab {
            padding: 12px 24px;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            color: #6b7280;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .payment-tab:hover {
            border-color: #6366f1;
            color: #6366f1;
        }
        
        .payment-tab.active {
            background: #6366f1;
            color: white;
            border-color: #6366f1;
        }
        
        .payment-panel {
            margin-top: 25px;
        }
        
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
                    <input type="hidden" name="action" value="login">
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
                            <li><a href="#" class="nav-link" onclick="showPage('game-config')">游戏配置</a></li>
                            <li><a href="#" class="nav-link" onclick="showPage('data-collect')">数据采集</a></li>
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
                    
                    <!-- 竞猜记录页面 -->
                    <div id="orders" class="page-content">
                        <div class="search-bar">
                            <input type="text" id="orderSearch" class="search-input" placeholder="搜索用户ID或游戏类型...">
                            <button class="btn" onclick="loadOrders()">搜索</button>
                            <button class="btn btn-success" onclick="refreshOrders()">刷新</button>
                        </div>
                        
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>订单ID</th>
                                        <th>用户ID</th>
                                        <th>游戏类型</th>
                                        <th>下注金额</th>
                                        <th>获胜金额</th>
                                        <th>盈亏</th>
                                        <th>状态</th>
                                        <th>时间</th>
                                    </tr>
                                </thead>
                                <tbody id="orderList">
                                    <tr><td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">正在加载...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- 上分申请页面 -->
                    <div id="recharge" class="page-content">
                        <div class="card">
                            <h3>📈 上分申请管理</h3>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>申请ID</th>
                                            <th>用户ID</th>
                                            <th>申请金额</th>
                                            <th>状态</th>
                                            <th>申请时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rechargeList">
                                        <tr><td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">暂无上分申请</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 下分申请页面 -->
                    <div id="withdraw" class="page-content">
                        <div class="card">
                            <h3>📉 下分申请管理</h3>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>申请ID</th>
                                            <th>用户ID</th>
                                            <th>申请金额</th>
                                            <th>状态</th>
                                            <th>申请时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="withdrawList">
                                        <tr><td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">暂无下分申请</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 上下分记录页面 -->
                    <div id="point-records" class="page-content">
                        <div class="search-bar">
                            <input type="text" id="recordSearch" class="search-input" placeholder="搜索用户ID...">
                            <select id="recordType" class="search-input" style="width: 150px;">
                                <option value="">全部类型</option>
                                <option value="1">上分记录</option>
                                <option value="0">下分记录</option>
                            </select>
                            <button class="btn" onclick="loadPointRecords()">搜索</button>
                            <button class="btn btn-success" onclick="refreshPointRecords()">刷新</button>
                        </div>
                        
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>记录ID</th>
                                        <th>用户ID</th>
                                        <th>类型</th>
                                        <th>金额</th>
                                        <th>状态</th>
                                        <th>备注</th>
                                        <th>操作员</th>
                                        <th>时间</th>
                                    </tr>
                                </thead>
                                <tbody id="recordList">
                                    <tr><td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">正在加载...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- 代理列表页面 -->
                    <div id="agents" class="page-content">
                        <div class="search-bar">
                            <input type="text" id="agentSearch" class="search-input" placeholder="搜索代理用户名...">
                            <button class="btn" onclick="loadAgents()">搜索</button>
                            <button class="btn btn-success" onclick="refreshAgents()">刷新</button>
                        </div>
                        
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>用户ID</th>
                                        <th>用户名</th>
                                        <th>昵称</th>
                                        <th>代理级别</th>
                                        <th>下级用户数</th>
                                        <th>总业绩</th>
                                        <th>分成比例</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody id="agentList">
                                    <tr><td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">正在加载...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- 开奖预设页面 -->
                    <div id="lottery-settings" class="page-content">
                        <div class="card">
                            <h3>🎲 开奖预设管理</h3>
                            <p style="color: #64748b; margin-bottom: 30px;">设置各游戏的开奖号码预设，支持期号管理</p>
                            
                            <!-- 游戏选择标签 -->
                            <div class="game-tabs" style="margin-bottom: 30px;">
                                <button class="btn game-tab active" data-game="xyft" onclick="switchGame('xyft')">🚁 急速飞艇</button>
                                <button class="btn game-tab" data-game="ssc" onclick="switchGame('ssc')">⏰ 时时彩</button>
                            </div>
                            
                            <!-- 急速飞艇预设 -->
                            <div id="game-xyft" class="game-panel active">
                                <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                    <h4 style="margin-bottom: 20px;">🚁 急速飞艇开奖预设</h4>
                                    <form id="xyft-form" onsubmit="return addLottery('xyft')">
                                        <div class="form-row" style="display: flex; gap: 15px; align-items: end; margin-bottom: 20px;">
                                            <div class="form-group" style="min-width: 120px;">
                                                <label>期号</label>
                                                <input type="number" id="xyft_period" placeholder="期号" required style="padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px; width: 100%;">
                                            </div>
                                            
                                            <div class="form-group" style="flex: 1;">
                                                <label>开奖号码 (10个号码)</label>
                                                <div style="display: flex; gap: 8px;">
                                                    <input type="number" min="1" max="10" placeholder="01" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="02" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="03" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="04" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="05" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="06" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="07" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="08" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="09" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="1" max="10" placeholder="10" maxlength="2" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">添加预设</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>期号</th>
                                                <th>开奖号码</th>
                                                <th>开奖时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="xyft-list">
                                            <tr><td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">暂无预设记录</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- 时时彩预设 -->
                            <div id="game-ssc" class="game-panel" style="display: none;">
                                <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                    <h4 style="margin-bottom: 20px;">⏰ 时时彩开奖预设</h4>
                                    <form id="ssc-form" onsubmit="return addLottery('ssc')">
                                        <div class="form-row" style="display: flex; gap: 15px; align-items: end; margin-bottom: 20px;">
                                            <div class="form-group" style="min-width: 120px;">
                                                <label>期号</label>
                                                <input type="number" id="ssc_period" placeholder="期号" required style="padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px; width: 100%;">
                                            </div>
                                            
                                            <div class="form-group" style="flex: 1;">
                                                <label>开奖号码 (5个号码)</label>
                                                <div style="display: flex; gap: 8px;">
                                                    <input type="number" min="0" max="9" placeholder="1" maxlength="1" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="0" max="9" placeholder="2" maxlength="1" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="0" max="9" placeholder="3" maxlength="1" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="0" max="9" placeholder="4" maxlength="1" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                    <input type="number" min="0" max="9" placeholder="5" maxlength="1" required style="width: 50px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 4px; text-align: center;">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">添加预设</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>期号</th>
                                                <th>开奖号码</th>
                                                <th>开奖时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ssc-list">
                                            <tr><td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">暂无预设记录</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 机器人设置页面 -->
                    <div id="robots" class="page-content">
                        <div class="card">
                            <h3>🤖 机器人管理</h3>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                                <div class="card" style="margin: 0; padding: 20px;">
                                    <h4>机器人设置</h4>
                                    <div class="form-group">
                                        <label>启用机器人下注</label>
                                        <select style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                            <option value="1">启用</option>
                                            <option value="0">禁用</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>机器人下注间隔（秒）</label>
                                        <input type="number" value="30" min="10" max="300" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <div class="form-group">
                                        <label>单次下注金额范围</label>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <input type="number" placeholder="最小金额" value="10" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                            <span>-</span>
                                            <input type="number" placeholder="最大金额" value="500" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                        </div>
                                    </div>
                                    <button class="btn btn-success">保存设置</button>
                                </div>
                                
                                <div class="card" style="margin: 0; padding: 20px;">
                                    <h4>胜率控制</h4>
                                    <div class="form-group">
                                        <label>机器人胜率（%）</label>
                                        <input type="number" value="45" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                        <small style="color: #666;">建议设置在40-50%之间</small>
                                    </div>
                                    <div class="form-group">
                                        <label>平台盈利率（%）</label>
                                        <input type="number" value="10" min="0" max="50" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                        <small style="color: #666;">平台整体盈利控制</small>
                                    </div>
                                    <button class="btn btn-warning">更新胜率</button>
                                </div>
                            </div>
                            
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>机器人ID</th>
                                            <th>用户名</th>
                                            <th>状态</th>
                                            <th>今日下注次数</th>
                                            <th>今日下注金额</th>
                                            <th>今日输赢</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="robotList">
                                        <tr><td colspan="7" style="text-align: center; padding: 40px; color: #9ca3af;">正在加载...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 平台统计页面 -->
                    <div id="platform-stats" class="page-content">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">📊</div>
                                    <div>
                                        <div class="stat-number">¥125,860</div>
                                        <div class="stat-label">今日营收</div>
                                    </div>
                                </div>
                                <div class="stat-change up">较昨日 +15.2%</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #d1fae5; color: #065f46;">🎯</div>
                                    <div>
                                        <div class="stat-number">1,256</div>
                                        <div class="stat-label">今日投注次数</div>
                                    </div>
                                </div>
                                <div class="stat-change up">较昨日 +8.5%</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #fef3c7; color: #92400e;">💰</div>
                                    <div>
                                        <div class="stat-number">¥856,200</div>
                                        <div class="stat-label">今日投注金额</div>
                                    </div>
                                </div>
                                <div class="stat-change down">较昨日 -2.1%</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-icon" style="background: #f3e8ff; color: #7c3aed;">🏆</div>
                                    <div>
                                        <div class="stat-number">14.7%</div>
                                        <div class="stat-label">平台盈利率</div>
                                    </div>
                                </div>
                                <div class="stat-change">健康范围</div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>📈 7日营收趋势</h3>
                            <div style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 8px; color: #64748b;">
                                📊 图表功能开发中...
                            </div>
                        </div>
                    </div>
                    
                    <!-- 网站配置页面 -->
                    <div id="site-config" class="page-content">
                        <div class="card">
                            <h3>⚙️ 网站基础配置</h3>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                                <div>
                                    <div class="form-group">
                                        <label>网站名称</label>
                                        <input type="text" value="食彩游戏平台" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <div class="form-group">
                                        <label>网站描述</label>
                                        <textarea rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">专业的线上游戏平台</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>客服QQ</label>
                                        <input type="text" placeholder="请输入客服QQ号码" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <div class="form-group">
                                        <label>客服微信</label>
                                        <input type="text" placeholder="请输入客服微信号" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="form-group">
                                        <label>网站状态</label>
                                        <select style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                            <option value="1">正常运行</option>
                                            <option value="0">维护中</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>注册状态</label>
                                        <select style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                            <option value="1">允许注册</option>
                                            <option value="0">关闭注册</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>最小充值金额</label>
                                        <input type="number" value="10" min="1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <div class="form-group">
                                        <label>最小提现金额</label>
                                        <input type="number" value="50" min="1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button class="btn btn-success">保存配置</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 管理员管理页面 -->
                    <div id="admin-users" class="page-content">
                        <div class="card">
                            <h3>👨‍💼 管理员管理</h3>
                            
                            <div style="margin-bottom: 20px;">
                                <button class="btn btn-success" onclick="showAddAdmin()">添加管理员</button>
                            </div>
                            
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>用户名</th>
                                            <th>昵称</th>
                                            <th>状态</th>
                                            <th>创建时间</th>
                                            <th>最后登录</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminList">
                                        <tr><td colspan="7" style="text-align: center; padding: 40px; color: #9ca3af;">正在加载...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 收款设置页面 -->
                    <div id="payment" class="page-content">
                        <div class="card">
                            <h3>💰 收款方式设置</h3>
                            <p style="color: #64748b; margin-bottom: 30px;">配置多种收款方式：数字货币、支付宝、微信</p>
                            
                            <!-- 收款方式标签 -->
                            <div class="payment-tabs" style="margin-bottom: 30px;">
                                <button class="btn payment-tab active" data-payment="usdt" onclick="switchPayment('usdt')">₮ USDT</button>
                                <button class="btn payment-tab" data-payment="alipay" onclick="switchPayment('alipay')">💰 支付宝</button>
                                <button class="btn payment-tab" data-payment="wechat" onclick="switchPayment('wechat')">💚 微信</button>
                            </div>
                            
                            <!-- USDT TRC20 收款设置 -->
                            <div id="payment-usdt" class="payment-panel active">
                                <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                    <h4 style="margin-bottom: 20px; color: #059669;">
                                        <span style="font-size: 24px;">₮</span> USDT (TRC20) 收款设置
                                    </h4>
                                
                                <form id="usdt-form" onsubmit="return saveUSDTSettings()">
                                    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 30px;">
                                        <!-- 左侧：地址和设置 -->
                                        <div>
                                            <div class="form-group" style="margin-bottom: 20px;">
                                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                    🏦 USDT TRC20 收款地址
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="usdt_address" 
                                                    placeholder="输入TRC20网络USDT收款地址，如：TRx2WPb8PbJp..."
                                                    style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-family: monospace; font-size: 14px;"
                                                    pattern="T[A-Za-z0-9]{33}"
                                                    title="请输入有效的TRC20地址（以T开头，34位字符）"
                                                >
                                                <small style="color: #6b7280; font-size: 12px;">
                                                    ⚠️ 请确保地址正确，错误地址可能导致资金丢失
                                                </small>
                                            </div>
                                            
                                            <div class="form-group" style="margin-bottom: 20px;">
                                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                    📱 收款二维码
                                                </label>
                                                <div style="display: flex; gap: 10px; align-items: center;">
                                                    <input 
                                                        type="file" 
                                                        id="qr_upload" 
                                                        accept="image/*"
                                                        style="flex: 1; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                        onchange="previewQRCode()"
                                                    >
                                                    <button type="button" class="btn btn-info" onclick="generateQRCode()" style="white-space: nowrap;">
                                                        🔄 自动生成
                                                    </button>
                                                </div>
                                                <small style="color: #6b7280; font-size: 12px;">
                                                    支持 JPG、PNG 格式，建议尺寸 300x300 像素
                                                </small>
                                            </div>
                                            
                                            <div class="form-group" style="margin-bottom: 20px;">
                                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                    ⚙️ 收款设置
                                                </label>
                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                                    <div>
                                                        <label style="font-size: 14px; color: #6b7280;">最小充值金额</label>
                                                        <input 
                                                            type="number" 
                                                            id="min_amount" 
                                                            placeholder="10"
                                                            min="1"
                                                            step="0.01"
                                                            style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                        >
                                                    </div>
                                                    <div>
                                                        <label style="font-size: 14px; color: #6b7280;">最大充值金额</label>
                                                        <input 
                                                            type="number" 
                                                            id="max_amount" 
                                                            placeholder="50000"
                                                            min="1"
                                                            step="0.01"
                                                            style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>
                                                    <input type="checkbox" id="auto_confirm" style="margin-right: 8px;">
                                                    <span style="font-weight: 500;">启用自动确认到账</span>
                                                </label>
                                                <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 4px;">
                                                    开启后，用户充值将自动确认（需要区块链查询接口）
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <!-- 右侧：二维码预览 -->
                                        <div>
                                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                👀 二维码预览
                                            </label>
                                            <div 
                                                id="qr_preview" 
                                                style="
                                                    width: 250px; 
                                                    height: 250px; 
                                                    border: 2px dashed #d1d5db; 
                                                    border-radius: 12px; 
                                                    display: flex; 
                                                    align-items: center; 
                                                    justify-content: center; 
                                                    background: #f9fafb;
                                                    margin-bottom: 15px;
                                                "
                                            >
                                                <span style="color: #9ca3af; text-align: center;">
                                                    📸<br>上传或生成<br>二维码预览
                                                </span>
                                            </div>
                                            
                                            <div style="text-align: center;">
                                                <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px;">
                                                    💾 保存设置
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>
                            
                            <!-- 支付宝收款设置 -->
                            <div id="payment-alipay" class="payment-panel" style="display: none;">
                                <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                    <h4 style="margin-bottom: 20px; color: #1890ff;">
                                        💰 支付宝收款设置
                                    </h4>
                                    
                                    <form id="alipay-form" onsubmit="return saveAlipaySettings()">
                                        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 30px;">
                                            <!-- 左侧：设置 -->
                                            <div>
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                        🏦 支付宝账号信息
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        id="alipay_account" 
                                                        placeholder="支付宝账号/手机号"
                                                        style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; margin-bottom: 10px;"
                                                    >
                                                    <input 
                                                        type="text" 
                                                        id="alipay_name" 
                                                        placeholder="收款人姓名"
                                                        style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px;"
                                                    >
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                        📱 支付宝收款二维码
                                                    </label>
                                                    <input 
                                                        type="file" 
                                                        id="alipay_qr_upload" 
                                                        accept="image/*"
                                                        style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                        onchange="previewAlipayQR()"
                                                    >
                                                    <small style="color: #6b7280; font-size: 12px;">
                                                        上传支付宝收款二维码，支持 JPG、PNG 格式
                                                    </small>
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                        ⚙️ 收款设置
                                                    </label>
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                                        <div>
                                                            <label style="font-size: 14px; color: #6b7280;">最小充值金额</label>
                                                            <input 
                                                                type="number" 
                                                                id="alipay_min_amount" 
                                                                placeholder="10"
                                                                min="1"
                                                                step="0.01"
                                                                style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label style="font-size: 14px; color: #6b7280;">最大充值金额</label>
                                                            <input 
                                                                type="number" 
                                                                id="alipay_max_amount" 
                                                                placeholder="10000"
                                                                min="1"
                                                                step="0.01"
                                                                style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" id="alipay_enabled" style="margin-right: 8px;">
                                                        <span style="font-weight: 500;">启用支付宝收款</span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- 右侧：二维码预览 -->
                                            <div>
                                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                    👀 二维码预览
                                                </label>
                                                <div 
                                                    id="alipay_qr_preview" 
                                                    style="
                                                        width: 250px; 
                                                        height: 250px; 
                                                        border: 2px dashed #d1d5db; 
                                                        border-radius: 12px; 
                                                        display: flex; 
                                                        align-items: center; 
                                                        justify-content: center; 
                                                        background: #f9fafb;
                                                        margin-bottom: 15px;
                                                    "
                                                >
                                                    <span style="color: #9ca3af; text-align: center;">
                                                        📸<br>支付宝<br>收款码预览
                                                    </span>
                                                </div>
                                                
                                                <div style="text-align: center;">
                                                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                                                        💾 保存设置
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- 微信收款设置 -->
                            <div id="payment-wechat" class="payment-panel" style="display: none;">
                                <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                    <h4 style="margin-bottom: 20px; color: #07c160;">
                                        💚 微信收款设置
                                    </h4>
                                    
                                    <form id="wechat-form" onsubmit="return saveWechatSettings()">
                                        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 30px;">
                                            <!-- 左侧：设置 -->
                                            <div>
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                        🏦 微信账号信息
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        id="wechat_account" 
                                                        placeholder="微信号"
                                                        style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; margin-bottom: 10px;"
                                                    >
                                                    <input 
                                                        type="text" 
                                                        id="wechat_name" 
                                                        placeholder="微信昵称"
                                                        style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px;"
                                                    >
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                        📱 微信收款二维码
                                                    </label>
                                                    <input 
                                                        type="file" 
                                                        id="wechat_qr_upload" 
                                                        accept="image/*"
                                                        style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                        onchange="previewWechatQR()"
                                                    >
                                                    <small style="color: #6b7280; font-size: 12px;">
                                                        上传微信收款二维码，支持 JPG、PNG 格式
                                                    </small>
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                        ⚙️ 收款设置
                                                    </label>
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                                        <div>
                                                            <label style="font-size: 14px; color: #6b7280;">最小充值金额</label>
                                                            <input 
                                                                type="number" 
                                                                id="wechat_min_amount" 
                                                                placeholder="10"
                                                                min="1"
                                                                step="0.01"
                                                                style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label style="font-size: 14px; color: #6b7280;">最大充值金额</label>
                                                            <input 
                                                                type="number" 
                                                                id="wechat_max_amount" 
                                                                placeholder="10000"
                                                                min="1"
                                                                step="0.01"
                                                                style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" id="wechat_enabled" style="margin-right: 8px;">
                                                        <span style="font-weight: 500;">启用微信收款</span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- 右侧：二维码预览 -->
                                            <div>
                                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">
                                                    👀 二维码预览
                                                </label>
                                                <div 
                                                    id="wechat_qr_preview" 
                                                    style="
                                                        width: 250px; 
                                                        height: 250px; 
                                                        border: 2px dashed #d1d5db; 
                                                        border-radius: 12px; 
                                                        display: flex; 
                                                        align-items: center; 
                                                        justify-content: center; 
                                                        background: #f9fafb;
                                                        margin-bottom: 15px;
                                                    "
                                                >
                                                    <span style="color: #9ca3af; text-align: center;">
                                                        📸<br>微信<br>收款码预览
                                                    </span>
                                                </div>
                                                
                                                <div style="text-align: center;">
                                                    <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px;">
                                                        💾 保存设置
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- 当前收款信息展示 -->
                            <div class="card" style="padding: 20px;">
                                <h4 style="margin-bottom: 15px;">📋 当前收款信息</h4>
                                <div id="current_payment_info">
                                    <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; text-align: center; color: #6b7280;">
                                        暂未设置收款信息
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 充值记录 -->
                            <div class="card" style="padding: 20px;">
                                <h4 style="margin-bottom: 15px;">📊 近期充值记录</h4>
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>用户</th>
                                                <th>金额(USDT)</th>
                                                <th>交易哈希</th>
                                                <th>状态</th>
                                                <th>时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recharge_records">
                                            <tr><td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">暂无充值记录</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 游戏配置页面 -->
                    <div id="game-config" class="page-content">
                        <div class="card">
                            <h3>🎮 游戏配置管理</h3>
                            <p style="color: #64748b; margin-bottom: 30px;">配置游戏开关、赔率设置、时间配置等参数</p>
                            
                            <!-- 游戏开关设置 -->
                            <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                <h4 style="margin-bottom: 20px; color: #059669;">🔧 游戏开关设置</h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                                    <div class="game-switch-item">
                                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                                            <input type="checkbox" id="xyft_enabled" onchange="updateGameConfig()">
                                            <div>
                                                <strong style="color: #374151;">🚁 急速飞艇</strong>
                                                <small style="display: block; color: #6b7280;">5分钟一期，10个号码</small>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="game-switch-item">
                                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                                            <input type="checkbox" id="ssc_enabled" onchange="updateGameConfig()">
                                            <div>
                                                <strong style="color: #374151;">⏰ 时时彩</strong>
                                                <small style="display: block; color: #6b7280;">分钟一期，5个号码</small>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="game-switch-item">
                                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                                            <input type="checkbox" id="bj28_enabled" onchange="updateGameConfig()">
                                            <div>
                                                <strong style="color: #374151;">🎯 螞蟻收益</strong>
                                                <small style="display: block; color: #6b7280;">3分钟一期，3个号码</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 赔率设置 -->
                            <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                <h4 style="margin-bottom: 20px; color: #dc2626;">💰 游戏赔率设置</h4>
                                <div id="odds-settings">
                                    <div class="odds-game-section" data-game="xyft">
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">🚁 急速飞艇赔率</h5>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">大小单双</label>
                                                <input type="number" id="xyft_basic_odds" step="0.1" placeholder="1.95" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">冠亚军</label>
                                                <input type="number" id="xyft_champion_odds" step="0.1" placeholder="9.8" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">前三名</label>
                                                <input type="number" id="xyft_top3_odds" step="0.1" placeholder="9.8" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="odds-game-section" data-game="ssc">
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">⏰ 时时彩赔率</h5>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">大小单双</label>
                                                <input type="number" id="ssc_basic_odds" step="0.1" placeholder="1.95" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">前三组</label>
                                                <input type="number" id="ssc_group_odds" step="0.1" placeholder="98" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">豹子</label>
                                                <input type="number" id="ssc_leopard_odds" step="0.1" placeholder="180" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="odds-game-section" data-game="bj28">
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">🎯 螞蟻收益赔率</h5>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">大小单双</label>
                                                <input type="number" id="bj28_basic_odds" step="0.1" placeholder="1.95" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">组合</label>
                                                <input type="number" id="bj28_combo_odds" step="0.1" placeholder="8.5" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">极值</label>
                                                <input type="number" id="bj28_extreme_odds" step="0.1" placeholder="25" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="text-align: center; margin-top: 20px;">
                                    <button class="btn btn-success" onclick="saveOddsSettings()" style="padding: 12px 30px;">💾 保存赔率设置</button>
                                </div>
                            </div>
                            
                            <!-- 时间配置 -->
                            <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                <h4 style="margin-bottom: 20px; color: #7c2d12;">⏱️ 游戏时间配置</h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
                                    <div>
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">🚁 急速飞艇时间</h5>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">开盘时间</label>
                                                <input type="time" id="xyft_start_time" value="00:00" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">封盘时间</label>
                                                <input type="time" id="xyft_end_time" value="23:59" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <label style="font-size: 14px; color: #6b7280;">封盘提前秒数</label>
                                            <input type="number" id="xyft_close_before" value="30" min="0" max="300" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">⏰ 时时彩时间</h5>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">开盘时间</label>
                                                <input type="time" id="ssc_start_time" value="00:00" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">封盘时间</label>
                                                <input type="time" id="ssc_end_time" value="23:59" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <label style="font-size: 14px; color: #6b7280;">封盘提前秒数</label>
                                            <input type="number" id="ssc_close_before" value="30" min="0" max="300" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">🎯 螞蟻收益时间</h5>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">开盘时间</label>
                                                <input type="time" id="bj28_start_time" value="00:00" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">封盘时间</label>
                                                <input type="time" id="bj28_end_time" value="23:59" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <label style="font-size: 14px; color: #6b7280;">封盘提前秒数</label>
                                            <input type="number" id="bj28_close_before" value="30" min="0" max="300" style="width: 100%; padding: 8px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="text-align: center; margin-top: 20px;">
                                    <button class="btn btn-success" onclick="saveTimeSettings()" style="padding: 12px 30px;">💾 保存时间配置</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 数据采集页面 -->
                    <div id="data-collect" class="page-content">
                        <div class="card">
                            <h3>📊 数据采集管理</h3>
                            <p style="color: #64748b; margin-bottom: 30px;">配置自动开奖数据采集，对接第三方API</p>
                            
                            <!-- 采集配置 -->
                            <div class="card" style="padding: 25px; margin-bottom: 25px;">
                                <h4 style="margin-bottom: 20px; color: #059669;">🔗 采集API配置</h4>
                                <div style="display: grid; gap: 20px;">
                                    <div class="api-config-item">
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">🚁 急速飞艇采集</h5>
                                        <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 15px; align-items: end;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">API地址</label>
                                                <input type="url" id="xyft_api_url" placeholder="https://api.example.com/xyft" style="width: 100%; padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label>
                                                    <input type="checkbox" id="xyft_auto_collect" style="margin-right: 8px;">
                                                    <span style="font-weight: 500;">启用自动采集</span>
                                                </label>
                                            </div>
                                            <div>
                                                <button class="btn btn-info" onclick="testAPI('xyft')">测试连接</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="api-config-item">
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">⏰ 时时彩采集</h5>
                                        <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 15px; align-items: end;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">API地址</label>
                                                <input type="url" id="ssc_api_url" placeholder="https://api.example.com/ssc" style="width: 100%; padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label>
                                                    <input type="checkbox" id="ssc_auto_collect" style="margin-right: 8px;">
                                                    <span style="font-weight: 500;">启用自动采集</span>
                                                </label>
                                            </div>
                                            <div>
                                                <button class="btn btn-info" onclick="testAPI('ssc')">测试连接</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="api-config-item">
                                        <h5 style="color: #1f2937; margin-bottom: 15px;">🎯 螞蟻收益采集</h5>
                                        <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 15px; align-items: end;">
                                            <div>
                                                <label style="font-size: 14px; color: #6b7280;">API地址</label>
                                                <input type="url" id="bj28_api_url" placeholder="https://api.example.com/bj28" style="width: 100%; padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px;">
                                            </div>
                                            <div>
                                                <label>
                                                    <input type="checkbox" id="bj28_auto_collect" style="margin-right: 8px;">
                                                    <span style="font-weight: 500;">启用自动采集</span>
                                                </label>
                                            </div>
                                            <div>
                                                <button class="btn btn-info" onclick="testAPI('bj28')">测试连接</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="text-align: center; margin-top: 25px;">
                                    <button class="btn btn-success" onclick="saveCollectSettings()" style="padding: 12px 30px;">💾 保存采集配置</button>
                                </div>
                            </div>
                            
                            <!-- 采集状态 -->
                            <div class="card" style="padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin-bottom: 15px;">📈 采集状态监控</h4>
                                <div id="collect-status">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                                        <div class="status-card" style="padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span>🚁 急速飞艇</span>
                                                <span class="badge badge-success" id="xyft_status">正常</span>
                                            </div>
                                            <small style="color: #6b7280; display: block; margin-top: 5px;">最后采集: <span id="xyft_last_time">--</span></small>
                                        </div>
                                        
                                        <div class="status-card" style="padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span>⏰ 时时彩</span>
                                                <span class="badge badge-success" id="ssc_status">正常</span>
                                            </div>
                                            <small style="color: #6b7280; display: block; margin-top: 5px;">最后采集: <span id="ssc_last_time">--</span></small>
                                        </div>
                                        
                                        <div class="status-card" style="padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span>🎯 螞蟻收益</span>
                                                <span class="badge badge-success" id="bj28_status">正常</span>
                                            </div>
                                            <small style="color: #6b7280; display: block; margin-top: 5px;">最后采集: <span id="bj28_last_time">--</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 最新开奖 -->
                            <div class="card" style="padding: 20px;">
                                <h4 style="margin-bottom: 15px;">🎲 最新开奖数据</h4>
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>游戏</th>
                                                <th>期号</th>
                                                <th>开奖号码</th>
                                                <th>开奖时间</th>
                                                <th>采集时间</th>
                                                <th>状态</th>
                                            </tr>
                                        </thead>
                                        <tbody id="latest-results">
                                            <tr><td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">暂无开奖数据</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                'game-config': '游戏配置',
                'data-collect': '数据采集',
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
            } else if (pageId === 'lottery-settings') {
                initLotteryPresets();
            } else if (pageId === 'payment') {
                initPaymentSettings();
            } else if (pageId === 'game-config') {
                loadGameConfig();
            } else if (pageId === 'data-collect') {
                loadCollectConfig();
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
        
        // 新功能JavaScript函数
        
        // 加载竞猜记录
        function loadOrders() {
            const tbody = document.getElementById('orderList');
            if (!tbody) return;
            
            // 模拟订单数据
            tbody.innerHTML = `
                <tr>
                    <td>1001</td>
                    <td>1</td>
                    <td>螞蟻收益</td>
                    <td>100.00</td>
                    <td>180.00</td>
                    <td style="color: green;">+80.00</td>
                    <td><span class="badge badge-success">已完成</span></td>
                    <td>2025-09-30 23:15</td>
                </tr>
                <tr>
                    <td>1002</td>
                    <td>2</td>
                    <td>时时彩</td>
                    <td>200.00</td>
                    <td>0.00</td>
                    <td style="color: red;">-200.00</td>
                    <td><span class="badge badge-success">已完成</span></td>
                    <td>2025-09-30 23:10</td>
                </tr>
                <tr>
                    <td>1003</td>
                    <td>3</td>
                    <td>急速飞艇</td>
                    <td>500.00</td>
                    <td>950.00</td>
                    <td style="color: green;">+450.00</td>
                    <td><span class="badge badge-success">已完成</span></td>
                    <td>2025-09-30 23:05</td>
                </tr>
            `;
        }
        
        function refreshOrders() {
            loadOrders();
        }
        
        // 加载上下分记录
        function loadPointRecords() {
            const tbody = document.getElementById('recordList');
            if (!tbody) return;
            
            tbody.innerHTML = `
                <tr>
                    <td>201</td>
                    <td>1</td>
                    <td><span class="badge badge-success">上分</span></td>
                    <td>1000.00</td>
                    <td><span class="badge badge-success">已完成</span></td>
                    <td>管理员充值</td>
                    <td>admin</td>
                    <td>2025-09-30 22:30</td>
                </tr>
                <tr>
                    <td>202</td>
                    <td>2</td>
                    <td><span class="badge badge-warning">下分</span></td>
                    <td>500.00</td>
                    <td><span class="badge badge-success">已完成</span></td>
                    <td>用户提现</td>
                    <td>admin</td>
                    <td>2025-09-30 21:45</td>
                </tr>
            `;
        }
        
        function refreshPointRecords() {
            loadPointRecords();
        }
        
        // 加载代理列表
        function loadAgents() {
            const tbody = document.getElementById('agentList');
            if (!tbody) return;
            
            tbody.innerHTML = `
                <tr>
                    <td>2</td>
                    <td>agent01</td>
                    <td>代理用户1</td>
                    <td>一级代理</td>
                    <td>15</td>
                    <td>¥125,600</td>
                    <td>15%</td>
                    <td>
                        <button class="btn btn-sm">详情</button>
                        <button class="btn btn-sm btn-warning">编辑</button>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>user002</td>
                    <td>测试用户2</td>
                    <td>二级代理</td>
                    <td>8</td>
                    <td>¥56,800</td>
                    <td>8%</td>
                    <td>
                        <button class="btn btn-sm">详情</button>
                        <button class="btn btn-sm btn-warning">编辑</button>
                    </td>
                </tr>
            `;
        }
        
        function refreshAgents() {
            loadAgents();
        }
        
        // 加载机器人列表
        function loadRobots() {
            const tbody = document.getElementById('robotList');
            if (!tbody) return;
            
            tbody.innerHTML = `
                <tr>
                    <td>3</td>
                    <td>robot01</td>
                    <td><span class="badge badge-success">运行中</span></td>
                    <td>156</td>
                    <td>¥15,600</td>
                    <td style="color: red;">-¥1,250</td>
                    <td>
                        <button class="btn btn-sm btn-danger">停止</button>
                        <button class="btn btn-sm">设置</button>
                    </td>
                </tr>
            `;
        }
        
        // 加载管理员列表
        function loadAdminUsers() {
            const tbody = document.getElementById('adminList');
            if (!tbody) return;
            
            tbody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>admin</td>
                    <td>超级管理员</td>
                    <td><span class="badge badge-success">正常</span></td>
                    <td>2025-09-30</td>
                    <td>刚刚</td>
                    <td>
                        <button class="btn btn-sm btn-warning">编辑</button>
                        <button class="btn btn-sm">重置密码</button>
                    </td>
                </tr>
            `;
        }
        
        // 游戏预设数据存储 - 迁移原来的逻辑
        let lotteryPresets = {
            xyft: JSON.parse(localStorage.getItem('xyft_presets') || '[]'),
            ssc: JSON.parse(localStorage.getItem('ssc_presets') || '[]')
        };
        
        // 游戏切换函数
        function switchGame(gameType) {
            // 切换标签激活状态
            document.querySelectorAll('.game-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-game="${gameType}"]`).classList.add('active');
            
            // 切换面板显示
            document.querySelectorAll('.game-panel').forEach(panel => {
                panel.style.display = 'none';
                panel.classList.remove('active');
            });
            
            const targetPanel = document.getElementById(`game-${gameType}`);
            targetPanel.style.display = 'block';
            targetPanel.classList.add('active');
            
            // 刷新列表
            loadLotteryList(gameType);
        }
        
        // 添加预设函数 - 基于原YusheController逻辑
        function addLottery(gameType) {
            const form = document.getElementById(`${gameType}-form`);
            const period = document.getElementById(`${gameType}_period`).value;
            
            // 获取号码输入
            let numbers = [];
            const inputs = form.querySelectorAll('input[type="number"]:not(#' + gameType + '_period)');
            
            inputs.forEach(input => {
                const val = input.value.padStart(gameType === 'xyft' ? 2 : 1, '0');
                numbers.push(val);
            });
            
            // 验证输入
            if (!period || numbers.some(n => !n)) {
                alert('请完整填写期号和所有号码！');
                return false;
            }
            
            // 检查期号是否已存在
            if (lotteryPresets[gameType].some(item => item.period == period)) {
                alert('该期号已存在，请更换期号！');
                return false;
            }
            
            // 验证号码范围
            if (gameType === 'xyft') {
                // 急速飞艇：1-10
                if (numbers.some(n => parseInt(n) < 1 || parseInt(n) > 10)) {
                    alert('急速飞艇号码必须在01-10范围内！');
                    return false;
                }
                // 检查重复号码
                if (new Set(numbers).size !== numbers.length) {
                    alert('急速飞艇号码不能重复！');
                    return false;
                }
            } else if (gameType === 'ssc') {
                // 时时彩：0-9
                if (numbers.some(n => parseInt(n) < 0 || parseInt(n) > 9)) {
                    alert('时时彩号码必须在0-9范围内！');
                    return false;
                }
            }
            
            // 添加预设记录
            const preset = {
                id: Date.now(),
                period: period,
                numbers: numbers.join(''),
                numbersDisplay: numbers.join(' '),
                time: new Date().toLocaleString('zh-CN'),
                gameType: gameType
            };
            
            lotteryPresets[gameType].unshift(preset);
            
            // 保存到本地存储
            localStorage.setItem(`${gameType}_presets`, JSON.stringify(lotteryPresets[gameType]));
            
            // 清空表单
            form.reset();
            
            // 刷新列表
            loadLotteryList(gameType);
            
            alert(`${gameType === 'xyft' ? '急速飞艇' : '时时彩'}预设添加成功！期号：${period}，号码：${preset.numbersDisplay}`);
            
            return false;
        }
        
        // 加载预设列表
        function loadLotteryList(gameType) {
            const tbody = document.getElementById(`${gameType}-list`);
            const presets = lotteryPresets[gameType] || [];
            
            if (presets.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">暂无预设记录</td></tr>';
                return;
            }
            
            tbody.innerHTML = presets.map(preset => `
                <tr>
                    <td><strong>${preset.period}</strong></td>
                    <td><span class="lottery-numbers" style="font-family: monospace; font-weight: bold; color: #dc2626;">${preset.numbersDisplay}</span></td>
                    <td>${preset.time}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteLottery('${gameType}', ${preset.id})" style="padding: 4px 8px; font-size: 12px;">删除</button>
                    </td>
                </tr>
            `).join('');
        }
        
        // 删除预设
        function deleteLottery(gameType, id) {
            if (!confirm('确定要删除这个预设吗？')) {
                return;
            }
            
            lotteryPresets[gameType] = lotteryPresets[gameType].filter(item => item.id !== id);
            localStorage.setItem(`${gameType}_presets`, JSON.stringify(lotteryPresets[gameType]));
            
            loadLotteryList(gameType);
            alert('预设删除成功！');
        }
        
        // 初始化预设列表
        function initLotteryPresets() {
            // 默认显示急速飞艇
            switchGame('xyft');
        }
        
        // 数字货币收款设置功能
        let paymentSettings = JSON.parse(localStorage.getItem('payment_settings') || '{}');
        
        // 保存USDT设置
        function saveUSDTSettings() {
            const address = document.getElementById('usdt_address').value.trim();
            const minAmount = document.getElementById('min_amount').value;
            const maxAmount = document.getElementById('max_amount').value;
            const autoConfirm = document.getElementById('auto_confirm').checked;
            const qrFile = document.getElementById('qr_upload').files[0];
            
            // 验证TRC20地址格式
            if (!address) {
                alert('请输入USDT TRC20收款地址！');
                return false;
            }
            
            if (!/^T[A-Za-z0-9]{33}$/.test(address)) {
                alert('请输入有效的TRC20地址格式！\n地址应以T开头，总长度34位字符。');
                return false;
            }
            
            // 验证金额设置
            if (minAmount && maxAmount && parseFloat(minAmount) >= parseFloat(maxAmount)) {
                alert('最小充值金额不能大于等于最大充值金额！');
                return false;
            }
            
            // 保存设置
            paymentSettings.usdt = {
                address: address,
                minAmount: minAmount || '10',
                maxAmount: maxAmount || '50000',
                autoConfirm: autoConfirm,
                updatedAt: new Date().toLocaleString('zh-CN')
            };
            
            // 处理二维码
            if (qrFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    paymentSettings.usdt.qrCode = e.target.result;
                    savePaymentSettings();
                };
                reader.readAsDataURL(qrFile);
            } else {
                savePaymentSettings();
            }
            
            return false;
        }
        
        // 保存到本地存储并更新显示
        function savePaymentSettings() {
            localStorage.setItem('payment_settings', JSON.stringify(paymentSettings));
            updatePaymentInfo();
            alert('USDT收款设置保存成功！');
        }
        
        // 预览上传的二维码
        function previewQRCode() {
            const file = document.getElementById('qr_upload').files[0];
            const preview = document.getElementById('qr_preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
        
        // 自动生成二维码
        function generateQRCode() {
            const address = document.getElementById('usdt_address').value.trim();
            
            if (!address) {
                alert('请先输入USDT收款地址！');
                return;
            }
            
            if (!/^T[A-Za-z0-9]{33}$/.test(address)) {
                alert('请输入有效的TRC20地址格式！');
                return;
            }
            
            // 使用第三方二维码生成API
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(address)}&bgcolor=FFFFFF&color=000000&margin=10`;
            
            const preview = document.getElementById('qr_preview');
            preview.innerHTML = `
                <img src="${qrUrl}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                     onload="this.style.opacity=1" style="opacity:0.5;">
            `;
            
            // 将生成的二维码保存到设置中
            if (!paymentSettings.usdt) paymentSettings.usdt = {};
            paymentSettings.usdt.qrCode = qrUrl;
            
            alert('二维码生成成功！请点击"保存设置"以保存配置。');
        }
        
        // 更新当前收款信息显示
        function updatePaymentInfo() {
            const infoDiv = document.getElementById('current_payment_info');
            
            if (!paymentSettings.usdt || !paymentSettings.usdt.address) {
                infoDiv.innerHTML = `
                    <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; text-align: center; color: #6b7280;">
                        暂未设置收款信息
                    </div>
                `;
                return;
            }
            
            const usdt = paymentSettings.usdt;
            const qrHtml = usdt.qrCode ? 
                `<img src="${usdt.qrCode}" style="width: 120px; height: 120px; border-radius: 8px; border: 2px solid #e5e7eb;">` :
                `<div style="width: 120px; height: 120px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 12px;">无二维码</div>`;
            
            infoDiv.innerHTML = `
                <div style="display: grid; grid-template-columns: 150px 1fr; gap: 20px; align-items: start;">
                    <div style="text-align: center;">
                        ${qrHtml}
                        <div style="margin-top: 8px; font-size: 12px; color: #6b7280;">USDT TRC20</div>
                    </div>
                    <div>
                        <div style="margin-bottom: 12px;">
                            <strong style="color: #374151;">收款地址：</strong>
                            <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px; word-break: break-all;">${usdt.address}</code>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
                            <div>
                                <strong style="color: #374151;">最小充值：</strong>
                                <span style="color: #059669;">${usdt.minAmount} USDT</span>
                            </div>
                            <div>
                                <strong style="color: #374151;">最大充值：</strong>
                                <span style="color: #059669;">${usdt.maxAmount} USDT</span>
                            </div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #374151;">自动确认：</strong>
                            <span class="badge ${usdt.autoConfirm ? 'badge-success' : 'badge-warning'}">${usdt.autoConfirm ? '已启用' : '已关闭'}</span>
                        </div>
                        <div style="font-size: 12px; color: #6b7280;">
                            最后更新：${usdt.updatedAt}
                        </div>
                    </div>
                </div>
            `;
        }
        
        // 支付方式切换函数
        function switchPayment(paymentType) {
            // 切换标签激活状态
            document.querySelectorAll('.payment-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-payment="${paymentType}"]`).classList.add('active');
            
            // 切换面板显示
            document.querySelectorAll('.payment-panel').forEach(panel => {
                panel.style.display = 'none';
            });
            
            const targetPanel = document.getElementById(`payment-${paymentType}`);
            targetPanel.style.display = 'block';
            
            // 根据支付方式加载对应设置
            if (paymentType === 'alipay') {
                loadAlipaySettings();
            } else if (paymentType === 'wechat') {
                loadWechatSettings();
            } else if (paymentType === 'usdt') {
                loadUSDTSettings();
            }
            
            updatePaymentInfo();
        }
        
        // 保存支付宝设置
        function saveAlipaySettings() {
            const account = document.getElementById('alipay_account').value.trim();
            const name = document.getElementById('alipay_name').value.trim();
            const minAmount = document.getElementById('alipay_min_amount').value;
            const maxAmount = document.getElementById('alipay_max_amount').value;
            const enabled = document.getElementById('alipay_enabled').checked;
            const qrFile = document.getElementById('alipay_qr_upload').files[0];
            
            if (enabled && (!account || !name)) {
                alert('启用支付宝收款时，账号和姓名为必填项！');
                return false;
            }
            
            // 验证金额设置
            if (minAmount && maxAmount && parseFloat(minAmount) >= parseFloat(maxAmount)) {
                alert('最小充值金额不能大于等于最大充值金额！');
                return false;
            }
            
            // 保存设置
            paymentSettings.alipay = {
                account: account,
                name: name,
                minAmount: minAmount || '10',
                maxAmount: maxAmount || '10000',
                enabled: enabled,
                updatedAt: new Date().toLocaleString('zh-CN')
            };
            
            // 处理二维码
            if (qrFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    paymentSettings.alipay.qrCode = e.target.result;
                    savePaymentSettings();
                };
                reader.readAsDataURL(qrFile);
            } else {
                savePaymentSettings();
            }
            
            return false;
        }
        
        // 保存微信设置
        function saveWechatSettings() {
            const account = document.getElementById('wechat_account').value.trim();
            const name = document.getElementById('wechat_name').value.trim();
            const minAmount = document.getElementById('wechat_min_amount').value;
            const maxAmount = document.getElementById('wechat_max_amount').value;
            const enabled = document.getElementById('wechat_enabled').checked;
            const qrFile = document.getElementById('wechat_qr_upload').files[0];
            
            if (enabled && (!account || !name)) {
                alert('启用微信收款时，账号和昵称为必填项！');
                return false;
            }
            
            // 验证金额设置
            if (minAmount && maxAmount && parseFloat(minAmount) >= parseFloat(maxAmount)) {
                alert('最小充值金额不能大于等于最大充值金额！');
                return false;
            }
            
            // 保存设置
            paymentSettings.wechat = {
                account: account,
                name: name,
                minAmount: minAmount || '10',
                maxAmount: maxAmount || '10000',
                enabled: enabled,
                updatedAt: new Date().toLocaleString('zh-CN')
            };
            
            // 处理二维码
            if (qrFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    paymentSettings.wechat.qrCode = e.target.result;
                    savePaymentSettings();
                };
                reader.readAsDataURL(qrFile);
            } else {
                savePaymentSettings();
            }
            
            return false;
        }
        
        // 预览支付宝二维码
        function previewAlipayQR() {
            const file = document.getElementById('alipay_qr_upload').files[0];
            const preview = document.getElementById('alipay_qr_preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
        
        // 预览微信二维码
        function previewWechatQR() {
            const file = document.getElementById('wechat_qr_upload').files[0];
            const preview = document.getElementById('wechat_qr_preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
        
        // 加载支付宝设置
        function loadAlipaySettings() {
            if (paymentSettings.alipay) {
                const alipay = paymentSettings.alipay;
                document.getElementById('alipay_account').value = alipay.account || '';
                document.getElementById('alipay_name').value = alipay.name || '';
                document.getElementById('alipay_min_amount').value = alipay.minAmount || '10';
                document.getElementById('alipay_max_amount').value = alipay.maxAmount || '10000';
                document.getElementById('alipay_enabled').checked = alipay.enabled || false;
                
                if (alipay.qrCode) {
                    document.getElementById('alipay_qr_preview').innerHTML = 
                        `<img src="${alipay.qrCode}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                }
            }
        }
        
        // 加载微信设置
        function loadWechatSettings() {
            if (paymentSettings.wechat) {
                const wechat = paymentSettings.wechat;
                document.getElementById('wechat_account').value = wechat.account || '';
                document.getElementById('wechat_name').value = wechat.name || '';
                document.getElementById('wechat_min_amount').value = wechat.minAmount || '10';
                document.getElementById('wechat_max_amount').value = wechat.maxAmount || '10000';
                document.getElementById('wechat_enabled').checked = wechat.enabled || false;
                
                if (wechat.qrCode) {
                    document.getElementById('wechat_qr_preview').innerHTML = 
                        `<img src="${wechat.qrCode}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                }
            }
        }
        
        // 加载USDT设置
        function loadUSDTSettings() {
            if (paymentSettings.usdt) {
                const usdt = paymentSettings.usdt;
                document.getElementById('usdt_address').value = usdt.address || '';
                document.getElementById('min_amount').value = usdt.minAmount || '10';
                document.getElementById('max_amount').value = usdt.maxAmount || '50000';
                document.getElementById('auto_confirm').checked = usdt.autoConfirm || false;
                
                if (usdt.qrCode) {
                    document.getElementById('qr_preview').innerHTML = 
                        `<img src="${usdt.qrCode}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                }
            }
        }
        
        // 初始化收款设置页面
        function initPaymentSettings() {
            // 默认显示USDT
            switchPayment('usdt');
        }
        
        // ==================== 游戏配置相关函数 ====================
        let gameConfig = {
            xyft: { enabled: false, odds: {}, timing: {} },
            ssc: { enabled: false, odds: {}, timing: {} },
            bj28: { enabled: false, odds: {}, timing: {} }
        };
        
        // 更新游戏配置
        function updateGameConfig() {
            gameConfig.xyft.enabled = document.getElementById('xyft_enabled').checked;
            gameConfig.ssc.enabled = document.getElementById('ssc_enabled').checked;
            gameConfig.bj28.enabled = document.getElementById('bj28_enabled').checked;
            
            localStorage.setItem('game_config', JSON.stringify(gameConfig));
        }
        
        // 保存赔率设置
        function saveOddsSettings() {
            // 保存急速飞艇赔率
            gameConfig.xyft.odds = {
                basic: parseFloat(document.getElementById('xyft_basic_odds').value) || 1.95,
                champion: parseFloat(document.getElementById('xyft_champion_odds').value) || 9.8,
                top3: parseFloat(document.getElementById('xyft_top3_odds').value) || 9.8
            };
            
            // 保存时时彩赔率
            gameConfig.ssc.odds = {
                basic: parseFloat(document.getElementById('ssc_basic_odds').value) || 1.95,
                group: parseFloat(document.getElementById('ssc_group_odds').value) || 98,
                leopard: parseFloat(document.getElementById('ssc_leopard_odds').value) || 180
            };
            
            // 保存螞蟻收益赔率
            gameConfig.bj28.odds = {
                basic: parseFloat(document.getElementById('bj28_basic_odds').value) || 1.95,
                combo: parseFloat(document.getElementById('bj28_combo_odds').value) || 8.5,
                extreme: parseFloat(document.getElementById('bj28_extreme_odds').value) || 25
            };
            
            localStorage.setItem('game_config', JSON.stringify(gameConfig));
            alert('赔率设置保存成功！');
        }
        
        // 保存时间配置
        function saveTimeSettings() {
            // 保存急速飞艇时间
            gameConfig.xyft.timing = {
                startTime: document.getElementById('xyft_start_time').value,
                endTime: document.getElementById('xyft_end_time').value,
                closeBefore: parseInt(document.getElementById('xyft_close_before').value) || 30
            };
            
            // 保存时时彩时间
            gameConfig.ssc.timing = {
                startTime: document.getElementById('ssc_start_time').value,
                endTime: document.getElementById('ssc_end_time').value,
                closeBefore: parseInt(document.getElementById('ssc_close_before').value) || 30
            };
            
            // 保存螞蟻收益时间
            gameConfig.bj28.timing = {
                startTime: document.getElementById('bj28_start_time').value,
                endTime: document.getElementById('bj28_end_time').value,
                closeBefore: parseInt(document.getElementById('bj28_close_before').value) || 30
            };
            
            localStorage.setItem('game_config', JSON.stringify(gameConfig));
            alert('时间配置保存成功！');
        }
        
        // 加载游戏配置
        function loadGameConfig() {
            const saved = localStorage.getItem('game_config');
            if (saved) {
                gameConfig = JSON.parse(saved);
                
                // 设置游戏开关
                document.getElementById('xyft_enabled').checked = gameConfig.xyft.enabled || false;
                document.getElementById('ssc_enabled').checked = gameConfig.ssc.enabled || false;
                document.getElementById('bj28_enabled').checked = gameConfig.bj28.enabled || false;
                
                // 加载赔率设置
                if (gameConfig.xyft.odds) {
                    document.getElementById('xyft_basic_odds').value = gameConfig.xyft.odds.basic || '';
                    document.getElementById('xyft_champion_odds').value = gameConfig.xyft.odds.champion || '';
                    document.getElementById('xyft_top3_odds').value = gameConfig.xyft.odds.top3 || '';
                }
                
                if (gameConfig.ssc.odds) {
                    document.getElementById('ssc_basic_odds').value = gameConfig.ssc.odds.basic || '';
                    document.getElementById('ssc_group_odds').value = gameConfig.ssc.odds.group || '';
                    document.getElementById('ssc_leopard_odds').value = gameConfig.ssc.odds.leopard || '';
                }
                
                if (gameConfig.bj28.odds) {
                    document.getElementById('bj28_basic_odds').value = gameConfig.bj28.odds.basic || '';
                    document.getElementById('bj28_combo_odds').value = gameConfig.bj28.odds.combo || '';
                    document.getElementById('bj28_extreme_odds').value = gameConfig.bj28.odds.extreme || '';
                }
                
                // 加载时间设置
                if (gameConfig.xyft.timing) {
                    document.getElementById('xyft_start_time').value = gameConfig.xyft.timing.startTime || '00:00';
                    document.getElementById('xyft_end_time').value = gameConfig.xyft.timing.endTime || '23:59';
                    document.getElementById('xyft_close_before').value = gameConfig.xyft.timing.closeBefore || 30;
                }
                
                if (gameConfig.ssc.timing) {
                    document.getElementById('ssc_start_time').value = gameConfig.ssc.timing.startTime || '00:00';
                    document.getElementById('ssc_end_time').value = gameConfig.ssc.timing.endTime || '23:59';
                    document.getElementById('ssc_close_before').value = gameConfig.ssc.timing.closeBefore || 30;
                }
                
                if (gameConfig.bj28.timing) {
                    document.getElementById('bj28_start_time').value = gameConfig.bj28.timing.startTime || '00:00';
                    document.getElementById('bj28_end_time').value = gameConfig.bj28.timing.endTime || '23:59';
                    document.getElementById('bj28_close_before').value = gameConfig.bj28.timing.closeBefore || 30;
                }
            }
        }
        
        // ==================== 数据采集相关函数 ====================
        let collectConfig = {
            xyft: { apiUrl: '', autoCollect: false },
            ssc: { apiUrl: '', autoCollect: false },
            bj28: { apiUrl: '', autoCollect: false }
        };
        
        let collectStatus = {
            xyft: { status: '未启用', lastTime: '--' },
            ssc: { status: '未启用', lastTime: '--' },
            bj28: { status: '未启用', lastTime: '--' }
        };
        
        // 测试API连接
        async function testAPI(game) {
            const apiUrl = document.getElementById(game + '_api_url').value;
            if (!apiUrl) {
                alert('请先输入API地址');
                return;
            }
            
            try {
                const response = await fetch(apiUrl, { method: 'GET', timeout: 5000 });
                if (response.ok) {
                    alert('API连接测试成功！');
                    updateCollectStatus(game, '连接正常', new Date().toLocaleString());
                } else {
                    alert('API连接失败：' + response.status);
                    updateCollectStatus(game, '连接失败', new Date().toLocaleString());
                }
            } catch (error) {
                alert('API连接错误：' + error.message);
                updateCollectStatus(game, '连接错误', new Date().toLocaleString());
            }
        }
        
        // 保存采集配置
        function saveCollectSettings() {
            collectConfig.xyft = {
                apiUrl: document.getElementById('xyft_api_url').value,
                autoCollect: document.getElementById('xyft_auto_collect').checked
            };
            
            collectConfig.ssc = {
                apiUrl: document.getElementById('ssc_api_url').value,
                autoCollect: document.getElementById('ssc_auto_collect').checked
            };
            
            collectConfig.bj28 = {
                apiUrl: document.getElementById('bj28_api_url').value,
                autoCollect: document.getElementById('bj28_auto_collect').checked
            };
            
            localStorage.setItem('collect_config', JSON.stringify(collectConfig));
            alert('数据采集配置保存成功！');
            
            // 启动或停止自动采集
            startAutoCollection();
        }
        
        // 更新采集状态
        function updateCollectStatus(game, status, time) {
            collectStatus[game] = { status, lastTime: time };
            
            // 更新界面显示
            const statusElement = document.getElementById(game + '_status');
            const timeElement = document.getElementById(game + '_last_time');
            
            if (statusElement && timeElement) {
                statusElement.textContent = status;
                statusElement.className = 'badge badge-' + (status.includes('正常') || status.includes('成功') ? 'success' : 'danger');
                timeElement.textContent = time;
            }
        }
        
        // 启动自动数据采集
        function startAutoCollection() {
            // 清除现有定时器
            if (window.collectionTimer) {
                clearInterval(window.collectionTimer);
            }
            
            // 如果有启用自动采集的游戏，开始定时采集
            const hasAutoCollect = Object.values(collectConfig).some(config => config.autoCollect && config.apiUrl);
            
            if (hasAutoCollect) {
                window.collectionTimer = setInterval(async () => {
                    for (const [game, config] of Object.entries(collectConfig)) {
                        if (config.autoCollect && config.apiUrl) {
                            try {
                                const response = await fetch(config.apiUrl);
                                if (response.ok) {
                                    const data = await response.json();
                                    updateCollectStatus(game, '采集正常', new Date().toLocaleString());
                                    updateLatestResults(game, data);
                                } else {
                                    updateCollectStatus(game, '采集失败', new Date().toLocaleString());
                                }
                            } catch (error) {
                                updateCollectStatus(game, '采集错误', new Date().toLocaleString());
                            }
                        }
                    }
                }, 30000); // 每30秒采集一次
            }
        }
        
        // 更新最新开奖数据
        function updateLatestResults(game, data) {
            const tableBody = document.getElementById('latest-results');
            if (!tableBody) return;
            
            // 如果是第一条数据，清除"暂无数据"的提示
            if (tableBody.children.length === 1 && tableBody.children[0].children.length === 1) {
                tableBody.innerHTML = '';
            }
            
            // 创建新行
            const row = document.createElement('tr');
            const gameNames = { xyft: '🚁 急速飞艇', ssc: '⏰ 时时彩', bj28: '🎯 螞蟻收益' };
            
            row.innerHTML = `
                <td>${gameNames[game] || game}</td>
                <td>${data.period || '--'}</td>
                <td style="font-weight: bold; color: #dc2626;">${data.numbers ? data.numbers.join(',') : '--'}</td>
                <td>${data.openTime || '--'}</td>
                <td>${new Date().toLocaleString()}</td>
                <td><span class="badge badge-success">已采集</span></td>
            `;
            
            // 插入到表格顶部
            tableBody.insertBefore(row, tableBody.firstChild);
            
            // 保持最多10条记录
            while (tableBody.children.length > 10) {
                tableBody.removeChild(tableBody.lastChild);
            }
        }
        
        // 加载采集配置
        function loadCollectConfig() {
            const saved = localStorage.getItem('collect_config');
            if (saved) {
                collectConfig = JSON.parse(saved);
                
                // 设置API地址和自动采集开关
                document.getElementById('xyft_api_url').value = collectConfig.xyft.apiUrl || '';
                document.getElementById('xyft_auto_collect').checked = collectConfig.xyft.autoCollect || false;
                
                document.getElementById('ssc_api_url').value = collectConfig.ssc.apiUrl || '';
                document.getElementById('ssc_auto_collect').checked = collectConfig.ssc.autoCollect || false;
                
                document.getElementById('bj28_api_url').value = collectConfig.bj28.apiUrl || '';
                document.getElementById('bj28_auto_collect').checked = collectConfig.bj28.autoCollect || false;
                
                // 启动自动采集
                startAutoCollection();
            }
        }
        
        <?php endif; ?>
    </script>
</body>
</html>