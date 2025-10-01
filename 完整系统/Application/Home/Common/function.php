<?php

/**
 * 修改config的函数
 * @param $arr1 配置前缀
 * @param $arr2 数据变量
 * @return bool 返回状态
 */
function setconfig($pat, $rep)
{
	/**
	 * 原理就是 打开config配置文件 然后使用正则查找替换 然后在保存文件.
	 * 传递的参数为2个数组 前面的为配置 后面的为数值.  正则的匹配为单引号  如果你的是分号 请自行修改为分号
	 * $pat[0] = 参数前缀;  例:   default_return_type
	   $rep[0] = 要替换的内容;    例:  json
	 */
	$fileurl = APP_PATH . "/Home/Conf/config.php";
	$string = file_get_contents($fileurl); //加载配置文件
	$fp = fopen($fileurl, "r+");
  
	
	
	
	if (flock($fp, LOCK_EX)) {  // 进行排它型锁定  
		
		$pats[0] = '/\'' . $pat . '\'(.*?),/';
		$reps[0] = "'". $pat. "'". "=>" . "'".$rep ."',";
		
		
		//$string = fread($fp,filesize($fileurl));
		//$string = fpassthru($fp);
		$string = preg_replace($pats, $reps, $string); // 正则查找然后替换
		//echo $string;
	
		fwrite($fp,$string); // 写入配置文件
		
		flock($fp, LOCK_UN);    // 释放锁定  
	} else {  
		echo "文件正在被其他程序占用";  
	}  
	   
	
    fclose($fp);
	return true;
	
}

 function auth_check($auth_code,$host){
 	 return array('game'=>'幸运飞艇,er75sc,xyft,ssc,bj28,jnd28');
	
}


/*
 * 竞猜格式检测
 * */
