<?php
// 应用公共文件

/**
 * 授权检查函数
 */
function auth_check($authCode, $host) {
    // 临时返回true用于测试，实际项目需要实现具体的授权逻辑
    return true;
}

/**
 * 检测是否为微信浏览器
 */
function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

/**
 * 检测是否为移动设备
 */
function is_mobile() {
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    
    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    
    return false;
}

/**
 * 六合彩数据格式化
 */
function lhc_format($caiji) {
    $shengxiao = array(
        1=>'狗', 13=>'狗', 25=>'狗', 37=>'狗', 49=>'狗', 
        12=>'猪', 24=>'猪', 36=>'猪', 48=>'猪', 
        11=>'鼠', 23=>'鼠', 35=>'鼠', 47=>'鼠', 
        10=>'牛', 22=>'牛', 34=>'牛', 46=>'牛', 
        9=>'虎', 21=>'虎', 33=>'虎', 45=>'虎',
        8=>'兔', 20=>'兔', 32=>'兔', 44=>'兔',
        7=>'龙', 19=>'龙', 31=>'龙', 43=>'龙',
        6=>'蛇', 18=>'蛇', 30=>'蛇', 42=>'蛇',
        5=>'马', 17=>'马', 29=>'马', 41=>'马',
        4=>'羊', 16=>'羊', 28=>'羊', 40=>'羊',
        3=>'猴', 15=>'猴', 27=>'猴', 39=>'猴',
        2=>'鸡', 14=>'鸡', 26=>'鸡', 38=>'鸡',
    );
    
    $time = strtotime($caiji['awardtime']);
    $w = date("w", $time);
    if ($w == 6) {
        $time = $time + 24*60*60*3;
    } else {
        $time = $time + 24*60*60*2;
    }
    
    $data = array();
    $data['drawIssue'] = $caiji['periodnumber'] + 1;
    $data['drawTime'] = date('Y-m-d H:i:s', $time);
    $data['preDrawCode'] = $caiji['awardnumbers'];
    $data['preDrawIssue'] = $caiji['periodnumber']; 
    $data['preDrawTime'] = $caiji['awardtime'];
    
    $code = explode(',', $data['preDrawCode']);
    $opentm = strtotime($data['preDrawTime']) + 24*60*60;
    
    $json = array(
        'Game' => 'six',
        'Name' => '六合彩',
        'OpenDateTime' => date('m.d', strtotime($data['preDrawTime'])) . ' 21:35',
        'OpenIndex' => $data['preDrawIssue'],
        'OpenLh' => $shengxiao[$code[0]] . ',' . $shengxiao[$code[1]] . ',' . $shengxiao[$code[2]] . ',' . $shengxiao[$code[3]] . ',' . $shengxiao[$code[4]] . ',' . $shengxiao[$code[5]] . ',' . $shengxiao[$code[6]],
        'OpenNumber' => $data['preDrawCode'],
        'OpenTime' => strtotime($data['drawTime']) - time(),
        'OpenTm' => $opentm,
        'ServerTime' => (int)time() . '000'
    );
    
    return json_encode($json);
}

/**
 * 六合彩数据格式化2
 */
function lhc_format2($caiji) {
    $periodNumber = $caiji['periodnumber'];
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];
    $time = strtotime($awardTime);
    $w = date("w", $time);
    
    if ($w == 6) {
        $time = $time + 24*60*60*3;
    } else {
        $time = $time + 24*60*60*2;
    }
    
    $n_awardTime = $time;
    
    $pkdata = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
        ),
        'game' => "lhc"
    );
    
    return json_encode($pkdata);
}

/**
 * 幸运飞艇数据格式化
 */
function 幸运飞艇_format($caiji) {
    $periodNumber = $caiji['periodnumber'];
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];
    $n_awardTime = strtotime($awardTime) + 75;

    $pkdata = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() < 0 ? 0 : $n_awardTime - time()
        ),
        'game' => $caiji['game']
    );
    
    return json_encode($pkdata);
}

/**
 * 时时彩数据格式化
 */
function er75sc_format($caiji) {
    $periodNumber = $caiji['periodnumber'];
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];
    
    if (strtotime($awardTime) > strtotime("04:00:00") && strtotime($awardTime) < strtotime("07:25:00")) {
        $time = strtotime($awardTime) + 3.5*60*60;
        $n_awardTime = strtotime(date('Y-m-d H:i:s', $time));
    } else {
        $n_awardTime = strtotime($awardTime) + 75;
    }

    $pkdata = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
        ),
        'game' => $caiji['game']
    );
    
    return json_encode($pkdata);
}

/**
 * 幸运飞艇数据格式化
 */
function xyft_format($caiji) {
    $periodNumber = intval(substr($caiji['periodnumber'], 2, 11));
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];

    if (strtotime($awardTime) > strtotime("04:03:00") && strtotime($awardTime) < strtotime("13:03:00")) {
        $time = strtotime($awardTime) + 9*60*60 - 300;
        $n_awardTime = strtotime(date('Y-m-d H:i:s', $time));
    } else {
        $n_awardTime = strtotime($awardTime) + 300;
    }

    $pkdata = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
        ),
        'game' => $caiji['game']
    );
    
    return json_encode($pkdata);
}

/**
 * 北京28数据格式化
 */
function bj28_format($caiji) {
    $periodNumber = $caiji['periodnumber'];
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];
    $n_awardTime = strtotime($awardTime) + 300;

    $bj28data = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
        ),
        'game' => $caiji['game']
    );
    
    return json_encode($bj28data);
}

/**
 * 加拿大28数据格式化
 */
function jnd28_format($caiji) {
    $periodNumber = $caiji['periodnumber'];
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];
    $n_awardTime = strtotime($awardTime) + 210;

    $jnd28data = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
        ),
        'game' => $caiji['game']
    );
    
    return json_encode($jnd28data);
}

/**
 * 新疆28数据格式化
 */
function xjp28_format($caiji) {
    return bj28_format($caiji); // 与北京28格式相同
}

/**
 * K3数据格式化
 */
function k3_format($caiji) {
    $periodNumber = $caiji['periodnumber'];
    $awardTime = $caiji['awardtime'];
    $awardNumbers = $caiji['awardnumbers'];
    $n_awardTime = strtotime($awardTime) + 600;

    $k3data = array(
        'time' => time(),
        'current' => array(
            'periodNumber' => $periodNumber,
            'awardTime' => $awardTime,
            'awardNumbers' => $awardNumbers
        ),
        'next' => array(
            'periodNumber' => $periodNumber + 1,
            'awardTime' => date('Y-m-d H:i:s', $n_awardTime),
            'awardTimeInterval' => abs($n_awardTime - time()) * 1000,
            'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
        ),
        'game' => $caiji['game']
    );
    
    return json_encode($k3data);
}

/**
 * 获取用户昵称
 */
function get_nickname($userid) {
    $userinfo = \think\facade\Db::table('user')->where("id", $userid)->find();
    if ($userinfo && $userinfo['nickname']) {
        return $userinfo['nickname'];
    } else {
        return false;
    }
}

/**
 * HTTP GET请求
 */
function http_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, trim($url));
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if (strpos($url, 'https') !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $result = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $result = curl_error($ch);
    }
    
    curl_close($ch);
    return $result;
}
