<?php
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型使用Think
    'type'          => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule'     => 3,  // 保持原有的控制器方法名
    // 模板目录名
    'view_dir_name' => 'view',
    // 模板后缀
    'view_suffix'   => 'html',
    // 模板文件名分隔符
    'view_depr'     => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'     => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'       => '}',
    // 标签库标签开始标记
    'taglib_begin'  => '{',
    // 标签库标签结束标记
    'taglib_end'    => '}',
    
    // 模板替换 - 兼容原有的静态资源路径
    'tpl_replace_string' => [
        '__PUBLIC__'    => '/public',
        '__ROOT__'      => '/',
        '__STATIC__'    => '/public',
        '__CSS__'       => '/public/Home/css',
        '__JS__'        => '/public/Home/js',
        '__IMG__'       => '/public/images',
        '__UPLOAD__'    => '/public/uploads',
        '__ADMIN__'     => '/public/Admin',
        '__HOME__'      => '/public/Home',
        '__COMMON__'    => '/public/Common',
    ],
];
