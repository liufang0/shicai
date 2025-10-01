<?php
function api($name, $vars = array())
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = $array ? array_pop($array) : 'Common';
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
    if (is_string($vars)) {
        parse_str($vars, $vars);
    }

    return call_user_func_array($callback, $vars);
}


function die_dump($v){
    echo "<pre>";
    var_dump($v);
    die();
}
/*
 * 删除缓存方法
 */
function delFileByDir($dir) {
	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if ($file != "." && $file != "..") {

			$fullpath = $dir . "/" . $file;
			if (is_dir($fullpath)) {
				delFileByDir($fullpath);
			} else {
				unlink($fullpath);
			}
		}
	}
	closedir($dh);
}


/**
 * 判断是否在微信浏览器中打开
 * @return boolean
 */
function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

    /**
     * 判断是移动设备访问
     */
function is_mobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

/*根据id获取头像
 * */
function get_nickname($userid){
	$userinfo = M('user')->where("id = $userid")->find();
	if($userinfo['nickname']){
		return $userinfo['nickname'];
	}else{
		return false;
	}
} 

/*post请求获取数据*/
function curlPost($url, $timeout = 5) {
	if (function_exists('file_get_contents')) {
		$optionget = array('http' => array('method' => "GET", 'header' => "User-Agent:Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.21022; .NET CLR 3.0.04506; CIBA)\r\nAccept:*/*\r\nReferer:https://kyfw.12306.cn/otn/lcxxcx/init"));
		$file_contents = file_get_contents($url, false, stream_context_create($optionget));
	} else {
		$ch = curl_init();
		$header = array('Accept:*/*', 'Accept-Charset:GBK,utf-8;q=0.7,*;q=0.3', 'Accept-Encoding:gzip,deflate,sdch', 'Accept-Language:zh-CN,zh;q=0.8,ja;q=0.6,en;q=0.4', 'Connection:keep-alive', 'Host:kyfw.12306.cn', 'Referer:https://kyfw.12306.cn/otn/lcxxcx/init', );
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
	}
	$file_contents = json_decode($file_contents, true);
	return $file_contents;
}
/*get请求获取数据*/
function curlGet($url){
	$ch = curl_init();
	//设置选项，包括URL
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
    if (strpos($url, 'https') !== false) {
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }
	//执行并获取HTML文档内容
	$output = curl_exec($ch);
	//释放curl句柄
	curl_close($ch);
	return $output;
}

    function http_get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // 获取的信息以文件流的形式返回
        // curl_setopt($ch,CURLOPT_USERAGENT, $user_agent);

//        curl_setopt($ch, CURLOPT_HEADER, $return_header); // 显示返回的Header区域内容
	    if (strpos($url, 'https') !== false) {
	        // curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    }
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时限制防止死循环
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_error($ch);  // 捕抓异常
        }
        return $result;
    }
/**
 *
 * @param $url     网址
 * @param $Referer 来路设置
 * @param $timeout 链接超时时长
 */

function sockget($url,$Referer=1,$timeout=5){
	 $info = parse_url($url);
	 $fp = fsockopen($info['host'], 80);
	if(!$fp) {
		die('无法连接');
               //echo "$errstr ($errno)&lt;br&gt;\n";
	} else {		
	stream_set_blocking($fp,0);//开启非阻塞模式 
	
		stream_set_timeout($fp, 5);  //读取超时
		$end = "\r\n";
	         $head = "GET ".$info['path'].($info["query"]?'?'.$info['query']:'')." HTTP/1.1$end";
		$head .= "Host: ".$info['host']."$end";
		//伪造来路页面
		$head .= "Referer: https://".$info['host'].($Referer!=1?$info['path'].($info["query"]?'?'.$info['query']:''):'')."$end";
		$head.="User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)$end";
		$head .= "Connection: Close$end";
		$head .= "$end";

  		// echo $head;
  		// exit;
		fputs($fp,$head);
		$info = stream_get_meta_data($fp);//获取读取内容是否超时
		
	 	if ($info['timed_out']) {
	 		 fclose($fp);
        	         echo 'Connection timed out!';
        	         die('发送数据超时');
    	         }
	       $content='';
                while(!feof($fp) && (!$info['timed_out'])) {
                $content.=fgets($fp,1024);
                $info = stream_get_meta_data($fp);
                flush(); 
        }
        fclose($fp);
        if ($info['timed_out']) {
        die('读取数据 Connection timed out!');
    	}

        return $content;
      }
}

function getConfigs()
{
    $config = S('DB_CONFIG_DATA');
    if (! $config) {
        $config = api('Config/lists');
        S('DB_CONFIG_DATA', $config);
    }
    C($config); // 添加配置
}
 

