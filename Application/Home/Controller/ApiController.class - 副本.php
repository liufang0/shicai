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
            1 => 'pk10',
			2 => 'er75sc',
            3 => 'xyft',
            4 => 'ssc',
            5 => 'bj28',
            6 => 'jnd28'
        );
        $this->apiarr = array(
            1 => 'http://api.cpkjapi.com/json?t=bjpk10&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
			2 => 'http://api.cpkjapi.com/json?t=jisussc&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
            3 => 'http://api.cpkjapi.com/json?t=xyft&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
            4 => 'http://api.cpkjapi.com/json?t=cqssc&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
            5 => 'http://api.cpkjapi.com/json?t=pcdd&limit=1&token=972D8A05AC5C388296820A56BE19DA17',
            6 => 'http://api.cpkjapi.com/json?t=jnd28&limit=1&token=972D8A05AC5C388296820A56BE19DA17'
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
				
				if (strtotime('now') >= $currentAward[next_time] || empty($currentAward)) {
					$code = $this->typearr[$k];
					echo "start update $code \n";
					$json_ori = http_get($this->apiarr[$k]);
					$json = json_decode($json_ori, 1);
					
					//echo "<pre>";
					//echo ($this->apiarr[$k]);
					$openInterval = $this->getOpenInterval($code);
					
					$currentAwardWhere[game] = $v;
					$currentAwardWhere[periodnumber] = $json[data][0][expect];
					$currentAward = $caiji_model->where($currentAwardWhere)->find();
					//die_dump($currentAward);
					//var_dump($currentAward);
					if (! empty($json) && empty($currentAward)) {
						
						$data = array(
							'game' => $v,
							'periodnumber' => $json[data][0][expect],
							'awardnumbers' => $json[data][0][opencode],
							'awardtime' => $json[data][0][opentime],
							'addtime' => strtotime('now'),
							'next_term' => ($json[data][0][expect] + 1),
							'next_time' => ($json[data][0][opentimestamp] + $openInterval)
						);
						//$open_model->add($data);
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
	
	//是否到采集时间
	public function isCaijiTime($game){
		//echo $game;
		$beginToday=strtotime('00:00:00');
		$endToday=strtotime("23:59:59");
			
		if($game == "pk10"){
			$beginToday=strtotime('09:00:00');
			$endToday=strtotime("23:59:59");
		}else if($game == "bj28"){
			$beginToday=strtotime('09:04:00');
			$endToday=strtotime("23:55:00");
		}else if($game == "xyft"){
			$beginToday=strtotime('04:05:00');
			$endToday=strtotime("13:05:00");
			if(time()>$beginToday && time()<$endToday){
				return false;
			}else{
				return true;
			}
		}else if($game == "er75sc"){
			$beginToday=strtotime('04:00:00');
			$endToday=strtotime("07:25:00");
			if(time()>$beginToday && time()<$endToday){
				return false;
			}else{
				return true;
			}
		}else if($game == "ssc"){
			$beginToday=strtotime('02:00:00');
			$endToday=strtotime("10:00:00");
			if(time()>$beginToday && time()<$endToday){
				return false;
			}else{
				return true;
			}
		}else if($game == "jnd28"){
			$beginToday=strtotime('19:01:00');
			$endToday=strtotime("21:00:00");
			if(time()>$beginToday && time()<$endToday){
				return false;
			}else{
				return true;
			}
		}
		
		if(time()>$beginToday && time()<$endToday){
			return true;
		}else{
			return false;
		}
	}

    public function getOpenInterval($game)
    {
		if($game == "pk10" || $game == "bj28" || $game == "xyft"){
			return 285;
		}else if($game == "er75sc"){
			return 60;
		}else if($game == "ssc"){
			if ($time < strtotime("21:57:00") && $time > strtotime("02:00:00")) {
				return 280;
			}else{
				return 580;
			}
		}else if($game == "jnd28"){
			return 190;
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
}

?>