<?php
// PHP内置服务器路由脚本
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// 检查是否为静态文件请求
$staticExtensions = ['png', 'jpg', 'jpeg', 'gif', 'css', 'js', 'ico', 'svg'];
$extension = pathinfo($path, PATHINFO_EXTENSION);

if (in_array(strtolower($extension), $staticExtensions)) {
    // 构建文件路径
    $filePath = __DIR__ . $path;
    
    if (file_exists($filePath)) {
        // 设置正确的MIME类型
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg', 
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml'
        ];
        
        $mimeType = isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        readfile($filePath);
        return true;
    }
}

// 其他请求交给index.php处理
return false;