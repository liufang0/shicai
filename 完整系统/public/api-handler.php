<?php
// API测试页面
header('Content-Type: text/html; charset=utf-8');

// 检查是否为API请求
$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/admin-control/') !== false) {
    header('Content-Type: application/json');
    
    // 模拟API响应
    $response = [];
    
    if (strpos($requestUri, '/get-system-stats') !== false) {
        $response = [
            'code' => 200,
            'message' => 'success',
            'data' => [
                'total_users' => 156,
                'online_users' => 23,
                'total_bets' => 8900,
                'today_revenue' => 45600.50,
                'system_status' => 'running'
            ]
        ];
    } elseif (strpos($requestUri, '/get-user-list') !== false) {
        $response = [
            'code' => 200,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'username' => 'admin', 'money' => 0, 'status' => 1],
                ['id' => 2, 'username' => 'test001', 'money' => 1250.50, 'status' => 1],
                ['id' => 3, 'username' => 'test002', 'money' => 800.20, 'status' => 1]
            ]
        ];
    } elseif (strpos($requestUri, '/login') !== false && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = [
            'code' => 200,
            'message' => '登录成功',
            'status' => 'success',
            'data' => ['token' => 'api_token_' . time()]
        ];
    } else {
        $response = [
            'code' => 404,
            'message' => 'API接口不存在',
            'status' => 'error'
        ];
    }
    
    echo json_encode($response);
    exit;
}

// 检查采集状态API
if (strpos($requestUri, '/collect/status') !== false) {
    header('Content-Type: application/json');
    echo json_encode([
        'code' => 200,
        'message' => '采集正常',
        'data' => [
            'status' => 'running',
            'last_update' => date('Y-m-d H:i:s'),
            'collected_count' => 1250
        ]
    ]);
    exit;
}
?>