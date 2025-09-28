<?php

namespace Home\Controller;
use Think\Controller;

header('content-type:text/html;charset=utf-8');
class GetController extends Controller{

	//add by gison 20180503
	public function getLhc(){
		$caiji = M('caiji')->where("game='lhc'")->limit(0,1)->order("id desc")->find();
		if (C('index_page') == '1') {
            $format = json_decode(lhc_format($caiji),true);
        } else {
            $format = json_decode(lhc_format2($caiji),true);
        }
		echo json_encode($format);
	}

	public function getPk10(){
		$caiji = M('caiji')->where("game='pk10'")->limit(0,1)->order("id desc")->find();
		$format = json_decode(pk10_format($caiji),true);
		// if (strtotime($format['current']['awardTime']) > strtotime("23:56:00")) {
		// 	$time = strtotime($format['next']['awardTime']) + 9*60*60-300;
		// 	$format['next']['awardTime'] = date('Y-m-d H:i:s',$time);
		// }
		echo json_encode($format);
	}
	
	public function getEr75sc(){
		$caiji = M('caiji')->where("game='er75sc'")->limit(0,1)->order("id desc")->find();
		$format = json_decode(er75sc_format($caiji),true);
		// if (strtotime($format['current']['awardTime']) > strtotime("23:56:00")) {
		// 	$time = strtotime($format['next']['awardTime']) + 9*60*60-300;
		// 	$format['next']['awardTime'] = date('Y-m-d H:i:s',$time);
		// }
		echo json_encode($format);
	}
	
	public function getXyft(){
		$caiji = M('caiji')->where("game='xyft'")->limit(0,1)->order("id desc")->find();
		$format = json_decode(xyft_format($caiji),true);
		echo json_encode($format);
	}

	public function getSsc(){
		$number = M('number')->where("game='ssc'")->limit(0,1)->order("id desc")->find();
		$caiji = M('caiji')->where("game='ssc'")->limit(0,1)->order("id desc")->find();

		$time = time();
		if ($time > strtotime("02:00:00") && $time < strtotime("21:57:00")) {
			$n_awardTime = strtotime($number["awardtime"]) + 590;
		} else {
			$n_awardTime = strtotime($number["awardtime"]) + 290;
		}

		$json_data =  array(
		    "preDrawCode" => explode(',',$number['awardnumbers']),
		    "drawTime" =>  $number['awardtime'],
		    "preDrawIssue" =>  $number['periodnumber'],
			"drawIssue"=>$caiji['next_term'],
		    "sumNum" =>  $number['tema'],
		    "sumBigSmall" =>  $number['tema_dx'],
		    "dragonTiger" =>   $number['lh'],
		    "sumSingleDouble" =>   $number['tema_ds'],
		    "status" =>  0,
		    "id" =>  "#numBig",
		    'counttime' =>  abs($n_awardTime - time()),
		);
		
		echo json_encode($json_data);
	}

	public function getBj28(){
		$number = M('number')->where("game='bj28'")->limit(0,1)->order("id desc")->find();
		$n_awardTime = strtotime($number["awardtime"]) + 300 - 18;
		
		$json_data =  array(
		    "preDrawCode" => explode(',',$number['awardnumbers']),
		    "drawIssue"=>$number['periodnumber']+1,
		    "drawTime" =>  date('Y-m-d H:i:s',$n_awardTime),
		    "preDrawIssue" =>  $number['periodnumber'],
		    "sumNum" =>  $number['tema'],
		    "sumBigSmall" =>  $number['tema_dx'],
		    "time" => ($n_awardTime - time()),
		);
		echo json_encode($json_data);
	}

	public function getJnd28(){
		$number = M('number')->where("game='jnd28'")->limit(0,1)->order("id desc")->find();
		$n_awardTime = strtotime($number["awardtime"]) + 180 -30;
		
		$json_data =  array(
		    "preDrawCode" => explode(',',$number['awardnumbers']),
		    "drawIssue"=>$number['periodnumber']+1,
		    "drawTime" =>  date('Y-m-d H:i:s',$n_awardTime),
		    "preDrawIssue" =>  $number['periodnumber'],
		    "sumNum" =>  $number['tema'],
		    "sumBigSmall" =>  $number['tema_dx'],
		    "time" => ($n_awardTime - time()),
		);
		echo json_encode($json_data);
	}
	
	public function getXjp28(){
		$number = M('number')->where("game='xjp28'")->limit(0,1)->order("id desc")->find();
		$n_awardTime = strtotime($number["awardtime"]) + 300;

		$json_data =  array(
		    "preDrawCode" => explode(',',$number['awardnumbers']),
		    "drawIssue"=>$number['periodnumber']+1,
		    "drawTime" =>  date('Y-m-d H:i:s',$n_awardTime),
		    "preDrawIssue" =>  $number['periodnumber'],
		    "sumNum" =>  $number['tema'],
		    "sumBigSmall" =>  $number['tema_dx'],
		    "time" => abs($n_awardTime - time()),
		);
		echo json_encode($json_data);
	}

	public function getK3(){
		$number = M('number')->where("game='k3'")->limit(0,1)->order("id desc")->find();
		$n_awardTime = strtotime($number["awardtime"]) + 600;

		$json_data =  array(
		    "preDrawCode" => explode(',',$number['awardnumbers']),
		    "drawIssue"=>$number['periodnumber']+1,
		    "drawTime" =>  date('Y-m-d H:i:s',$n_awardTime),
		    "preDrawIssue" =>  $number['periodnumber'],
		    "sumNum" =>  $number['tema'],
		    "sumBigSmall" =>  $number['tema_dx'],
		    "time" => abs($n_awardTime - time()),
		);
		echo json_encode($json_data);
	}


	public function apiSsc(){
		$caiji = M('caiji')->where("game='ssc'")->limit(0,1)->order("id desc")->find();
		echo json_encode($caiji);
	}

	public function apiXyft(){
		$caiji = M('caiji')->where("game='xyft'")->limit(0,1)->order("id desc")->find();

		$periodNumber = $caiji['periodnumber'];
		$awardTime = $caiji['awardtime'];
		$awardNumbers = $caiji['awardnumbers'];

		if (strtotime($awardTime) > strtotime("04:03:00") && strtotime($awardTime) < strtotime("13:03:00")) {
			$time = strtotime($awardTime) + 9*60*60-300;
			$n_awardTime = date('Y-m-d H:i:s',$time);
		}else{
			$n_awardTime = strtotime($awardTime) + 300;
		}

	
		$format = array(
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

		if (strtotime($format['current']['awardTime']) > strtotime("03:56:00")) {
			$time = strtotime($format['next']['awardTime']) + 9*60*60-300;
			$format['next']['awardTime'] = date('Y-m-d H:i:s',$time);
		}
		echo json_encode($format);
	}
}

?>