function check_format($message,$min_point,$max_point,$xz_open,$xz_max) {
	$data['error'] = 1;
	//车号大小单双(20-20,000)
	// 12345双100 = 1~5车道买双各$100 = 总$500
	//1双100
	if ($xz_open['dxds'] && preg_match('/^(?!\d*?(\d)\d*?\1)\d{1,10}+(大|双|小|单){1}+\d+$/', $message)) {
		$ex_info = explode('大', $message);
		if (count($ex_info) > 1) {
			$info = $ex_info;
		}
		$ex_info = explode('小', $message);
		if (count($ex_info) > 1) {
			$info = $ex_info;
		}
		$ex_info = explode('单', $message);
		if (count($ex_info) > 1) {
			$info = $ex_info;
		}
		$ex_info = explode('双', $message);
		if (count($ex_info) > 1) {
			$info = $ex_info;
		}
		
		// $info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['dxds']){
			$data['points'] = $info[1]*strlen($info[0]);
			$data['type'] = 1;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dxds'];
		}
	}

	if ($xz_open['dxds'] && preg_match('/^(大|双|小|单){1}+\d+$/', $message)) {
		// $info = explode('/', $message);
		$info = substr($message, 3);
		if($info>=$min_point && $info<=$xz_max['dxds']){
			$data['points'] = $info;
			$data['type'] = 1;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dxds'];
		}
	}

	//车号(20-20,000)
	// 12345/89/20 = 1~5车道的8号、9号各买$20 = 总$200
	if ($xz_open['chehao'] && preg_match('/^(?!\d*?(\d)\d*?\1)\d{1,10}+\/{1}+(?!\d*?(\d)\d*?\1)\d{1,10}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['chehao']){
			$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0])*strlen($info[1]);
			$data['type'] = 2;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['chehao'];
		}
	}

	if ($xz_open['chehao'] && preg_match('/^(?!\d*?(\d)\d*?\1)\d{1,10}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['chehao']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1]*strlen($info[0]);
			$data['type'] = 2;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['chehao'];
		}
	}

	//组合(20-10,000)
	// 890/大单/50 = 8.9.10车道大单各买$50 = 总$150
	if ($xz_open['zuhe_1'] && preg_match('/^(?!\d*?(\d)\d*?\1)\d{1,10}+\/{1}+(大单|小双|小单|大双){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['zuhe']){
			$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0]);
			$data['type'] = 3;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['zuhe'];
		}
	}

	//龙虎(20-20,000)
	// 123/龙/100 = 1~3车道买龙各$100=总$300
	if ($xz_open['lh'] && preg_match('/^(?![1-5]*?([1-5])[1-5]*?\1)[1-5]{1,5}+\/{1}+(龙|虎){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['lh']){
			$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0]);
			$data['type'] = 4;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['lh'];
		}
	}

	//冠亚庄闲(20-20,000)
	// 庄/200 = 冠军大于亚军即中奖
	if ($xz_open['zx'] && preg_match('/^(庄|闲){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['zx']){
			$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1];
			$data['type'] = 5;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['zx'];
		}
	}

	//冠亚号码(20-5,000)
	// 组/5-6/50 = 5号.6号车在冠亚军(顺序不限) = 总$50
	// 组/1-9.3-7/100 = 1.9号车或3.7号车在冠亚军(顺序不限) = 总$200
	if ($xz_open['gy'] && preg_match('/^组\/{1}+([0-9]{1}-[0-9]{1}\.)*([0-9]{1}-[0-9]{1})+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['gy']){
			if (strlen($info[1]) > 3) {
				$info2 = explode('.', $info[1]);
				for ($i = 0; $i < count($info2); $i++) {
					$info3[$i] = explode('-', $info2[$i]);
					if ($info3[$i][0] == $info3[$i][1]) {
						$res = 0;
						return false;
					} else {
						$res = 1;
					}
					for ($a = 0; $a < $i - 1; $a++) {
						if ($info2[$i] == $info2[$a]) {
							$res = 0;
							return false;
						} else {
							$res = 1;
						}
						$info3 = explode('-', $info2[$a]);
						$info4 = $info3[1] . '-' . $info3[0];
						if ($info2[$i] == $info4) {
							$res = 0;
							return false;
						} else {
							$res = 1;
						}
					}
				}
				if ($res == 1) {
					$data['start'] = serialize($info2);
					$data['points'] = $info[2]*count($info2);
					$data['type'] = 6;
				}
			} else {
				$info1 = explode('-', $info[1]);
				if ($info1[0] != $info1[1]) {
					$data['start'] = serialize(array('0' => $info[1]));
					$data['points'] = $info[2];
					$data['type'] = 6;
				}
			}
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['gy'];
		}	
	}

	//特码大小单双(20-20,000)
	// 和双100 = 「冠亚和」的双$100
	if ($xz_open['tema'] && preg_match('/^(和|特){1}(大|小|单|双){1}+\d+$/', $message)) {
		$info = substr($message, 6);
		if($info>=$min_point && $info<=$xz_max['tema']){
			$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info;
			$data['type'] = 7;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tema'];
		}
	}

	//特码数字
	// 3.4.18.19，含本42倍，限额20-1,000
	// 5.6.16.17，含本21倍，限额20-2,000
	// 7.8.14.15，含本14倍，限额20-3,000
	// 9.10.12.13，含本10倍，限额20-4,000
	// 11，含本8倍，限额20-5,000
	// 和567/100 = 竞猜「冠亚和」的值为5或6或7各$100 = 总$300
	// 和341819/100
	if ($xz_open['tema_sz_1'] && preg_match('/^(和|特){1}(([3-9]{1}|1[0-9]{1}){1})*+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);

		$start = substr($info[0], 3);
		if($info[1]>=$min_point && $info[1]<=$xz_max['tema_sz']){
			$num_str = fv_split($start);
			$info_str = array();
			for($a=0;$a<count($num_str);$a++){
				if ($a >= 1) {
					if ($num_str[$a-1] == '1') {
						if ( $num_str[$a-2] == '1' && $num_str[$a] == '1') {
							
						} else {
							continue;
						}
					}
				}
				if ($num_str[$a] == '1') {
					$info_str[] = $num_str[$a] . $num_str[$a+1];
				} else {
					$info_str[] = $num_str[$a];
				}
			}

			$data['points'] = $info[1]*count($info_str);
			$data['type'] = 8;

		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tema_sz'];
		}
	}

	//特码区段(20-10,000)
	//BC/100 = 投注BC段位各100＝总$200
	if ($xz_open['tema_qd_1'] && preg_match('/^(A|B|C){1,4}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['tema_qd']){
			$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1]*count(str_split($info[0]));
			$data['type'] = 9;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tema_qd'];
		}
	}
	return $data;
}