function get__browser() {
	// 默认为 chrome 标准浏览器
	$browser = array(
		'device'=>'pc', // pc|mobile|pad
		'name'=>'chrome', // chrome|firefox|ie|opera
		'version'=>30,
	);
	$agent = $_SERVER['HTTP_USER_AGENT'];
	// 主要判断是否为垃圾 IE6789
	if(strpos($agent, 'msie') !== FALSE || stripos($agent, 'trident') !== FALSE) {
		$browser['name'] = 'ie';
		$browser['version'] = 8;
		preg_match('#msie\s*([\d\.]+)#is', $agent, $m);
		if(!empty($m[1])) {
			if(strpos($agent, 'compatible; msie 7.0;') !== FALSE) {
				$browser['version'] = 8;
			} else {
				$browser['version'] = intval($m[1]);
			}
		} else {
			// 匹配兼容模式 Trident/7.0，兼容模式下会有此标志 $trident = 7;
			preg_match('#Trident/([\d\.]+)#is', $agent, $m);
			if(!empty($m[1])) {
				$trident = intval($m[1]);
				$trident == 4 AND $browser['version'] = 8;
				$trident == 5 AND $browser['version'] = 9;
				$trident > 5 AND $browser['version'] = 10;
			}
		}
	}

	if(isset($_SERVER['HTTP_X_WAP_PROFILE']) || (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap") || stripos($agent, 'phone')  || stripos($agent, 'mobile') || strpos($agent, 'ipod'))) {
		$browser['device'] = 'mobile';
	} elseif(strpos($agent, 'pad') !== FALSE) {
		$browser['device'] = 'pad';
		$browser['name'] = '';
		$browser['version'] = '';
	/*
	} elseif(strpos($agent, 'miui') !== FALSE) {
		$browser['device'] = 'mobile';
		$browser['name'] = 'xiaomi';
		$browser['version'] = '';
	*/
	} else {
		$robots = array('bot', 'spider', 'slurp');
		foreach($robots as $robot) {
			if(strpos($agent, $robot) !== FALSE) {
				$browser['name'] = 'robot';
				return $browser;
			}
		}
	}
	return $browser;
}


/*
 * 生成二维码
 * */
function qrcode($url,$level=3,$size=4){
	Vendor('phpqrcode.phpqrcode');
	$errorCorrectionLevel =intval($level) ;//容错级别 
	$matrixPointSize = intval($size);//生成图片大小 
		//生成二维码图片 
	//echo $_SERVER['REQUEST_URI'];
	$object = new \QRcode();
	
	$date = date('Y-m-d');
	$path = "Uploads/qrcode/".$date.'/';
	if (!file_exists($path)) {
        mkdir ("$path", 0777, true);
	}
	$name = time().'_'.mt_rand();
    //生成的文件名
    $fileName = $path.$name.'.png';
	$res = $object->png($url, $fileName, $errorCorrectionLevel, $matrixPointSize, 2); 
	return $fileName;  
 }

/**
 * 获取微信操作对象
 * @staticvar array $wechat
 * @param type $type
 * @return WechatReceive
 */
function & load_wechat($type = '') {
    
	!class_exists('Wechat\Loader', FALSE) && Vendor('Wechat.Loader');
	static $wechat = array();
	$index = md5(strtolower($type));
	if (!isset($wechat[$index])) {
		// 从数据库查询配置参数
		$res = C('WEIXINPAY_CONFIG');
		$config['appid'] = C("WEIXIN_APPID");
		$config['appsecret'] =  C("WEIXIN_APPSECRET");
		
		$config['encodingaeskey'] = '';
		$config['mch_id'] = $res['MCHID'];
		$config['partnerkey'] = $res['KEY'];
		$config['ssl_cer'] = '';
		$config['ssl_key'] = '';
		$config['cachepath'] = '';
		
		// 设置SDK的缓存路径
		$config['cachepath'] = CACHE_PATH . 'Data/';
		$wechat[$index] = &\Wechat\Loader::get_instance($type, $config);
	}
	return $wechat[$index];
}

//六合彩数据格式化 20180503 add by gison
function lhc_format($caiji){
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
	$sebo = array(
		'1'=>'红波','2'=>'红波','7'=>'红波','8'=>'红波','12'=>'红波','13'=>'红波','18'=>'红波','19'=>'红波','23'=>'红波','24'=>'红波','29'=>'红波','30'=>'红波','34'=>'红波','35'=>'红波','40'=>'红波','45'=>'红波','46'=>'红波',
		'3'=>'蓝波','4'=>'蓝波','9'=>'蓝波','10'=>'蓝波','14'=>'蓝波','15'=>'蓝波','20'=>'蓝波','25'=>'蓝波','26'=>'蓝波','31'=>'蓝波','36'=>'蓝波','37'=>'蓝波','41'=>'蓝波','42'=>'蓝波','47'=>'蓝波','48'=>'蓝波',
		'5'=>'绿波','6'=>'绿波','11'=>'绿波','16'=>'绿波','17'=>'绿波','21'=>'绿波','22'=>'绿波','27'=>'绿波','28'=>'绿波','32'=>'绿波','33'=>'绿波','38'=>'绿波','39'=>'绿波','43'=>'绿波','44'=>'绿波','49'=>'绿波'
	);
	/*$url='https://1680660.com/smallSix/findSmallSixInfo.do';
	$res = file_get_contents($url);
	$jsons = json_decode($res, true);
	$data = $jsons['result']['data'];*/

	/*测试开始*/

	$time = strtotime($caiji['awardtime']);
	$w=date("w",$time);
	if($w==6){
		$time = $time + 24*60*60*3;
	}else{
		$time = $time + 24*60*60*2;
	}
	
	$data = array();
	$data['drawIssue'] = $caiji['periodnumber']+1;
	$data['drawTime'] = date('Y-m-d H:i:s', $time);
	$data['preDrawCode'] = $caiji['awardnumbers'];
	$data['preDrawIssue'] = $caiji['periodnumber']; 
	$data['preDrawTime'] = $caiji['awardtime'];
	/*测试结束*/

	$code = explode(',',$data['preDrawCode']);
	$opentm = '';
	$opentm.=array_sum($code).',';
	$opentm.=array_sum($code)>=175 ? '大,' : '小,';
	$opentm.=array_sum($code) % 2 == 0 ? '双,' : '单,';
	$opentm.=$code[6]<=24 ? '小,' : '大,';
	$opentm.=$code[6] % 2==0 ? '双,' : '单,';
	if($code[6]==49){
		$opentm.='和,';
	}else{
		$opentm.=array_sum(str_split($code[6]))>=7 ? '合大,' : '合小,';	
	}
	if($code[6]==49){
		$opentm.='和,';
	}else{
		$opentm.=array_sum(str_split($code[6]))%2==0 ? '合双,' : '合单,';	
	}

	if($code[6]==49){
		$opentm.='和';	
	}else{
		$opentm.=end(str_split($code[6]))>=5 ? '尾大' : '尾小';
	}
	$json['success'] = 1;
	/*测试开始*/

	/*测试结束*/
	$json['info'] = array(
						'BetIndex'=>$data['drawIssue'],
						'BetTime'=>strtotime($data['drawTime'])-time(), //应该减点
						'Game'=>'six',
						'Name'=>'六合彩',
						'OpenDateTime'=>date('m.d', strtotime($data['preDrawTime'])).' 21:35',
						'OpenIndex'=>$data['preDrawIssue'],
						'OpenLh' =>$shengxiao[$code[0]].','.$shengxiao[$code[1]].','.$shengxiao[$code[2]].','.$shengxiao[$code[3]].','.$shengxiao[$code[4]].','.$shengxiao[$code[5]].','.$shengxiao[$code[6]],
						'OpenNumber' =>$data['preDrawCode'],
						'OpenTime'=>strtotime($data['drawTime'])-time(),
						'OpenTm'=>$opentm,
						'ServerTime'=>(int)time().'000'
					);
	return json_encode($json); 
}

//六合彩数据格式化
function lhc_format2($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];
	$time = strtotime($awardTime);
	$w=date("w",$time);
	if($w==6){
		$time = $time + 24*60*60*3;
	}else{
		$time = $time + 24*60*60*2;
	}
	$n_awardTime = $time;

	$pkdata = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => "lhc"
	);
	return json_encode($pkdata); 
}

