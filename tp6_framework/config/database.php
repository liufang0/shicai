<?php

return [
    // 默认使用的数据库连接配置
    'default'         => 'mysql',

    // 自定义时间查询规则
    'time_query_rule' => [],

    // 自动写入时间戳字段
    'auto_timestamp'  => true,

    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',

    // 时间字段配置 配置格式：create_time,update_time
    'datetime_field'  => '',

    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'            => 'mysql',
            // 服务器地址
            'hostname'        => '127.0.0.1',
            // 数据库名
            'database'        => 'root',
            // 用户名
            'username'        => 'root',
            // 密码
            'password'        => '123456',
            // 端口
            'hostport'        => '3306',
            // 数据库表前缀
            'prefix'          => 'think_',
            // 数据库编码
            'charset'         => 'utf8',
            // 数据库调试模式
            'debug'           => true,
            // 数据库部署方式
            'deploy'          => 0,
            // 数据库读写是否分离
            'rw_separate'     => false,
        ],
    ],
];
