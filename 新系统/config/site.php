<?php
return [
    // 站点配置
    'sitename' => '蚂蚁金服',
    'siteurl' => '127.0.0.1',
    'welcome' => '欢迎莅临，祝您竞猜愉快！！！',
    'auth_code' => 'your_auth_code_here',
    
    // 系统开关
    'is_open' => '1',
    'is_open_reg' => '1',
    'is_weixin' => '1',
    'is_qrcode' => '0',
    'is_baidu' => '0',
    'is_say' => '0',
    'robot' => '1',
    'robot_rate' => '5',
    'baidu_value' => '888',
    
    // 微信支付配置
    'mp_choose' => '1',
    'mp_host_url' => '127.0.0.1',
    'WEIXINPAY_CONFIG' => [
        'CAIJI_KEY' => 't6904900282d5f413k',
        'APPID' => 'wxc7e0e93a98ac61b5',
        'APPSECRET' => '50a3d0049e4a3464ce07e5088dcf0cf0',
    ],
    
    // 分销配置
    'fenxiao' => '0.5',
    'fenxiao_min' => '0',
    'fenxiao_set' => '2',
    'fs_rate' => '0.5',
    
    // 金额限制
    'money_limit' => '100-50000|30000-500000|100000-50000000',
    
    // 开奖设置
    'kj_set' => '1',
    'gy_set' => '1',
    
    // API接口配置
    'pk10_api' => 'http://39.104.56.250/caiji/?name=bjpks',
    'xyft_api' => 'http://39.104.56.250/caiji/?name=xyft',
    'ssc_api' => 'http://39.104.56.250/caiji/?name=cqssc',
    'bj28_api' => 'http://39.104.56.250/caiji/?name=bj28',
    'jnd28_api' => 'http://39.104.56.250/caiji/?name=jnd28',
    'lhc_api' => 'http://39.104.56.250/caiji/?name=lhc',
    
    // PK10游戏配置
    'pk10_min_point' => '10',
    'pk10qi_min_point' => '10',
    'pk10qi_max_point' => '100000',
    'pk10_dxds' => '1.98',
    'pk10_chehao' => '9.8',
    'pk10_zuhe_1' => '4',
    'pk10_zuhe_2' => '3',
    'pk10_lh' => '1.98',
    'pk10_zx' => '1.94',
    'pk10_gy' => '40',
    'pk10_tema_1' => '2.2',
    'pk10_tema_2' => '1.7',
    'pk10_tema_sz_1' => '40',
    'pk10_tema_sz_2' => '20',
    'pk10_tema_sz_3' => '12',
    'pk10_tema_sz_4' => '9',
    'pk10_tema_sz_5' => '8',
    'pk10_tema_qd_1' => '5',
    'pk10_tema_qd_2' => '1.5',
    'pk10_xz_open' => [
        'dxds' => '1',
        'chehao' => '1',
        'zuhe_1' => '1',
        'lh' => '1',
        'zx' => '1',
        'gy' => '1',
        'tema' => '1',
        'tema_sz_1' => '1',
        'tema_qd_1' => '1',
    ],
    
    // 幸运飞艇配置
    'ft_min_point' => '10',
    'ftqi_min_point' => '50',
    'ftqi_max_point' => '100000',
    'ft_dxds' => '1.98',
    'ft_chehao' => '9.8',
    'ft_zuhe_1' => '4',
    'ft_zuhe_2' => '3',
    'ft_lh' => '1.98',
    'ft_zx' => '1.94',
    'ft_gy' => '40',
    'ft_tema_1' => '2.2',
    'ft_tema_2' => '1.7',
    'ft_tema_sz_1' => '40',
    'ft_tema_sz_2' => '20',
    'ft_tema_sz_3' => '12',
    'ft_tema_sz_4' => '9',
    'ft_tema_sz_5' => '8',
    'ft_tema_qd_1' => '4',
    'ft_tema_qd_2' => '1.5',
    'ft_xz_open' => [
        'dxds' => '1',
        'chehao' => '1',
        'zuhe_1' => '0',
        'lh' => '1',
        'zx' => '0',
        'gy' => '1',
        'tema' => '1',
        'tema_sz_1' => '1',
        'tema_qd_1' => '0',
    ],
    
    // 时时彩配置
    'ssc_min_point' => '10',
    'sscqi_min_point' => '50',
    'sscqi_max_point' => '100000',
    'ssc_dwq' => '9.7',
    'ssc_lhh_1' => '2.1',
    'ssc_lhh_2' => '2.1',
    'ssc_dxds' => '1.98',
    'ssc_zhx' => '1.98',
    'ssc_hz' => '1.98',
    'ssc_lhqw' => '1.98',
    'ssc_dxqw' => '1.98',
    'ssc_dsqw' => '1.98',
    
    // 28类游戏配置
    'bj28_min_point' => '10',
    'bj28_max_point' => '100000',
    'bj28_dxds' => '1.98',
    'bj28_hz' => '1.98',
    'bj28_jd' => '4.5',
    
    'jnd28_min_point' => '10',
    'jnd28_max_point' => '100000',
    'jnd28_dxds' => '1.98',
    'jnd28_hz' => '1.98',
    'jnd28_jd' => '4.5',
    
    'xjp28_min_point' => '10',
    'xjp28_max_point' => '100000',
    'xjp28_dxds' => '1.98',
    'xjp28_hz' => '1.98',
    'xjp28_jd' => '4.5',
    
    // 六合彩配置
    'lhc_min_point' => '10',
    'lhc_max_point' => '100000',
    'lhc_tm' => '42',
    'lhc_sx' => '9',
    'lhc_se' => '2.8',
    'lhc_lh' => '1.98',
    'lhc_dxds' => '1.98',
    'lhc_wh' => '1.98',
    
    // K3配置
    'k3_min_point' => '10',
    'k3_max_point' => '100000',
    'k3_hz' => '1.98',
    'k3_dxds' => '1.98',
    'k3_thtx' => '230',
    'k3_duizi' => '10',
    'k3_shunzi' => '35',
    
    // 游戏状态控制
    'pk10_state' => '1',
    'xyft_state' => '1',
    'ssc_state' => '1',
    'bj28_state' => '1',
    'jnd28_state' => '1',
    'xjp28_state' => '1',
    'lhc_state' => '1',
    'k3_state' => '1',
    
    // 代理配置
    'agent_pay' => '0',
    'agent_rate' => '0.5',
    
    // 安全配置
    'login_verify' => '1',
    'reg_verify' => '0',
    'ip_limit' => '10',
    'bet_limit' => '5',
];