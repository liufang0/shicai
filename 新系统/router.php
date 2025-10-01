<?php
/**
 * ThinkPHP Router for Built-in PHP Server
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 处理静态文件
if (preg_match('/\.(?:css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $requestUri)) {
    $file = __DIR__ . '/public' . $requestUri;
    if (file_exists($file)) {
        return false; // 让内置服务器处理静态文件
    }
}

// 所有其他请求都转到入口文件
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/public/index.php';