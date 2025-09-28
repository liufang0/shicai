<?php
namespace Home\Controller;

use Think\Controller;

class ApiController extends Controller
{

    private $typearr;

    private $apiarr;

    public function __construct()
    {
        parent::__construct();
    }

    public function _initialize()
    {
        getConfigs();
        $this->typearr = array(
            /* 0 => 'pk10',
			1 => 'er75sc',
            2 => 'xyft',
            3 => 'ssc', */
            4 => 'bj28',
            5 => 'jnd28'
        );
		
		
        $this->apiarr = array(
           /*  0 => 'http://api.api861861.com/pks/getPksHistoryList.do?date=&lotCode=10037',
			1 => 'http://api.cpkjapi.com/json?t=jisussc&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
            2 => 'http://api.api68.com/pks/getPksHistoryList.do?lotCode=10057',
            3 => 'http://api.api861861.com/CQShiCai/getBaseCQShiCaiList.do?lotCode=10060', */
            4 => '',
            5 => ''
        );
        // http://e.apiplus.net/newly.do?token=t92f294b93daf64d3k&code=bjkl8&format=json
    }

    public function testOpenInterval()
    {
        foreach ($this->typearr as $k => $v) {
            
            $json_ori = http_get($this->apiarr[$k]);
            $json = json_decode($json_ori, 1);
            
            echo "<pre>";
            // var_dump($json);
            $openInterval = $this->getOpenInterval($json);
            echo $v."---".$openInterval."</br>";
        }
    }

    public function testjnd28()
    {
        $json_ori = http_get('http://api1.jiutoumao.cn/Home/Lottery/index/token/d35b6b8cd98c74ee94efbc6898fbaf32/code/PCDD/format/xml/num/5/');
        $json = json_decode($json_ori, 1);
        echo '期号:' . $json[data][0][expect] . "<br>";
        $bj28 = explode(',', $json[data][0][opencode]);
        sort($bj28);
        // die_dump($bj28);
        
        $numberOne = ($bj28[1] + $bj28[4] + $bj28[7] + $bj28[10] + $bj28[13] + $bj28[16]) % 10;
        echo ($bj28[1] + $bj28[4] + $bj28[7] + $bj28[10] + $bj28[13] + $bj28[16]) . "<br>";
        $numberTwo = ($bj28[2] + $bj28[5] + $bj28[8] + $bj28[11] + $bj28[14] + $bj28[17]) % 10;
        $numberThree = ($bj28[3] + $bj28[6] + $bj28[9] + $bj28[12] + $bj28[15] + $bj28[18]) % 10;
        echo $numberOne . "+" . $numberTwo . "+" . $numberThree . "=" . ($numberOne + $numberTwo + $numberThree);
        
        // die_dump($bj28);
    }

    public function test01()
    {
        $json_ori = http_get('http://api1.jiutoumao.cn/Home/Lottery/index/token/d35b6b8cd98c74ee94efbc6898fbaf32/code/PCDD/format/xml/num/5/');
        $json = json_decode($json_ori, 1);
        echo '期号:' . $json[data][0][expect] . "<br>";
        $bj28 = explode(',', $json[data][0][opencode]);
        sort($bj28);
        // die_dump($bj28);
        
        $numberOne = ($bj28[0] + $bj28[1] + $bj28[2] + $bj28[3] + $bj28[4] + $bj28[5]) % 10;
        echo ($bj28[0] + $bj28[1] + $bj28[2] + $bj28[3] + $bj28[4] + $bj28[5]) . "<br>";
        $numberTwo = ($bj28[6] + $bj28[7] + $bj28[8] + $bj28[9] + $bj28[10] + $bj28[11]) % 10;
        $numberThree = ($bj28[12] + $bj28[13] + $bj28[14] + $bj28[15] + $bj28[16] + $bj28[17]) % 10;
        echo $numberOne . "+" . $numberTwo . "+" . $numberThree . "=" . ($numberOne + $numberTwo + $numberThree);
        
        // die_dump($bj28);
    }