//北京赛车数据格式化
function 幸运飞艇_format($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];
	$time = strtotime($awardTime);
	$n_awardTime = strtotime($awardTime) + 75;

	$pkdata = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() < 0 ? 0 : $n_awardTime - time()
			//'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => $caiji['game']
	);
	return json_encode($pkdata); 
}

//极速赛车数据格式化
function er75sc_format($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];
	if (strtotime($awardTime) > strtotime("04:00:00") && strtotime($awardTime) < strtotime("07:25:00")) {
		
		$time = strtotime($awardTime) + 3.5*60*60;
		$n_awardTime = strtotime(date('Y-m-d H:i:s',$time));
	} else {
		$n_awardTime = strtotime($awardTime) + 75;
	}

	$pkdata = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => $caiji['game']
	);
	return json_encode($pkdata); 
}

//幸运飞艇数据格式化
function xyft_format($caiji){
	$periodNumber = intval(substr($caiji['periodnumber'],2,11));
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];

	if (strtotime($awardTime) > strtotime("04:03:00") && strtotime($awardTime) < strtotime("13:03:00")) {
		$time = strtotime($awardTime) + 9*60*60-300;
		$n_awardTime = strtotime(date('Y-m-d H:i:s',$time));
	} else {
		$n_awardTime = strtotime($awardTime) + 300;
	}

	$pkdata = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => "xyft"
	);
	return json_encode($pkdata); 
}

