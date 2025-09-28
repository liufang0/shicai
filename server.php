<?php
#!/usr/bin/env php
define('APP_PATH','./Application/');

//绑定控制器
define('BIND_CONTROLLER', 'Workerman');

define('BIND_MODULE', 'Home');

// 加载框架引导文件
require './ThinkPHP/ThinkPHP.php';
?>