    public function index()
    {
        set_time_limit(0);
		
		//$open_model = M("caiji02");
		$caiji_model = M("caiji");
		$caiji_admin = M("caiji_admin");
		$game_time = M("time");
		
		foreach ($this->typearr as $k => $v) {
			
			if($this->isCaijiTime($v)){
				// 北京赛车
				$currentAward = $caiji_model->where('game="' . $v . '"')
					->order('next_term desc')
					->find();
				
				//var_dump($currentAward);
				$now_time = date("Y-m-d H:i:s",strtotime('now'));
				echo $v."============================== \n";
				echo "now time:$now_time \n";
				
				if ((strtotime('now') >= $currentAward[next_time] || empty($currentAward)) && $v == 'bj28' || $v == 'jnd28' ) {
					
					$code = $this->typearr[$k];
					echo "start update $code \n";
					if($v=='jnd28' || $v=='bj28'){
						
						if($v=='bj28'){
							$time=date("Ymd",time());
							
							$d_admin=$caiji_admin->where(array('addtime'=>$time,'game'=>$v))->find();

							if(!$d_admin){
								$time_arr=$game_time->where(array('game'=>$v))->select();
								
								foreach($time_arr as $val){
									$return['periodnumber']=date("ymd").str_pad($val['actionno'],3,"0",STR_PAD_LEFT);
									$return['awardnumbers']=rand(0,9).','.rand(0,9).','.rand(0,9);
									$return['game']=$v;
									$return['addtime']=$time;
									
									$sql = $caiji_admin->add($return);
									
								}
							}
						}
						
						if($v=='jnd28'){
							$time=date("Ymd",time());
							
							$d_admin=$caiji_admin->where(array('addtime'=>$time,'game'=>$v))->find();

							if(!$d_admin){
								$time_arr=$game_time->where(array('game'=>$v))->select();
								
								foreach($time_arr as $val){
									$return['periodnumber']="1".date("md").str_pad($val['actionno'],3,"0",STR_PAD_LEFT);
									$return['awardnumbers']=rand(0,9).','.rand(0,9).','.rand(0,9);
									$return['game']=$v;
									$return['addtime']=$time;
									$sql = $caiji_admin->add($return);
									
								}
							}
						}
						
						$j=$game_time->where('game="' . $v . '" and actionTime < "' . date("H:i:s",time()) . '"')->order('actionNo desc')->find();
						
						
						if($v=='bj28'){
							$json['preDrawIssue']=date("ymd").str_pad($j['actionno'],3,"0",STR_PAD_LEFT);
							$json['preDrawTime']=date("Y-m-d ").$j['actiontime'];
							if($j['actionno'] + 1 > 288){
								$json['next_term']=date("ymd",time()+86400).str_pad(1,3,"0",STR_PAD_LEFT);
							}else{
								$json['next_term']=date("ymd").str_pad($j['actionno'],3,"0",STR_PAD_LEFT);
								
							}
						}
						if($v=='jnd28'){
							$json['preDrawIssue']="1".date("md").str_pad($j['actionno'],3,"0",STR_PAD_LEFT);
							$json['preDrawTime']=date("Y-m-d ").$j['actiontime'];
							if($j['actionno'] + 1 > 480){
								$json['next_term']="1".date("md",time()+86400).str_pad(1,3,"0",STR_PAD_LEFT);
							}else{
								$json['next_term']="1".date("md").str_pad($j['actionno'],3,"0",STR_PAD_LEFT);
								
							}
						}
						
					}else{
						$json_ori = http_get($this->apiarr[$k]);
						$json = json_decode($json_ori, 1)['result']['data'][0];
					}
					//print_r($json);
					
					//echo "<pre>";
					//echo ($this->apiarr[$k]);
					$openInterval = $this->getOpenInterval($code);
					
					$currentAwardWhere[game] = $v;
					$currentAwardWhere[periodnumber] = $json['preDrawIssue'];
					$currentAward = $caiji_model->where($currentAwardWhere)->find();
					//die_dump($currentAward);
					//var_dump($currentAward);
					if (! empty($json) && empty($currentAward)) {
						
						$map['game'] = $v;
						$map['periodnumber'] = $json['preDrawIssue'];
						$yushe = $caiji_admin->where($map)->find();
						
						if($yushe){
							$code = $yushe['awardnumbers'];
						}else{
							$code = join(',',array(rand(0,9),rand(0,9),rand(0,9)));
						}
						
						$data = array(
							'game' => $v,
							'periodnumber' => $json['preDrawIssue'],
							'awardnumbers' => $code,
							'awardtime' => $json['preDrawTime'],
							'addtime' => strtotime('now'),
							'next_term' => $json['next_term']+1,
							'next_time' => ($openInterval)
						);
						//$open_model->add($data);strtotime($json['preDrawTime']) + 
						$flag = $caiji_model->add($data);
						var_dump($flag);
						//echo "update $code success!\n";
					} else {
						echo "expect {$currentAward[periodnumber]} is exist or json is null; next time:" . date("Y-m-d H:i:s", $currentAward[next_time]) . "\n";
					}
				}else if ((strtotime('now') >= $currentAward[next_time] || empty($currentAward)) && $v == 'xyft') {
					$code = $this->typearr[$k];
					echo "start update $code \n";
					
					$json_ori = http_get($this->apiarr[$k]);
					$json = json_decode($json_ori, 1)['result']['data'][0];
					
					$arrs = ['01','02','03','04','05','06','07','08','09','10'];
					shuffle($arrs);
					
					//echo "<pre>";
					//echo ($this->apiarr[$k]);
					$openInterval = $this->getOpenInterval($code);
					
					$currentAwardWhere[game] = $v;
					$currentAwardWhere[periodnumber] = $json['preDrawIssue'];
					$currentAward = $caiji_model->where($currentAwardWhere)->find();
					//die_dump($currentAward);
					//var_dump($currentAward);
					if (! empty($json) && empty($currentAward)) {
						
						$map['game'] = $v;
						$map['periodnumber'] = $json['preDrawIssue'];
						$yushe = $caiji_admin->where($map)->find();
						
						if($yushe){
							$code = $yushe['awardnumbers'];
						}else{
							$code =  join(',',$arrs);
						}
						
						$data = array(
							'game' => $v,
							'periodnumber' => $json['preDrawIssue'],
							'awardnumbers' =>$code,
							'awardtime' => $json['preDrawTime'],
							'addtime' => strtotime('now'),
							'next_term' => ($json['preDrawIssue'] + 1),
							'next_time' => ( $openInterval)
						);
						//$open_model->add($data);strtotime($json['preDrawTime']) +
						$flag = $caiji_model->add($data);
						var_dump($flag);
						//echo "update $code success!\n";
					} else {
						echo "expect {$currentAward[periodnumber]} is exist or json is null; next time:" . date("Y-m-d H:i:s", $currentAward[next_time]) . "\n";
					}
				}else if ((strtotime('now') >= $currentAward[next_time] || empty($currentAward)) && $v == 'jnd28') {
					$code = $this->typearr[$k];
					echo "start update $code \n";
					
					$json_ori = http_get($this->apiarr[$k]);
					$json = json_decode($json_ori, 1)['data'][0];
					
					//var_dump($json);die;
					
					//echo "<pre>";
					//echo ($this->apiarr[$k]);
					$openInterval = $this->getOpenInterval($code);
					
					$currentAwardWhere[game] = $v;
					$currentAwardWhere[periodnumber] = $json['issue'];
					$currentAward = $caiji_model->where($currentAwardWhere)->find();
					//die_dump($currentAward);
					//var_dump($currentAward);
					if (! empty($json) && empty($currentAward)) {
						
						$map['game'] = $v;
						$map['periodnumber'] = $json['issue'];
						$yushe = $caiji_admin->where($map)->find();
						
						if($yushe){
							$code = $yushe['awardnumbers'];
						}else{
							$code = join(',',array(rand(0,9),rand(0,9),rand(0,9)));
						}
						
						$data = array(
							'game' => $v,
							'periodnumber' => $json['issue'],
							'awardnumbers' => $code,
							'awardtime' => $json['time'],
							'addtime' => strtotime('now'),
							'next_term' => ($json['issue'] + 1),
							'next_time' => ( $openInterval)
						);
						//$open_model->add($data);strtotime($json['time']) +
						$flag = $caiji_model->add($data);
						var_dump($flag);
						//echo "update $code success!\n";
					} else {
						echo "expect {$currentAward[periodnumber]} is exist or json is null; next time:" . date("Y-m-d H:i:s", $currentAward[next_time]) . "\n";
					}
				} else {
					echo "next time:" . date("Y-m-d H:i:s", $currentAward[next_time]) . "\n";
				}
				echo "============================== \n\n";
			}
			
		}
		//$this->webRefresh($currentAward);
		
    }
	