function check_format_ssc($message,$min_point,$max_point,$xz_open,$xz_max) {
	$data['error'] = 1;

	//定位球(20-20,000)
	// 12345/89/20 = 1~5求的开出8、9各买$20 = 总$200
	if ($xz_open['dwq'] && preg_match('/^(?!\d*?(\d)\d*?\1)[1-5]{1,5}+\/{1}+(?!\d*?(\d)\d*?\1)[0-9]{1,10}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if ($info[2] >= $min_point && $info[2] <= $xz_max['dwq']) {
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0])*strlen($info[1]);
			$data['type'] = 1;
		} else {
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dwq'];
		}
	}

	if ($xz_open['dwq'] && preg_match('/^(?!\d*?(\d)\d*?\1)[0-9]{1,10}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if ($info[1] >= $min_point && $info[1] <= $xz_max['dwq']) {
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1]*5*strlen($info[0]);
			$data['type'] = 1;
		} else {
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dwq'];
		}
	}

	//龙虎合(20-20,000)
	if ($xz_open['lhh'] && preg_match('/^(龙|虎){1}+\d+$/', $message)) {
		$info = substr($message, 3);
		if ($info>=$min_point && $info<=$xz_max['lhh']) {
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info;
			$data['type'] = 2;
		} else {
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['lhh'];
		}
	}else if ($xz_open['lhh'] && preg_match('/^(合){1}+\d+$/', $message)) {
		$info = substr($message, 3);
		if ($info>=$min_point && $info<=$xz_max['lhh_he']) {
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info;
			$data['type'] = 8;
		} else {
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['lhh_he'];
		}
	}

	//定位大小单双(20-20,000)
	// 12345/双/100 = 1~5车道买双各$100 = 总$500
	if ($xz_open['dxds'] && preg_match('/^(?!\d*?(\d)\d*?\1)[1-5]{1,5}+\/{1}+(大|双|小|单){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['dxds']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0]);
			$data['type'] = 3;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dxds'];
		}
	}

	//总大小单双
	if ($xz_open['zdxds'] && preg_match('/^(大|小|单|双){1}+\d+$/', $message)) {
		$info = substr($message, 3);
		if($info>=$min_point && $info<=$xz_max['zdxds']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info;
			$data['type'] = 4;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['zdxds'];
		}
	}

	//前中后豹子顺子对子半顺杂六
	if ($xz_open['qzh'] && preg_match('/^(前|中|后){1}(豹子|顺子|对子|半顺|杂六){1}+\d+$/', $message)) {
		$info = substr($message, 9);
		if($info>=$min_point && $info<=$xz_max['qzh']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info;
			$data['type'] = 5;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['qzh'];
		}
	}



	//特码区段(20-10,000)
	//BC/100 = 投注BC段位各100＝总$200
	if ($xz_open['tema_qd'] && preg_match('/^(A|B|C){1,3}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['tema_qd']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1]*count(str_split($info[0]));
			$data['type'] = 6;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tema_qd'];
		}
	}

	//二字定位 13/34.56/100
	if ($xz_open['he'] && preg_match('/^([1-5]{1}[1-5]{1})+\/{1}+([0-9]{1,10}\.[0-9]{1,10})+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		$ws = explode('.', $info[1]);
		

		if($info[2]>=$min_point && $info[2]<=$xz_max['he']){
			$data['points'] = count(str_split($ws[0]))*count(str_split($ws[1]))*$info[2];
			$data['type'] = 7;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['he'];
		}
	}
	return $data;
}

