<?php
return array(
	//'配置项'=>'配置值'
	'SHOW_PAGE_TRACE' => false,
	
	// 加载扩展配置文件
	'LOAD_EXT_CONFIG' => 'site,db', 
	
    //模版设置相关
    'TMPL_ACTION_SUCCESS'   => 'Public/dispatch_jump',
    'TMPL_ACTION_ERROR'     => 'Public/dispatch_jump',
	
    /*SESSION配置*/
	'SESSION_OPTIONS'=>array(
	    //'type'=> 'db',//session采用数据库,init时间超过1秒
	    'expire'=>3600*24,//session过期时间，如果不设就是php.ini中设置的默认值
	  ),
	//'SESSION_TABLE'=>'think_session', //必须设置成这样，如果不加前缀就找不到数据表，这个需要注意
	
	/*URL设置*/
	'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
	'URL_PATHINFO_DEPR'     =>  '/',    // PATHINFO模式下，各参数之间的分割符号
	'URL_PATHINFO_FETCH'    =>  'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
	'URL_REQUEST_URI'       =>  'REQUEST_URI', // 获取当前页面地址的系统变量 默认为REQUEST_URI
	'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
	'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
	'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
	'URL_PARAMS_BIND_TYPE'  =>  0, // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
	'URL_404_REDIRECT'      =>  '', // 404 跳转页面 部署模式有效
	'URL_ROUTER_ON'         =>  false,   // 是否开启URL路由
	'URL_ROUTE_RULES'       =>  array(), // 默认路由规则 针对模块
	'URL_MAP_RULES'         =>  array(), // URL映射定义规则
	
);
?>
	