	public function rounds(){
		$arr[0] = rand(0,9);
		$arr[1] = rand(0,9);
		$arr[2] = rand(0,9);
		$arr = join(',',$arr);
		return $arr;
	}
	
	//是否到采集时间
	public function isCaijiTime($game){
		//echo $game;
		$beginToday=strtotime('00:00:00');
		$endToday=strtotime("23:59:59");
			
		/* if($game == "pk10"){
			$beginToday=strtotime('00:00:00');
			$endToday=strtotime("23:59:59");
		}else if($game == "bj28"){
			$beginToday=strtotime('09:04:00');
			$endToday=strtotime("23:55:00");
		}else if($game == "xyft"){
			$beginToday=strtotime('04:05:00');
			$endToday=strtotime("13:05:00");
			
		}else if($game == "er75sc"){
			$beginToday=strtotime('04:00:00');
			$endToday=strtotime("07:25:00");
			if(time()>$beginToday && time()<$endToday){
				return false;
			}else{
				return true;
			}
		}else if($game == "ssc"){
			
			$beginToday=strtotime('00:10:00');
			$endToday=strtotime("23:59:59");
		
		}else if($game == "jnd28"){
			$beginToday=strtotime('23:59:00');
			$endToday=strtotime("00:01:00");
			if(time()>$beginToday && time()<$endToday){
				return false;
			}else{
				return true;
			}
		} */
		
		if(time()>$beginToday && time()<$endToday){
			return true;
		}else{
			return false;
		}
	}