//时时彩数据格式化
function ssc_format($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];
	$time = time();
	
	if (strtotime($awardTime) > strtotime("01:55:00") && strtotime($awardTime) < strtotime("09:58:00")) {
		$n_awardTime = strtotime($awardTime) + 8*60*60;
	} else if(strtotime($awardTime) > strtotime("09:59:00") && strtotime($awardTime) < strtotime("21:57:00")){
		$n_awardTime = strtotime($awardTime) + 590;
	} else {
		$n_awardTime = strtotime($awardTime) + 290;
	}

	if (strtotime($awardTime) > strtotime("00:00:00") && strtotime($awardTime) < strtotime("00:03:00")) {
		$next_periodNumber = date('Ymd',time()).'001';
	} else {
		$next_periodNumber =  $periodNumber+1;
	}

	$pkdata = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $next_periodNumber,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => "ssc"
	);
	return json_encode($pkdata); 
}


//北京赛车数据格式化
function bj28_format($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];
	//if (strtotime($awardTime) < strtotime("23:55:00") && strtotime($awardTime) > strtotime("09:00:00")) {
		$n_awardTime = strtotime($awardTime) + 300;
	/* } else {
		$time = strtotime($awardTime) + 9*60*60;
		$n_awardTime = strtotime(date('Y-m-d H:i:s',$time));
	} */

	$bj28data = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => "bj28"
	);
	return json_encode($bj28data); 
}

function xjp28_format($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];
	$n_awardTime = strtotime($awardTime) + 300;

	$xjp28data = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => "xjp28"
	);
	return json_encode($xjp28data); 
}

//北京赛车数据格式化
function jnd28_format($caiji){
	$periodNumber = $caiji['periodnumber'];
	$awardTime = $caiji['awardtime'];
	$awardNumbers = $caiji['awardnumbers'];

	// if (strtotime($awardTime) > strtotime("19:01:00") && strtotime($awardTime) < strtotime("21:00:00")) {
		// $time = strtotime($awardTime) + 2*60*60;
		// $n_awardTime = strtotime(date('Y-m-d H:i:s',$time));
	// } else {
		$n_awardTime = strtotime($awardTime) + 180;
	//}
	
	$bj28data = array(
		'time' =>  time(),
		'current' => array(
			'periodNumber' => $periodNumber,
			'awardTime' => $awardTime,
			'awardNumbers' => $awardNumbers
		),
		'next' => array(
			'periodNumber' => $periodNumber+1,
			'awardTime' => date('Y-m-d H:i:s',$n_awardTime),
			'awardTimeInterval' => abs($n_awardTime - time())*1000,
			'delayTimeInterval' => $n_awardTime - time() > 0 ? 0 : time() - $n_awardTime
		),
		'game' => "jnd28"
	);
	return json_encode($bj28data); 
}



function ssc_qzh($v){
	sort($v);
	if ($v[0] == $v[1] && $v[1] == $v[2] && $v[0] == $v[2]) {
		return '豹子';
	}

	$im_v= implode('', $v);
	if (($v[0]+1 == $v[1] && $v[1]+1==$v[2]) || $im_v == '089' || $im_v == '019') {
		return '顺子';
	}

	
	if ($v[0] == $v[1] || $v[1] == $v[2] || $v[0] == $v[2]) {
		return '对子';
	}

	
	if ($v[0]+1 == $v[1] || $v[1]+1==$v[2] || ($v[0] == 0 && $v[2] == 9)) {
		return '半顺';
	}

	if ($v[0] != $v[1] && $v[1] != $v[2] && $v[0] != $v[2]) {
		return '杂六';
	}
}


function bj28_qzh($v){
	sort($v);
	if ($v[0] == $v[1] && $v[1] == $v[2] && $v[0] == $v[2]) {
		return '豹子';
	} 
	if(($v[0]+1 == $v[1] && $v[1]+1==$v[2])) {
		return '顺子';
	}
	 
	if ($v[0] == $v[1] || $v[1] == $v[2] || $v[0] == $v[2]) {
		return '对子';
	}

	if ($v[0]+1 == $v[1] || $v[1]+1==$v[2]) {
		return '半顺';
	}

	if ($v[0] != $v[1] && $v[1] != $v[2] && $v[0] != $v[2]) {
		return '杂六';
	}
	return '';
}




?>