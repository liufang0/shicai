<?php
namespace Home\Controller;
use Think\Controller;
class CaiController extends Controller{
	public function sc2(){
		$url = "http://www.bwlc.net/bulletin/trax.html";
		$cpk = http_get($url);
		preg_match_all('/<td>(.*?)<\/td>/',$cpk, $out); 
		if (isset($out[1]) && $out[1]) {
			$periodnumber = strval($out[1][0]);
			$awardnumbers = strval($out[1][1]);
			$awardtime = strval($out[1][2]) . ":30";
		}
		if ($periodnumber && $awardnumbers && $awardtime) {
			$data = array(
				'periodnumber' => $periodnumber,
				'awardnumbers' => $awardnumbers,
				'awardtime' => $awardtime,
				'game' => '幸运飞艇',
				'addtime' => time()
			);
			$caijinum = M('caiji')->where("game = '幸运飞艇'")->limit(0,1)->order("id desc")->find();
			if (strval($caijinum['periodnumber']) != $periodnumber && $periodnumber > $caijinum['periodnumber']) {
				M('caiji')->add($data);
			}
		}
	}
	public function cpkpk(){
		$url = "http://u7a.chengdashizheng.com/chatbet_v3/game/loginweb.php";
		// var_dump($url);
		$cpk = file_get_contents($url);
		var_dump($cpk);exit;
		$cr_data = json_decode($cpk);
		foreach ($cr_data as $key => $value) {
			$periodnumber = $key;
			$awardtime = $value->dateline;
			$awardnumbers = $value->number;
			break;
		}
		if ($periodnumber && $awardnumbers && $awardtime) {
			$data = array(
				'periodnumber' => $periodnumber,
				'awardnumbers' => $awardnumbers,
				'awardtime' => $awardtime,
				'game' => '幸运飞艇',
				'addtime' => time()
			);
			$caijinum = M('caiji')->where("game = '幸运飞艇'")->limit(0,1)->order("id desc")->find();
			if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
				M('caiji')->add($data);
			}
		}		
	}
	
	public function ft1(){
		$url = "https://www.幸运飞艇tv.com/index.php?c=content&a=list&catid=138";
		$cpk = http_get($url);
		preg_match('/<tbody.*?id=\"reslist\">\s*<tr >\s*<td>(.*?)<\/td>\s*<td> (.*?) <\/td>/',$cpk, $num); 
		if (isset($num[1]) && $num[1]) {
			$awardtime = $num[1];
			$periodnumber = $num[2];
		}
		preg_match_all('/<span title="(.*?)" class="ball_pks_  ball_pks.*? ball_lenght10  ">\s*.*?<\/span>/',$cpk, $pnum); 
		$awardnumbers = array();
		if (isset($pnum[1]) && $pnum[1]) {
			$awardnumbers = array();
			foreach ($pnum[1] as $key => $value) {
				if ($key < 10) {
					continue;
				}
				if ($key >= 20) {
					break;
				}
				$awardnumbers[] = $value < 10 ? '0'.$value : $value;
			}
		}
		$awardnumbers = implode(',', $awardnumbers);
		if ($periodnumber && $awardnumbers && $awardtime) {
			$data = array(
				'periodnumber' => $periodnumber,
				'awardnumbers' => $awardnumbers,
				'awardtime' => $awardtime,
				'game' => 'xyft',
				'addtime' => time()
			);
			$caijinum = M('caiji')->where("game = 'xyft'")->limit(0,1)->order("id desc")->find();
			if (strval($caijinum['periodnumber']) != $periodnumber && $periodnumber > $caijinum['periodnumber']) {
				M('caiji')->add($data);
			}
		}
	}
	public function dd(){
				//幸运飞艇开奖采集,幸运飞艇tv
				$time = time();
				if ($time > strtotime("09:00::00") and $time < strtotime("23:59:59")) {
					$url = "http://www.幸运飞艇tv.com/index.php?c=content&a=list&catid=2";
					$cpk = file_get_contents($url);
					
					preg_match('/<tbody.*?id=\"reslist\">\s*<tr >\s*<td>(.*?)<\/td>\s*<td> (.*?) <\/td>/',$cpk, $num); 
					if (isset($num[1]) && $num[1]) {
						$awardtime = $num[1];
						$periodnumber = $num[2];
					}
					preg_match_all('/<span title="(.*?)" class="ball_pks_  ball_pks.*? ball_lenght10  ">\s*.*?<\/span>/',$cpk, $pnum); 
					$awardnumbers = array();
					if (isset($pnum[1]) && $pnum[1]) {
						$awardnumbers = array();
						foreach ($pnum[1] as $key => $value) {
							if ($key < 10) {
								continue;
							}
							if ($key >= 20) {
								break;
							}
							$awardnumbers[] = $value;
						}
					}
					$awardnumbers = implode(',', $awardnumbers);
					
					if ($periodnumber && $awardnumbers && $awardtime) {
						$data = array(
							'periodnumber' => $periodnumber,
							'awardnumbers' => $awardnumbers,
							'awardtime' => $awardtime,
							'game' => '幸运飞艇',
							'addtime' => time()
						);
						$caijinum = M('caiji')->where("game = '幸运飞艇'")->limit(0,1)->order("id desc")->find();
						if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
							M('caiji')->add($data);
						}
					}			
				} 
	}
	public function sc1(){
		
		$url = "http://www.幸运飞艇tv.com/index.php?c=content&a=list&catid=2";
		$cpk = file_get_contents($url);
		
		preg_match('/<tbody.*?id=\"reslist\">\s*<tr >\s*<td>(.*?)<\/td>\s*<td> (.*?) <\/td>/',$cpk, $num); 
		if (isset($num[1]) && $num[1]) {
			$awardtime = $num[1];
			$periodnumber = $num[2];
		}
		preg_match_all('/<span title="(.*?)" class="ball_pks_  ball_pks.*? ball_lenght10  ">\s*.*?<\/span>/',$cpk, $pnum); 
		$awardnumbers = array();
		if (isset($pnum[1]) && $pnum[1]) {
			$awardnumbers = array();
			foreach ($pnum[1] as $key => $value) {
				if ($key < 10) {
					continue;
				}
				if ($key >= 20) {
					break;
				}
				$awardnumbers[] = $value;
			}
		}
		$awardnumbers = implode(',', $awardnumbers);
		if ($periodnumber && $awardnumbers && $awardtime) {
			$data = array(
				'periodnumber' => $periodnumber,
				'awardnumbers' => $awardnumbers,
				'awardtime' => $awardtime,
				'game' => '幸运飞艇',
				'addtime' => time()
			);
			$caijinum = M('caiji')->where("game = '幸运飞艇'")->limit(0,1)->order("id desc")->find();
			if (strval($caijinum['periodnumber']) != $periodnumber  && $periodnumber > $caijinum['periodnumber']) {
				M('caiji')->add($data);
			}
		}
	}
}
?>