    public function getOpenInterval($game)
    {
		if($game == "bj28"){
			return 300;
		}elseif( $game == "jnd28"){
			return 180;
		}
         $allInterval = 0;
        foreach ($json[data] as $k => $v) {
            if ($k > 0) {
                $allInterval += $lastInterval - $v[opentimestamp];
            }
            $lastInterval = $v[opentimestamp];
        }
        return floor($allInterval / (count($json[data]) - 1)); 
    }

    public function site()
    {
		$url = C('siteurl');
        echo ("http://".$url);
    }
	
	public function ctrl_user_win(){
		$expect = $_GET["expect"];
		$opencode = $_GET["opencode"];
		
		$res = M('user')->where("xjp28_is_win = 1")->find();
		
		if($res){
			$win_money = $this->xjp28_jiesuan($expect,$opencode,$res['id']);
		}else{
			$win_money = 0;
		}
		
		if($win_money > 0){
			echo "true";exit;
		}else{
			echo "false";exit;
		}
	}
	
	public function ctrl_user_lost(){
		$expect = $_GET["expect"];
		$opencode = $_GET["opencode"];
		
		$res = M('user')->where("xjp28_is_lost = 1")->find();
		
		if($res){
			$win_money = $this->xjp28_jiesuan($expect,$opencode,$res['id']);
		}else{
			$win_money = 0;
		}
		
		if($win_money >= 0){
			echo "false";exit;
		}else{
			echo "true";exit;
		}
	}
	
	public function ctrl_platform_jiesuan(){
		
		//平台今日输赢
		$start=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

		$map['time'] = array(array('egt',$start),array('elt',$end),'and');
		$map['state'] = 1;
		$map['is_add'] = 1;
		$order = M('order');

		$pt_today = $order->field("SUM(add_points) AS add_points,SUM(del_points) AS del_points")->where($map)->find();
		$today_ying = $pt_today['del_points'] - $pt_today['add_points'];
		
		$expect = $_GET["expect"];
		$opencode = $_GET["opencode"];
		
		//查询需要统计的用户
		/* $userids = "";
		$userlist = M('user')->where("er75sc_sys_is_win = 1")->select();
		
		for($i=0;$i<count($userlist);$i++){
			$userids = $userids.$userlist[$i]['id'].",";
		}
		$userids = substr($userids,0,strlen($userids)-1); */
		
		$win_money = $this->xjp28_jiesuan($expect,$opencode);
		//echo $win_money;
		//echo $today_ying;
		if($win_money == 0){
			echo "false";exit;
		}
		
		$config_one = M('config_one')->where("name = 'is_ctrl_xjp28'")->field('value')->find();
		//到达亏损值开始控制
		if($today_ying+$win_money <= C('xjp28_today_ying1') || $config_one['value'] == '1'){
			if($config_one['value'] == '0'){
				M('config_one')->where("name = 'is_ctrl_xjp28'")->setField('value','1');
			}
			
			//到达盈利值后停止控制
			if($today_ying+$win_money >= C('xjp28_today_ying2')){
				M('config_one')->where("name = 'is_ctrl_xjp28'")->setField('value','0');
				echo "false";exit;
			}
			
			if($win_money > 0){
				echo "false";exit;
			}else{
				echo "true";exit;
			}
			
		}else{
			echo "false";exit;
		}
		
		
		
	}
	
