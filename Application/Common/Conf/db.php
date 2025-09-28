<?php
/*
 *  mysql配置
 * */
return array(
	'DB_TYPE' => 'mysql', //数据库类型
    'DB_HOST' => '127.0.0.1', //数据库主机
    'DB_NAME' => 'root', //数据库名称
    'DB_USER' => 'root', //数据库用户名
    'DB_PWD' => '123456', //数据库密码
    'DB_PORT' => '3306', //数据库端口
    'DB_PREFIX' => 'think_', //数据库前缀
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_DEBUG'  => '', // 数据库调试模式 开启后可以记录SQL日志
);
?>