function check_format_bj28($message,$min_point,$max_point,$xz_open,$xz_max) {
	$data['error'] = 1;

	
	//大小单双  大100  小100
	if ($xz_open['dxds'] && preg_match('/^(大|小|单|双){1}+\d+$/', $message)) {
		$info = substr($message, 3);
		if($info>=$min_point && $info<=$xz_max['dxds']){
			$data['points'] = $info;
			$data['type'] = 1;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dxds'];
		}
	}

	//组合(20-10,000)
	// 大单100 = 大单各买$100 
	if ($xz_open['zuhe_1'] && preg_match('/^(大单|小双|小单|大双){1}+\d+$/', $message)) {
		
		$info = substr($message, 6);
		if($info>=$min_point && $info<=$xz_max['zuhe']){
			$data['points'] = $info;
			$data['type'] = 2;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['zuhe'];
		}
	}

	// 极大100 
	if ($xz_open['jdx'] && preg_match('/^(极大|极小){1}+\d+$/', $message)) {
		$info = substr($message, 6);
		if($info>=$min_point && $info<=$xz_max['jdx']){
			$data['points'] = $info;
			$data['type'] = 3;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['jdx'];
		}
	}

	// 庄闲和  庄100 
	if ($xz_open['zx'] && preg_match('/^(庄|闲|和){1}+\d+$/', $message)) {
		$info = substr($message, 3);
		if($info>=$min_point && $info<=$xz_max['zx']){
			$data['points'] = $info;
			$data['type'] = 4;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['zx'];
		}
	}

	//豹子顺子对子   豹子100
	if ($xz_open['bds'] && preg_match('/^(豹子|顺子|对子|半顺|杂六){1}+\d+$/', $message)) {
		$info = substr($message, 6);
		if($info>=$min_point && $info<=$xz_max['bds']){
			$data['points'] = $info;
			$data['type'] = 5;
		}else{
			if ($info<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['bds'];
		}
	}

	
	// 0-27点100  ([3-9]{1}|1[0-9]{1}){1}
	if ($xz_open['tema'] && preg_match('/^(2[0-7]{1}|1[0-9]{1}|[0-9]{1}){1}+(点){1}+\d+$/', $message)) {
		$info = explode('点', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['tema']){
			$data['points'] = $info[1];
			$data['type'] = 6;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tema'];
		}
	}

	return $data;
}

function check_format_lhc($message,$min_point,$max_point,$xz_open,$xz_max) {
	$data['error'] = 1;
	//正码定位大小单双
	// 12345/双/100 = 1~5正码买双各$100 = 总$500
	if ($xz_open['dxds'] && preg_match('/^(?!\d*?(\d)\d*?\1)[1-6]{1,6}+\/{1}+(大|双|小|单){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['dxds']){
			//$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0]);
			$data['type'] = 1;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['dxds'];
		}
	}
	//特码大小单双
	//大/100
	if ($xz_open['tdxds'] && preg_match('/^(大|小|单|双){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['tdxds']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1];
			$data['type'] = 2;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tdxds'];
		}
	}
	//正码定位红绿蓝
	// 12345/红波/100 = 1~5正码买双各$100 = 总$500
	if ($xz_open['zhll'] && preg_match('/^(?!\d*?(\d)\d*?\1)[1-6]{1,6}+\/{1}+(红波|绿波|蓝波){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[2]>=$min_point && $info[2]<=$xz_max['zhll']){
			//$data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[2]*strlen($info[0]);
			$data['type'] = 3;
		}else{
			if ($info[2]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['zhll'];
		}
	}
	//特码红绿蓝
	//红波/100
	if ($xz_open['thll'] && preg_match('/^(红波|绿波|蓝波){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[1]>=$min_point && $info[1]<=$xz_max['thll']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1];
			$data['type'] = 4;
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['thll'];
		}
	}
	//特码生肖
	//鸡/100
	if ($xz_open['tsx'] && preg_match('/^(鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪){1}+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if($info[0]=='鼠'){
			$xz_max['tsx']=$xz_max['tshu'];
		}else if($info[0]=='牛'){
			$xz_max['tsx']=$xz_max['tniu'];
		}else if($info[0]=='虎'){
			$xz_max['tsx']=$xz_max['thu'];
		}else if($info[0]=='兔'){
			$xz_max['tsx']=$xz_max['ttu'];
		}else if($info[0]=='龙'){
			$xz_max['tsx']=$xz_max['tlong'];
		}else if($info[0]=='蛇'){
			$xz_max['tsx']=$xz_max['tshe'];
		}else if($info[0]=='马'){
			$xz_max['tsx']=$xz_max['tma'];
		}else if($info[0]=='羊'){
			$xz_max['tsx']=$xz_max['tyang'];
		}else if($info[0]=='猴'){
			$xz_max['tsx']=$xz_max['thou'];
		}else if($info[0]=='鸡'){
			$xz_max['tsx']=$xz_max['tji'];
		}else if($info[0]=='狗'){
			$xz_max['tsx']=$xz_max['tgou'];
		}else if($info[0]=='猪'){
			$xz_max['tsx']=$xz_max['tzhu'];
		}
		if($info[1]>=$min_point && $info[1]<=$xz_max['tsx']){
			// $data['start'] = serialize(str_split($info[0]));
			$data['points'] = $info[1];
			$data['type'] = 5;
			$data['xz_max'] = $xz_max['tsx'];
		}else{
			if ($info[1]<$min_point) {
				$data['error'] = 0;
			} else {
				$data['error'] = 2;
			}
			$data['xz_max'] = $xz_max['tsx'];
		}
	}
	//正码定位(多位) 13/34.45/100
	if ($xz_open['zhm'] && preg_match('/^(?!\d*?(\d)\d*?\1)[1-6]{1,6}+\/{1}+(([1-9]|[1-4]\d)(\.([1-9]|[1-4]\d))*)+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if (preg_match('/^(([1-9]|[1-4]\d)(\.([1-9]|[1-4]\d))*)$/', $info[1])) {
			$ws = explode('.', $info[1]);
			if($info[2]>=$min_point && $info[2]<=$xz_max['zhm']){
				//$data['points'] = count(str_split($ws[0]))*count(str_split($ws[1]))*$info[2];
				$data['points'] = count(str_split($info[0]))*count($ws)*$info[2];
				$data['type'] = 6;
			}else{
				if ($info[2]<$min_point) {
					$data['error'] = 0;
				} else {
					$data['error'] = 2;
				}
				$data['xz_max'] = $xz_max['zhm'];
			}
		}
	}
	//正码定位(单位) 13/34/100
	if ($xz_open['zhm'] && preg_match('/^(?!\d*?(\d)\d*?\1)[1-6]{1,6}+\/{1}+(([1-9]|[1-4]\d))+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if (preg_match('/^(([1-9]|[1-4]\d))$/', $info[1])) {
			if($info[2]>=$min_point && $info[2]<=$xz_max['zhm']){
				//$data['start'] = serialize(str_split($info[0]));
				$data['points'] = $info[2]*strlen($info[0]);
				$data['type'] = 7;
			}else{
				if ($info[2]<$min_point) {
					$data['error'] = 0;
				} else {
					$data['error'] = 2;
				}
				$data['xz_max'] = $xz_max['zhm'];
			}
		}
	}
	//特码定位 7/34.45/100 34.45/100 7/34/100 34/100
	if ($xz_open['thm'] && preg_match('/^(?!\d*?(\d)\d*?\1)[7]{0,1}+\/{0,1}+(([1-9]|[1-4]\d)(\.([1-9]|[1-4]\d))*)+\/{1}+\d+$/', $message)) {
		$info = explode('/', $message);
		if (count($info) == 3) {// 7/34.45/100 7/34/100
			$zu = explode('.', $info[1]);
			if(count($zu) >1){ //7/34.45/100
				if (preg_match('/^(([1-9]|[1-4]\d)(\.([1-9]|[1-4]\d))*)$/', $info[1])) {
					$ws = explode('.', $info[1]);
					if($info[2]>=$min_point && $info[2]<=$xz_max['thm']){
						$data['points'] = count($ws)*$info[2];
						$data['type'] = 8;
					}else{
						if ($info[2]<$min_point) {
							$data['error'] = 0;
						} else {
							$data['error'] = 2;
						}
						$data['xz_max'] = $xz_max['thm'];
					}
				}
			}else{//7/34/100
				if (preg_match('/^(([1-9]|[1-4]\d)(\.([1-9]|[1-4]\d))*)$/', $info[1])) {
					if($info[2]>=$min_point && $info[2]<=$xz_max['thm']){
						//$data['start'] = serialize(str_split($info[0]));
						$data['points'] = $info[2];
						$data['type'] = 8;
					}else{
						if ($info[2]<$min_point) {
							$data['error'] = 0;
						} else {
							$data['error'] = 2;
						}
						$data['xz_max'] = $xz_max['thm'];
					}
				}
			}
			
		}else{
			$zu = explode('.', $info[0]);
			if(count($zu) >1){ //34.45/100
				if (preg_match('/^(([1-9]|[1-4]\d)(\.([1-9]|[1-4]\d))*)$/', $info[0])) {
					$ws = explode('.', $info[0]);
					if($info[1]>=$min_point && $info[1]<=$xz_max['thm']){
						$data['points'] = count($ws)*$info[1];
						$data['type'] = 8;
					}else{
						if ($info[1]<$min_point) {
							$data['error'] = 0;
						} else {
							$data['error'] = 2;
						}
						$data['xz_max'] = $xz_max['thm'];
					}
				}
			}else{//34/100
				if (preg_match('/^(([1-9]|[1-4]\d))$/', $info[0])) {
					if($info[1]>=$min_point && $info[1]<=$xz_max['thm']){
						//$data['start'] = serialize(str_split($info[0]));
						$data['points'] = $info[1];
						$data['type'] = 8;
					}else{
						if ($info[1]<$min_point) {
							$data['error'] = 0;
						} else {
							$data['error'] = 2;
						}
						$data['xz_max'] = $xz_max['thm'];
					}
				}
			}
		}
	}
	return $data;
}

/*和值分割*/
function fv_split($str) {
	$arr = str_split($str);
	$new = array();
	$ii_1_b = true;
	$ii_1 = '';
	foreach ($arr as $ii) {
		if (!$ii_1_b && $ii_1 == "1") $ii = "1" . $ii;
		$ii_1 = $ii;
		if ($ii_1_b) $ii_1_b = false;
		if ($ii == "1") continue;
		array_push($new, $ii);
	}
	return $new;
}
?>