	public function xjp28_jiesuan($expect,$opencode,$userids){
		//结算
		//开奖结果
		$map['awardnumbers'] = $opencode;
		$map['awardtime'] = time();
		$map['time'] = time();
		$map['periodnumber'] = $expect;
		
		$info = explode(',', $map['awardnumbers']);
		$numberOne = $info[0];
		$numberTwo = $info[1];
		$numberThree = $info[2];
		
		$map['tema'] = $numberOne+$numberTwo+$numberThree ;

		if($map['tema'] % 2 == 0){
			$map['tema_ds'] = '双';
		}
		else{
			$map['tema_ds'] = '单';
		}
	
		if($map['tema']>=14){
			$map['tema_dx'] = '大';
		}else{
			$map['tema_dx'] = '小';
		}
	
		if($numberOne>$numberTwo){
			$map['zx'] = '庄';
		} else if($numberOne == $numberTwo){
			$map['zx'] = '和';
		}else{
			$map['zx'] = '闲';
		}

		$map['q3'] = bj28_qzh(array($numberOne,$numberTwo,$numberThree));

		$map['game'] = $data['game'];
		$current_number = $map;
		//$number1 = explode(',', $current_number['awardnumbers']);
		//$tema_number = $number1[0] + $number1[1] + $number1[2];
		$tema_number = $current_number['tema'];
		if ($tema_number <= 13) {
			if ($tema_number%2 == 0) {
				$current_number['zuhe'] = '小双';
			} else {
				$current_number['zuhe'] = '小单';
			}
		} else {
			if ($tema_number%2 == 0) {
				$current_number['zuhe'] = '大双';
			} else {
				$current_number['zuhe'] = '大单';
			}
		}

		if ($tema_number >=0 && $tema_number <=5) {
			$current_number['jdx'] = '极小';
		} else if($tema_number >= 22 && $tema_number <=27) {
			$current_number['jdx'] = '极大';
		}  else {
			$current_number['jdx'] = '';
		}

		//当前局所有竞猜
		if($userids){
			$list = M('order')->where("number = {$current_number['periodnumber']} && userid in ({$userids}) && state = 1 && is_add = 0 && game='xjp28'")->order("time ASC")->select();
		}else{
			//当前局所有竞猜
			$today_time = strtotime(date('Y-m-d',time()));
			$list = M('order')->where("number = {$current_number['periodnumber']} && time > '{$today_time}' && state = 1 && is_add = 0 && game='xjp28'")->order("time ASC")->select();
		}
		if($list){
			$sum_points = 0;
			$sum_del_points = 0;
			for($i=0;$i<count($list);$i++){
				$del_points = $list[$i]['del_points'];
				$sum_del_points = $sum_del_points + $del_points;
				//分类
				switch($list[$i]['type']){
					
					//大小单双  大100  小100  
					case 1:
						$start1 = substr($list[$i]['jincai'], 0,3);
						$starts1 = substr($list[$i]['jincai'],3);
						$num1 = 0;

						if ($start1 == '大' || $start1 == '小') {
							if ($start1 == $current_number['tema_dx']) {
								$num1 = 1;
							}
						} else {
							if ($start1 == $current_number['tema_ds']) {
								$num1 = 1;
							}
						}

						if($num1>0){
							if ($current_number['tema'] == '13' || $current_number['tema'] == '14') {
								if(C('xjp28_1314_open') == '0'){
									$user_points = M('order')->field("sum(del_points) as sum_del")->where("userid = {$userid} and state=1")->find();
									if(intval($user_points['sum_del']) <= intval(C('xjp28_dxds_1314zz'))){
										$points1 = $num1*$starts1*C('xjp28_dxds_md1');
									}else if(intval($user_points['sum_del']) > intval(C('xjp28_dxds_1314zz')) && intval($user_points['sum_del']) <= intval(C('xjp28_dxds_1314zz2'))){
										$points1 = $num1*$starts1*C('xjp28_dxds_md2');
									}else if(intval($user_points['sum_del']) > intval(C('xjp28_dxds_1314zz2'))){
										$points1 = $num1*$starts1*C('xjp28_dxds_md3');
									}
								}else{
									$points1 = $num1*$starts1*C('xjp28_dxds_md');
								}
							} else {
								$points1 = $num1*$starts1*C('xjp28_dxds');
							}
							
							$sum_points = $sum_points + $points1;
						}
						break;

					//组合  大单100  小100  
					case 2:
						$start2 = substr($list[$i]['jincai'], 0,6);
						$starts2 = substr($list[$i]['jincai'],6);
						$num2 = 0;

					
						if ($start2 == $current_number['zuhe']) {
							$num2 = 1;
						}

						if($num2>0){
							if ($current_number['tema'] == '13' || $current_number['tema'] == '14') {
								$points2 = $num2*$starts2*C('xjp28_zuhe_md');
							} else {
								if ($start2 == '大单' || $start2 == '小双'){
									$points2 = $num2*$starts2*C('xjp28_zuhe_1');
								} else {
									$points2 = $num2*$starts2*C('xjp28_zuhe_2');
								}
							}
							
							$sum_points = $sum_points + $points2;
						}
						break;


					//极大小  极大100  
					case 3:
						$start3 = substr($list[$i]['jincai'], 0,6);
						$starts3 = substr($list[$i]['jincai'],6);
						$num3 = 0;
						if ($start3 == $current_number['jdx']) {
							$num3 = 1;
						}

						if($num3>0){
							$points3 = $num3*$starts3*C('xjp28_jdx');
							$sum_points = $sum_points + $points3;
						}
						break;


					//庄闲和    庄100  和100
					case 4:
						$start4 = substr($list[$i]['jincai'], 0,3);
						$starts4 = substr($list[$i]['jincai'],3);

						$num4 = 0;

						if ($start4 == $current_number['zx']) {
							$num4 = 1;
						}

						if($num4 > 0 ){
							if ($start4 == '庄' || $start4 == '闲') {
								if ($current_number['zx'] == '和') {
									$points4 = $num4*$starts4*1;
								} else {
									$points4 = $num4*$starts4*C('xjp28_zx_1');
								}
							} else {
								$points4 = $num4*$starts4*C('xjp28_zx_2');
							}

							$sum_points = $sum_points + $points4;
						}
						break;


					//豹子对子顺子 100  白字100  
					case 5:
						$start5 = substr($list[$i]['jincai'], 0,6);
						$starts5 = substr($list[$i]['jincai'],6);
						$num5 = 0;
						if ($start5 == $current_number['q3']) {
							$num5 = 1;
						}

						if($num5>0){
							if ($start5 == '豹子') {
								$points5 = $num5*$starts5*C('xjp28_bds_1');
							} else if($start5 == '顺子') {
								$points5 = $num5*$starts5*C('xjp28_bds_2');
							} else if($start5 == '对子') {
								$points5 = $num5*$starts5*C('xjp28_bds_3');
							} else if($start5 == '半顺') {
								$points5 = $num5*$starts5*C('xjp28_bds_4');
							} else if($start5 == '杂六') {
								$points5 = $num5*$starts5*C('xjp28_bds_5');
							}
							
							$sum_points = $sum_points + $points5;
						}
						break;


					//特码数字 3点100  
					case 6:
						$start6 = explode('点', $list[$i]['jincai']);

						$num6 = 0;
						if ($start6[0] == $current_number['tema']) {
							$num6 = 1;
						}

						if($num6>0){
							if ($start6[0] == '0' || $start6[0] == '27') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_0');
							} else if($start6[0] == '1' || $start6[0] == '26') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_1');
							} else if($start6[0] == '2' || $start6[0] == '25') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_2');
							} else if($start6[0] == '3' || $start6[0] == '24') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_3');
							} else if($start6[0] == '4' || $start6[0] == '23') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_4');
							} else if($start6[0] == '5' || $start6[0] == '22') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_5');
							} else if($start6[0] == '6' || $start6[0] == '21') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_6');
							} else if($start6[0] == '7' || $start6[0] == '20') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_7');
							} else if($start6[0] == '8' || $start6[0] == '19') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_8');
							} else if($start6[0] == '9' || $start6[0] == '18') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_9');
							} else if($start6[0] == '10' || $start6[0] == '17') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_10');
							} else if($start6[0] == '11' || $start6[0] == '16') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_11');
							} else if($start6[0] == '12' || $start6[0] == '15') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_12');
							} else if($start6[0] == '13' || $start6[0] == '14') {
								$points6 = $num6*$start6[1]*C('xjp28_tema_13');
							}	

							$sum_points = $sum_points + $points6;
						}
						break;					
				}
			}
		}
		return $sum_del_points - $sum_points;
	}
	
	/* 生成随机数 */
	function randKeys($len){
		$array=array("01","02","03","04","05","06","07","08","09","10");
		$charsLen = count($array) - 1; 
		shuffle($array);
		$output = ""; 
	  //  for ($i=0; $i<$len; $i++){ 
			
		$a= $array[mt_rand(0, $charsLen)];
			$b= $array[mt_rand(0, $charsLen)];
			while($a==$b)
			{
		 $b= $array[mt_rand(0, $charsLen)];
			}
			$c=$array[mt_rand(0, $charsLen)];
			while($c==$a||$c==$b)
			{
		  $c= $array[mt_rand(0, $charsLen)];
			}

			$d= $array[mt_rand(0, $charsLen)];
			while($d==$a||$d==$b||$d==$c)
			{
				$d= $array[mt_rand(0, $charsLen)];
			}
			$e= $array[mt_rand(0, $charsLen)];
			while($e==$a||$e==$b||$e==$c||$e==$d)
			{
				$e= $array[mt_rand(0, $charsLen)];
			}
					$f= $array[mt_rand(0, $charsLen)];
			while($f==$a||$f==$b||$f==$c||$f==$d||$f==$e)
			{
				$f= $array[mt_rand(0, $charsLen)];
			}
					$g= $array[mt_rand(0, $charsLen)];
			while($g==$a||$g==$b||$g==$c||$g==$d||$g==$e||$g==$f)
			{
				$g= $array[mt_rand(0, $charsLen)];
			}
				  $h= $array[mt_rand(0, $charsLen)];
			while($h==$a||$h==$b||$h==$c||$h==$d||$h==$e||$h==$f||$h==$g)
			{
				$h= $array[mt_rand(0, $charsLen)];
			}
				 $i= $array[mt_rand(0, $charsLen)];
			while($i==$a||$i==$b||$i==$c||$i==$d||$i==$e||$i==$f||$i==$g||$i==$h)
			{
				$i= $array[mt_rand(0, $charsLen)];
			}
						 $j= $array[mt_rand(0, $charsLen)];
			while($j==$a||$j==$b||$j==$c||$j==$d||$j==$e||$j==$f||$j==$g||$j==$h||$j==$i)
			{
				$j= $array[mt_rand(0, $charsLen)];
			}
		   //$output .= $array[mt_rand(0, $charsLen)].",";  
	  //  }  
		 return $outpuet=$a.','.$b.','.$c.','.$d.','.$e.','.$f.','.$g.','.$h.','.$i.','.$j;
	   // return rtrim($output, ',');
	}
	
	function make_password( $length = 50 )
	{
		// 密码字符集，可任意添加你需要的字符
		$chars = array('A', 'B', 'C', 'D', 
		'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
		'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		// 在 $chars 中随机取 $length 个数组元素键名
		$keys = array_rand($chars, $length); 
		$password = '';
		for($i = 0; $i < $length; $i++)
		{
			// 将 $length 个数组元素连接成字符串
			$password .= $chars[$keys[$i]];
		}
		return $password;
	}
}

?>