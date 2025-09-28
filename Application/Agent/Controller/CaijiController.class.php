<?php

namespace Agent\Controller;
use Think\Controller;

class CaijiController extends BaseController{
	
	public function pk10(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		$caiji = M('caiji');
		$count = $caiji->where("game='pk10'")->count();
		$page = new \Think\Page($count,20);
		$show = $page->show();
		$list = $caiji->where("game='pk10'")->limit($page->firstRow.','.$page->listRows)->order("periodnumber desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function xyft(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		$caiji = M('caiji');
		$count = $caiji->where("game='xyft'")->count();
		$page = new \Think\Page($count,20);
		$show = $page->show();
		$list = $caiji->where("game='xyft'")->limit($page->firstRow.','.$page->listRows)->order("periodnumber desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function ssc(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		$caiji = M('caiji');
		$count = $caiji->where("game='ssc'")->count();
		$page = new \Think\Page($count,20);
		$show = $page->show();
		$list = $caiji->where("game='ssc'")->limit($page->firstRow.','.$page->listRows)->order("periodnumber desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function bj28(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		$caiji = M('caiji');
		$count = $caiji->where("game='bj28'")->count();
		$page = new \Think\Page($count,20);
		$show = $page->show();
		$list = $caiji->where("game='bj28'")->limit($page->firstRow.','.$page->listRows)->order("periodnumber desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function jnd28(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		$caiji = M('caiji');
		$count = $caiji->where("game='jnd28'")->count();
		$page = new \Think\Page($count,20);
		$show = $page->show();
		$list = $caiji->where("game='jnd28'")->limit($page->firstRow.','.$page->listRows)->order("periodnumber desc")->select();

		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->display();
	}

	public function addpk10(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$data['periodnumber'] = I('periodnumber');
 				$data['awardnumbers'] = I('awardnumbers');
 				$data['awardtime'] = time();
 				$data['game'] = 'pk10';
 				$data['addtime'] = time();

 				//增加手动开奖结算
				$log = array(
					'username' => session('admin')['username'],
					'type' => 8,
					'addtime' => time(),
					'content' => "手动开奖【北京赛车】，期号【{$data['periodnumber']}】,开奖号码【{$data['awardnumbers']}】"
				);
				M('admin_log')->add($log);

				//结算
				//开奖结果
				$map['awardnumbers'] = $data['awardnumbers'];
				$map['awardtime'] = time();
				$map['time'] = time();
				$map['periodnumber'] = $data['periodnumber'];
				$info = explode(',', $map['awardnumbers']);
				for($i=0;$i<count($info);$i++){
					if($info[$i]<10){
						$info[$i] = substr($info[$i], 1);
					}
				}
				$map['number'] = serialize($info);
				if($info[0]>$info[9]){
					$lh[0] = '龙';
				}else{
					$lh[0] = '虎';
				}
				if($info[1]>$info[8]){
					$lh[1] = '龙';
				}else{
					$lh[1] = '虎';
				}
				if($info[2]>$info[7]){
					$lh[2] = '龙';
				}else{
					$lh[2] = '虎';
				}
				if($info[3]>$info[6]){
					$lh[3] = '龙';
				}else{
					$lh[3] = '虎';
				}
				if($info[4]>$info[5]){
					$lh[4] = '龙';
				}else{
					$lh[4] = '虎';
				}
				$map['lh'] = serialize($lh);
				$map['tema'] = $info[0]+$info[1];
				if($map['tema'] % 2 == 0){
					$map['tema_ds'] = '双';
				}else if($map['tema'] == 11){
					$map['tema_ds'] = '和';
				}
				else{
					$map['tema_ds'] = '单';
				}
				if($map['tema']>=12){
					$map['tema_dx'] = '大';
				}else if($map['tema'] == 11){
					$map['tema_dx'] = '和';
				}else{
					$map['tema_dx'] = '小';
				}
				if($map['tema']>=3 && $map['tema']<=7){
					$map['tema_dw'] = 'A';
				}
				if($map['tema']>=8 && $map['tema']<=14){
					$map['tema_dw'] = 'B';
				}
				if($map['tema']>=15 && $map['tema']<=19){
					$map['tema_dw'] = 'C';
				}
				if($info[0]>$info[1]){
					$map['zx'] = '庄';
				}else{
					$map['zx'] = '闲';
				}
				$map['game'] = $data['game'];

				$current_number = $map;
				$number1 = explode(',', $current_number['awardnumbers']);
				$lh = unserialize($current_number['lh']);
				for($i=0;$i<count($number1);$i++){
					if($number1[$i] % 2 == 0){
						$number[$i]['ds'] = '双';
						if($number1[$i]>=6){
							$number[$i]['zuhe'] = '大双';
						}else{
							$number[$i]['zuhe'] = '小双';
						}
					}else{
						$number[$i]['ds'] = '单';
						if($number1[$i]>=6){
							$number[$i]['zuhe'] = '大单';
						}else{
							$number[$i]['zuhe'] = '小单';
						}
					}
					if($number1[$i]>=6){
						$number[$i]['dx'] = '大';
					}else{
						$number[$i]['dx'] = '小';
					}
				}	
				//当前局所有竞猜
				$list = M('order')->where("number = {$current_number['periodnumber']}   && state = 1 && is_add = 0 && game='pk10'")->order("time ASC")->select();
				// var_dump($list);exit;
				if($list){
					for($i=0;$i<count($list);$i++){
						$id = $list[$i]['id'];
						$userid = $list[$i]['userid'];
						if($list[$i]['t_id'] and C('fenxiao_set') == 2){
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('yong', $list[$i]['del_points']*C('fenxiao')*0.01);
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('t_add', $list[$i]['del_points']*C('fenxiao')*0.01);
							$fx_data = array(
								'uid' => $userid,
								't_uid' => $list[$i]['t_id'],
								'push_money' => $list[$i]['del_points'],
								'rate' => C('fenxiao')*0.01,
								'money' => $list[$i]['del_points']*C('fenxiao')*0.01,
								'time' => time()
							);
							M('push_money')->add($fx_data);
						}
						
						//分类
						switch($list[$i]['type']){
							//车号大小单双(12345/双/100)
							case 1:
								$start1 = explode('/', $list[$i]['jincai']);
								$num1 = 0;
								$starts1 = str_split($start1[0]);
								if($start1[1]=='单' || $start1[1]=='双'){
									for($a=0;$a<count($starts1);$a++){
										if($starts1[$a]==0){
											$hao1 = '9';
										}else{
											$hao1 = $starts1[$a]-1;
										}
										if($number[$hao1]['ds']==$start1[1]){
											$num1++;
										}
									}
								}else{
									for($a=0;$a<count($starts1);$a++){
										if($starts1[$a]==0){
											$hao1 = '9';
										}else{
											$hao1 = $starts1[$a]-1;
										}
										if($number[$hao1]['dx']==$start1[1]){
											$num1++;
										}
									}
								}
								if($num1>0){
									$points1 = $num1*$start1[2]*C('pk10_dxds');
									$res1 = $this->add_points($id,$userid,$points1);
									
								} 
								break;
								
							//车号(12345/89/20)
							case 2:
								$start2 = explode('/', $list[$i]['jincai']);
								$chehao2 = str_split($start2[1]);
								$starts2 = str_split($start2[0]);
								$num2 = 0;
								for($s=0;$s<count($chehao2);$s++){
									for($a=0;$a<count($starts2);$a++){
										if($starts2[$a]==0){
											$hao2 = '9';
										}else{
											$hao2 = $starts2[$a]-1;
										}
										if($chehao2[$s]==0){
											$chehao2[$s]=10;
										}
										if($chehao2[$s]==$number1[$hao2]){
											$num2++;
										}
									}
								}
								if($num2>0){
									$points2 = $num2*$start2[2]*C('pk10_chehao');
									$res2 = $this->add_points($id,$userid,$points2);
								
								} 
								break;
								
							//组合(890/大单/50)
							case 3:
								$start3 = explode('/', $list[$i]['jincai']);
								$starts3 = str_split($start3[0]);
								$num3 = 0;
								for($a=0;$a<count($starts3);$a++){
									if($starts3[$a]==0){
										$hao3 = '9';
									}else{
										$hao3 = $starts3[$a]-1;
									}
									if($number[$hao3]['zuhe']==$start3[1]){
										$num3++;
									}
								}
								if($num3>0){
									if($start3[1]=='大单' || $start3[1]=='小双'){
										$points3 = $num3*$start3[2]*C('pk10_zuhe_1');
									}else{
										$points3 = $num3*$start3[2]*C('pk10_zuhe_2');
									}
									$res3 = $this->add_points($id,$userid,$points3);
									
								} 
								break;
								
							//龙虎(123/龙/100)
							case 4:
								$start4 = explode('/', $list[$i]['jincai']);
								$starts4 = str_split($start4[0]);
								$num4 = 0;
								for($a=0;$a<count($starts4);$a++){
									if($starts4[$a]==0){
										$hao4 = '9';
									}else{
										$hao4 = $starts4[$a]-1;
									}
									if($lh[$hao4]==$start4[1]){
										$num4++;
									}
								}
								if($num4>0){
									$points4 = $num4*$start4[2]*C('pk10_lh');
									$res4 = $this->add_points($id,$userid,$points4);
									
								} 
								break;
								
							//冠亚庄闲(庄/200)
							case 5:
								$start5 = explode('/', $list[$i]['jincai']);
								if($current_number['zx'] == $start5[0]){
									$points5 = $start5[1]*C('pk10_zx');
									$res5 = $this->add_points($id,$userid,$points5);
									
								}
								break;
								
							//冠亚号码(组/1-9.3-7/100)
							case 6:
								$start6 = explode('/', $list[$i]['jincai']);
								if(strlen($start6[1])>3){
									$zu = explode('.', $start6[1]);
									for($a=0;$a<count($zu);$a++){
										$gy = explode('-', $zu[$a]);
										if($gy[0]==0){
											$gy[0]=10;
										}
										if($gy[1]==0){
											$gy[1]=10;
										}
										if($gy[0]==$number1[0] && $gy[1]==$number1[1] || $gy[0]==$number1[1] && $gy[1]==$number1[0]){
											$num6 = 1;
										}
									}
								}else{
									$gy = explode('-', $start6[1]);
									if($gy[0]==0){
										$gy[0]=10;
									}
									if($gy[1]==0){
										$gy[1]=10;
									}
									if($gy[0]==$number1[0] && $gy[1]==$number1[1] || $gy[0]==$number1[1] && $gy[1]==$number1[0]){
										$num6 = 1;
									}
								}
								if($num6>0){
									$points6 = $num6*$start6[2]*C('pk10_gy');
									$res6 = $this->add_points($id,$userid,$points6);
									
								} 
								break;
								
							//特码大小单双(和双100)
							case 7:
								$start7 = substr($list[$i]['jincai'], 3,3); 
								$starts7 = substr($list[$i]['jincai'], 6);
								$num7 = 0;
								if($current_number['tema'] == 11){
									$num7 = 2;
								}else if($start7=='大' || $start7=='小'){
									if($current_number['tema_dx']==$start7){
										$num7 = 1;
									}
								}else if($start7=='单' || $start7=='双'){
									if($current_number['tema_ds']==$start7){
										$num7 = 1;
									}
								}
								if($num7>0){
									if ($num7 == 1) {
										$points7 = $starts7*C('pk10_tema');
									} else{
										$points7 = $starts7*1;
									}
									
									$res7 = $this->add_points($id,$userid,$points7);
									
								}
								break;
								
							//特码数字(和5.6.7/100)
							case 8:
								$tema1 = array('03','04','18','19');
								$tema2 = array('5','6','16','17');
								$tema3 = array('7','8','14','15');
								$tema4 = array('9','10','12','13');
								$tema5 = array('11');
								
								$start8 = explode('/', $list[$i]['jincai']);
								$starts8 = substr($start8[0], 3);
								$num8 = 0;
								if(strlen($starts8)>1){
									$tlist = explode('.', $starts8);
									for($a=0;$a<count($tlist);$a++){
										if($current_number['tema']==$tlist[$a]){
											if(in_array($tlist[$a], $tema1)){
												$points8 = $start8[1]*C('pk10_tema_sz_1');
											}
											if(in_array($tlist[$a], $tema2)){
												$points8 = $start8[1]*C('pk10_tema_sz_2');
											}
											if(in_array($tlist[$a], $tema3)){
												$points8 = $start8[1]*C('pk10_tema_sz_3');
											}
											if(in_array($tlist[$a], $tema4)){
												$points8 = $start8[1]*C('pk10_tema_sz_4');
											}
											if(in_array($tlist[$a], $tema5)){
												$points8 = $start8[1]*C('pk10_tema_sz_5');
											}
											$num8 = 1;
										}
									}
								}else{
									if($current_number['tema']==$starts8){
										if(in_array($starts8, $tema1)){
											$points8 = $start8[1]*C('pk10_tema_sz_1');
										}
										if(in_array($starts8, $tema2)){
											$points8 = $start8[1]*C('pk10_tema_sz_2');
										}
										if(in_array($starts8, $tema3)){
											$points8 = $start8[1]*C('pk10_tema_sz_3');
										}
										if(in_array($starts8, $tema4)){
											$points8 = $start8[1]*C('pk10_tema_sz_4');
										}
										if(in_array($starts8, $tema5)){
											$points8 = $start8[1]*C('pk10_tema_sz_5');
										}
										$num8 = 1;
									}
								}
								if($num8>0){
									$res8 = $this->add_points($id,$userid,$points8);
								
								} 
								break;
								
							//特码区段(BC/100)
							case 9:
								$start9 = explode('/', $list[$i]['jincai']);
								$num9 = 0;
								if(strlen($start9[0])>1){
									$starts9 = str_split($start9[0]);
									for($a=0;$a<count($starts9);$a++){
										if($current_number['tema_dw']==$starts9[$a]){
											if($starts9[$a]=='A' || $starts9[$a]=='C'){
												$points9 = $start9[1]*C('pk10_tema_qd_1');
											}else{
												$points9 = $start9[1]*C('pk10_tema_qd_2');
											}
											$num9 = 1;
										}
									}
								}else{
									if($current_number['tema_dw']==$start9[0]){
										if($start9[0]=='A' || $start9[0]=='C'){
											$points9 = $start9[1]*C('pk10_tema_qd_1');
										}else{
											$points9 = $start9[1]*C('pk10_tema_qd_2');
										}
										$num9 = 1;
									}
								}
								if($num9>0 && $points9){
									$res9 = $this->add_points($id,$userid,$points9);
									
								}
								break;							
						}
					}
					$this->success('手动添加开奖成功，请查看竞猜记录该期的开奖结果!',U('Admin/Order/index'),1);
				}else{
					$this->error('当期未查找到下注记录');
				}

			}
			
		}else{
			$this->display();
		}
	}

	public function addxyft(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$data['periodnumber'] = I('periodnumber');
 				$data['awardnumbers'] = I('awardnumbers');
 				$data['awardtime'] = time();
 				$data['game'] = 'xyft';
 				$data['addtime'] = time();

 			    //增加手动开奖结算
				$log = array(
					'username' => session('admin')['username'],
					'type' => 8,
					'addtime' => time(),
					'content' => "手动开奖【幸运飞艇】，期号【{$data['periodnumber']}】,开奖号码【{$data['awardnumbers']}】"
				);
				M('admin_log')->add($log);

				//结算
				//开奖结果
				$map['awardnumbers'] = $data['awardnumbers'];
				$map['awardtime'] = time();
				$map['time'] = time();
				$map['periodnumber'] = $data['periodnumber'];
				$info = explode(',', $map['awardnumbers']);
				for($i=0;$i<count($info);$i++){
					if($info[$i]<10){
						$info[$i] = substr($info[$i], 1);
					}
				}
				$map['number'] = serialize($info);
				if($info[0]>$info[9]){
					$lh[0] = '龙';
				}else{
					$lh[0] = '虎';
				}
				if($info[1]>$info[8]){
					$lh[1] = '龙';
				}else{
					$lh[1] = '虎';
				}
				if($info[2]>$info[7]){
					$lh[2] = '龙';
				}else{
					$lh[2] = '虎';
				}
				if($info[3]>$info[6]){
					$lh[3] = '龙';
				}else{
					$lh[3] = '虎';
				}
				if($info[4]>$info[5]){
					$lh[4] = '龙';
				}else{
					$lh[4] = '虎';
				}
				$map['lh'] = serialize($lh);
				$map['tema'] = $info[0]+$info[1];
				if($map['tema'] % 2 == 0){
					$map['tema_ds'] = '双';
				}else if($map['tema'] == 11){
					$map['tema_ds'] = '和';
				}
				else{
					$map['tema_ds'] = '单';
				}
				if($map['tema']>=12){
					$map['tema_dx'] = '大';
				}else if($map['tema'] == 11){
					$map['tema_dx'] = '和';
				}else{
					$map['tema_dx'] = '小';
				}
				if($map['tema']>=3 && $map['tema']<=7){
					$map['tema_dw'] = 'A';
				}
				if($map['tema']>=8 && $map['tema']<=14){
					$map['tema_dw'] = 'B';
				}
				if($map['tema']>=15 && $map['tema']<=19){
					$map['tema_dw'] = 'C';
				}
				if($info[0]>$info[1]){
					$map['zx'] = '庄';
				}else{
					$map['zx'] = '闲';
				}
				$map['game'] = $data['game'];

				$current_number = $map;
				$number1 = explode(',', $current_number['awardnumbers']);
				$lh = unserialize($current_number['lh']);
				for($i=0;$i<count($number1);$i++){
					if($number1[$i] % 2 == 0){
						$number[$i]['ds'] = '双';
						if($number1[$i]>=6){
							$number[$i]['zuhe'] = '大双';
						}else{
							$number[$i]['zuhe'] = '小双';
						}
					}else{
						$number[$i]['ds'] = '单';
						if($number1[$i]>=6){
							$number[$i]['zuhe'] = '大单';
						}else{
							$number[$i]['zuhe'] = '小单';
						}
					}
					if($number1[$i]>=6){
						$number[$i]['dx'] = '大';
					}else{
						$number[$i]['dx'] = '小';
					}
				}	
				//当前局所有竞猜
				$list = M('order')->where("number = {$current_number['periodnumber']}   && state = 1 && is_add = 0 && game='xyft'")->order("time ASC")->select();
				


				if($list){
					for($i=0;$i<count($list);$i++){
						$id = $list[$i]['id'];
						$userid = $list[$i]['userid'];
						if($list[$i]['t_id'] and C('fenxiao_set') == 2){
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('yong', $list[$i]['del_points']*C('fenxiao')*0.01);
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('t_add', $list[$i]['del_points']*C('fenxiao')*0.01);
							$fx_data = array(
								'uid' => $userid,
								't_uid' => $list[$i]['t_id'],
								'push_money' => $list[$i]['del_points'],
								'rate' => C('fenxiao')*0.01,
								'money' => $list[$i]['del_points']*C('fenxiao')*0.01,
								'time' => time()
							);
							M('push_money')->add($fx_data);
						}
						
						//分类
						switch($list[$i]['type']){
							//车号大小单双(12345/双/100)
							case 1:
								$start1 = explode('/', $list[$i]['jincai']);
								$num1 = 0;
								$starts1 = str_split($start1[0]);
								if($start1[1]=='单' || $start1[1]=='双'){
									for($a=0;$a<count($starts1);$a++){
										if($starts1[$a]==0){
											$hao1 = '9';
										}else{
											$hao1 = $starts1[$a]-1;
										}
										if($number[$hao1]['ds']==$start1[1]){
											$num1++;
										}
									}
								}else{
									for($a=0;$a<count($starts1);$a++){
										if($starts1[$a]==0){
											$hao1 = '9';
										}else{
											$hao1 = $starts1[$a]-1;
										}
										if($number[$hao1]['dx']==$start1[1]){
											$num1++;
										}
									}
								}
								if($num1>0){
									$points1 = $num1*$start1[2]*C('ft_xds');
									$res1 = $this->add_points($id,$userid,$points1);
									
								} 
								break;
								
							//车号(12345/89/20)
							case 2:
								$start2 = explode('/', $list[$i]['jincai']);
								$chehao2 = str_split($start2[1]);
								$starts2 = str_split($start2[0]);
								$num2 = 0;
								for($s=0;$s<count($chehao2);$s++){
									for($a=0;$a<count($starts2);$a++){
										if($starts2[$a]==0){
											$hao2 = '9';
										}else{
											$hao2 = $starts2[$a]-1;
										}
										if($chehao2[$s]==0){
											$chehao2[$s]=10;
										}
										if($chehao2[$s]==$number1[$hao2]){
											$num2++;
										}
									}
								}
								if($num2>0){
									$points2 = $num2*$start2[2]*C('ft_chehao');
									$res2 = $this->add_points($id,$userid,$points2);
								
								} 
								break;
								
							//组合(890/大单/50)
							case 3:
								$start3 = explode('/', $list[$i]['jincai']);
								$starts3 = str_split($start3[0]);
								$num3 = 0;
								for($a=0;$a<count($starts3);$a++){
									if($starts3[$a]==0){
										$hao3 = '9';
									}else{
										$hao3 = $starts3[$a]-1;
									}
									if($number[$hao3]['zuhe']==$start3[1]){
										$num3++;
									}
								}
								if($num3>0){
									if($start3[1]=='大单' || $start3[1]=='小双'){
										$points3 = $num3*$start3[2]*C('ft_zuhe_1');
									}else{
										$points3 = $num3*$start3[2]*C('ft_zuhe_2');
									}
									$res3 = $this->add_points($id,$userid,$points3);
									
								} 
								break;
								
							//龙虎(123/龙/100)
							case 4:
								$start4 = explode('/', $list[$i]['jincai']);
								$starts4 = str_split($start4[0]);
								$num4 = 0;
								for($a=0;$a<count($starts4);$a++){
									if($starts4[$a]==0){
										$hao4 = '9';
									}else{
										$hao4 = $starts4[$a]-1;
									}
									if($lh[$hao4]==$start4[1]){
										$num4++;
									}
								}
								if($num4>0){
									$points4 = $num4*$start4[2]*C('ft_lh');
									$res4 = $this->add_points($id,$userid,$points4);
									
								} 
								break;
								
							//冠亚庄闲(庄/200)
							case 5:
								$start5 = explode('/', $list[$i]['jincai']);
								if($current_number['zx'] == $start5[0]){
									$points5 = $start5[1]*C('ft_zx');
									$res5 = $this->add_points($id,$userid,$points5);
									
								}
								break;
								
							//冠亚号码(组/1-9.3-7/100)
							case 6:
								$start6 = explode('/', $list[$i]['jincai']);
								if(strlen($start6[1])>3){
									$zu = explode('.', $start6[1]);
									for($a=0;$a<count($zu);$a++){
										$gy = explode('-', $zu[$a]);
										if($gy[0]==0){
											$gy[0]=10;
										}
										if($gy[1]==0){
											$gy[1]=10;
										}
										if($gy[0]==$number1[0] && $gy[1]==$number1[1] || $gy[0]==$number1[1] && $gy[1]==$number1[0]){
											$num6 = 1;
										}
									}
								}else{
									$gy = explode('-', $start6[1]);
									if($gy[0]==0){
										$gy[0]=10;
									}
									if($gy[1]==0){
										$gy[1]=10;
									}
									if($gy[0]==$number1[0] && $gy[1]==$number1[1] || $gy[0]==$number1[1] && $gy[1]==$number1[0]){
										$num6 = 1;
									}
								}
								if($num6>0){
									$points6 = $num6*$start6[2]*C('ft_gy');
									$res6 = $this->add_points($id,$userid,$points6);
									
								} 
								break;
								
							//特码大小单双(和双100)
							case 7:
								$start7 = substr($list[$i]['jincai'], 3,3); 
								$starts7 = substr($list[$i]['jincai'], 6);
								$num7 = 0;
								if($current_number['tema'] == 11){
									$num7 = 2;
								}else if($start7=='大' || $start7=='小'){
									if($current_number['tema_dx']==$start7){
										$num7 = 1;
									}
								}else if($start7=='单' || $start7=='双'){
									if($current_number['tema_ds']==$start7){
										$num7 = 1;
									}
								}
								if($num7>0){
									if ($num7 == 1) {
										$points7 = $starts7*C('ft_tema');
									} else{
										$points7 = $starts7*1;
									}
									
									$res7 = $this->add_points($id,$userid,$points7);
									
								}
								break;
								
							//特码数字(和5.6.7/100)
							case 8:
								$tema1 = array('03','04','18','19');
								$tema2 = array('5','6','16','17');
								$tema3 = array('7','8','14','15');
								$tema4 = array('9','10','12','13');
								$tema5 = array('11');
								
								$start8 = explode('/', $list[$i]['jincai']);
								$starts8 = substr($start8[0], 3);
								$num8 = 0;
								if(strlen($starts8)>1){
									$tlist = explode('.', $starts8);
									for($a=0;$a<count($tlist);$a++){
										if($current_number['tema']==$tlist[$a]){
											if(in_array($tlist[$a], $tema1)){
												$points8 = $start8[1]*C('ft_tema_sz_1');
											}
											if(in_array($tlist[$a], $tema2)){
												$points8 = $start8[1]*C('ft_tema_sz_2');
											}
											if(in_array($tlist[$a], $tema3)){
												$points8 = $start8[1]*C('ft_tema_sz_3');
											}
											if(in_array($tlist[$a], $tema4)){
												$points8 = $start8[1]*C('ft_tema_sz_4');
											}
											if(in_array($tlist[$a], $tema5)){
												$points8 = $start8[1]*C('ft_tema_sz_5');
											}
											$num8 = 1;
										}
									}
								}else{
									if($current_number['tema']==$starts8){
										if(in_array($starts8, $tema1)){
											$points8 = $start8[1]*C('ft_tema_sz_1');
										}
										if(in_array($starts8, $tema2)){
											$points8 = $start8[1]*C('ft_tema_sz_2');
										}
										if(in_array($starts8, $tema3)){
											$points8 = $start8[1]*C('ft_tema_sz_3');
										}
										if(in_array($starts8, $tema4)){
											$points8 = $start8[1]*C('ft_tema_sz_4');
										}
										if(in_array($starts8, $tema5)){
											$points8 = $start8[1]*C('ft_tema_sz_5');
										}
										$num8 = 1;
									}
								}
								if($num8>0){
									$res8 = $this->add_points($id,$userid,$points8);
								
								} 
								break;
								
							//特码区段(BC/100)
							case 9:
								$start9 = explode('/', $list[$i]['jincai']);
								$num9 = 0;
								if(strlen($start9[0])>1){
									$starts9 = str_split($start9[0]);
									for($a=0;$a<count($starts9);$a++){
										if($current_number['tema_dw']==$starts9[$a]){
											if($starts9[$a]=='A' || $starts9[$a]=='C'){
												$points9 = $start9[1]*C('ft_tema_qd_1');
											}else{
												$points9 = $start9[1]*C('ft_tema_qd_2');
											}
											$num9 = 1;
										}
									}
								}else{
									if($current_number['tema_dw']==$start9[0]){
										if($start9[0]=='A' || $start9[0]=='C'){
											$points9 = $start9[1]*C('ft_tema_qd_1');
										}else{
											$points9 = $start9[1]*C('ft_tema_qd_2');
										}
										$num9 = 1;
									}
								}
								if($num9>0 && $points9){
									$res9 = $this->add_points($id,$userid,$points9);
									
								}
								break;							
						}
					}
					$this->success('手动添加开奖成功，请查看竞猜记录该期的开奖结果!',U('Admin/Order/index'),1);
				}else{
					$this->error('当期未查找到下注记录');
				}
			}
			
		}else{
			$this->display();
		}
	}

	

	public function addssc(){
		if(IS_POST){
			if(!IS_AJAX){
				$this->error('提交方式不正确！');
			}else{
				$data['periodnumber'] = I('periodnumber');
 				$data['awardnumbers'] = I('awardnumbers');
 				$data['awardtime'] = time();
 				$data['game'] = 'ssc';
 				$data['addtime'] = time();

 				//增加手动开奖结算
				$log = array(
					'username' => session('admin')['username'],
					'type' => 8,
					'addtime' => time(),
					'content' => "手动开奖【重庆时时彩】，期号【{$data['periodnumber']}】,开奖号码【{$data['awardnumbers']}】"
				);
				M('admin_log')->add($log);

				//结算
				//开奖结果
				$map['awardnumbers'] = $data['awardnumbers'];
				$map['awardtime'] = time();
				$map['time'] = time();
				$map['periodnumber'] = $data['periodnumber'];
				$info = explode(',', $map['awardnumbers']);
				
				//龙虎合
				$map['number'] = serialize($info);
				if($info[0]>$info[4]){
					$map['lh'] = '龙';
				}else if($info[0] == $info[4]){
					$map['lh'] = '合';
				}else{
					$map['lh'] = '虎';
				}
				$map['tema'] = $info[0]+$info[1]+$info[2]+$info[3]+$info[4];

				//大小单双
				if($map['tema'] % 2 == 0){
					$map['tema_ds'] = '双';
				}
				else{
					$map['tema_ds'] = '单';
				}
			
				if($map['tema']>=12){
					$map['tema_dx'] = '大';
				}else{
					$map['tema_dx'] = '小';
				}

				//区段
				if($map['tema']>=0 && $map['tema']<=15){
					$map['tema_dw'] = 'A';
				}
				if($map['tema']>=16 && $map['tema']<=29){
					$map['tema_dw'] = 'B';
				}
				if($map['tema']>=30 && $map['tema']<=45){
					$map['tema_dw'] = 'C';
				}
			
				//前中后
				$map['q3'] = ssc_qzh(array($info[0],$info[1],$info[2]));
				$map['z3'] = ssc_qzh(array($info[1],$info[2],$info[3]));
				$map['h3'] = ssc_qzh(array($info[2],$info[3],$info[4]));

				$current_number = $map;

				$number1 = explode(',', $current_number['awardnumbers']);
				
				for($i=0;$i<count($number1);$i++){
					if($number1[$i] % 2 == 0){
						$number[$i]['ds'] = '双';
					}else{
						$number[$i]['ds'] = '单';
						
					}
					if($number1[$i]>=6){
						$number[$i]['dx'] = '大';
					}else{
						$number[$i]['dx'] = '小';
					}
				}	
				//当前局所有竞猜
				$today_time = strtotime(date('Y-m-d',time()));
				$list = M('order')->where("number = {$current_number['periodnumber']} && time > '{$today_time}' && state = 1 && is_add = 0 and game = 'ssc'")->order("time ASC")->select();

				//当前局所有竞猜
				$list = M('order')->where("number = {$current_number['periodnumber']}   && state = 1 && is_add = 0")->order("time ASC")->select();
			
				if($list){
					for($i=0;$i<count($list);$i++){
						$id = $list[$i]['id'];
						$userid = $list[$i]['userid'];
						if($list[$i]['t_id'] and C('fenxiao_set') == 2){
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('yong', $list[$i]['del_points']*C('fenxiao')*0.01);
							M('user')->where("id = {$list[$i]['t_id']}")->setInc('t_add', $list[$i]['del_points']*C('fenxiao')*0.01);
							$fx_data = array(
								'uid' => $userid,
								't_uid' => $list[$i]['t_id'],
								'push_money' => $list[$i]['del_points'],
								'rate' => C('fenxiao')*0.01,
								'money' => $list[$i]['del_points']*C('fenxiao')*0.01,
								'time' => time()
							);
							M('push_money')->add($fx_data);
						}
						
						//分类
						switch($list[$i]['type']){
							//定位球(12345/89/20)
							case 1:
								$start1 = explode('/', $list[$i]['jincai']);
								$chehao1 = str_split($start1[1]);
								$starts1 = str_split($start1[0]);
								$num1 = 0;
								for($s=0;$s<count($chehao1);$s++){
									for($a=0;$a<count($starts1);$a++){
										$hao1 = $starts1[$a]-1;
										if($chehao1[$s]==$number1[$hao1]){
											$num1++;
										}
									}
								}
								if($num1>0){
									$points1 = $num1*$start1[2]*C('ssc_dwq');
									$res1 = $this->add_points($id,$userid,$points1);
								
								} 
								break;

							//龙虎合(龙/100)
							case 2:
								$start2 = substr($list[$i]['jincai'], 0,3); 
								$starts2 = substr($list[$i]['jincai'], 3);
								$num2 = 0;
								if ($start2 == $current_number['lh']) {
										$num2++;
								}

								if($num2>0){
									if ($start2 == '龙' || $start2 == '虎') {
										$points2 = $num2*$start2[2]*C('ssc_lhh_1');
									} else if($start4 == '合') {
										$points2 = $num2*$start2[2]*C('ssc_lhh_2');
									}

									$res2= $this->add_points($id,$userid,$points2);
									
								}
								break;
								
								
							
							//定位大小单双(12345/双/100)
							case 3:
								$start1 = explode('/', $list[$i]['jincai']);
								$num1 = 0;
								$starts1 = str_split($start1[0]);
								if($start1[1]=='单' || $start1[1]=='双'){
									for($a=0;$a<count($starts1);$a++){
										$hao1 = $starts1[$a]-1;
										if($number[$hao1]['ds']==$start1[1]){
											$num1++;
										}
									}
								}else{
									for($a=0;$a<count($starts1);$a++){
										$hao1 = $starts1[$a]-1;
										if($number[$hao1]['dx']==$start1[1]){
											$num1++;
										}
									}
								}
								if($num1>0){
									$points1 = $num1*$start1[2]*C('ssc_dxds');
									$res1 = $this->add_points($id,$userid,$points1);
								
								} 
								break;
						
							//总数大小单双  大/100  小/100  
							case 4:
								$start4 = substr($list[$i]['jincai'], 0,3); 
								$starts4 = substr($list[$i]['jincai'], 3);
								$num4 = 0;

								if ($start4 == '大' || $start4 == '小') {
									if ($start4 == $current_number['tema_dx']) {
										$num4++;
									}
								} else {
									if ($start4 == $current_number['tema_ds']) {
										$num4++;
									}
								}

								if($num4>0){
									$points4 = $num4*$starts4*C('ssc_zdxds');
									$res4 = $this->add_points($id,$userid,$points4);
									
								} 
								break;

								
							//前中后  前豹子100  中顺子100
							case 5:
								$start5 = substr($list[$i]['jincai'], 0,3); 
								$starts5 = substr($list[$i]['jincai'], 3,6);
								$money5 = substr($list[$i]['jincai'], 9);
								$num5 = 0;
								if ($start5 == '前') {
									if ($starts5 == $current_number['q3']) {
										$num5 = 1;
									}
								} else if($start5 == "中") {
									if ($starts5 == $current_number['z3']) {
										$num5 = 1;
									}
								} else if($start5 == '后') {
									if ($starts5 == $current_number['h3']) {
										$num5 = 1;
									}
								}

								if($num5>0){
									if ($starts5 == '豹子') {
										$points5 = $num5*$money5*C('ssc_qzh_1');
									} 
									if ($starts5 == '顺子') {
										$points5 = $num5*$money5*C('ssc_qzh_2');
									} 
									if ($starts5 == '对子') {
										$points5 = $num5*$money5*C('ssc_qzh_3');
									} 
									if ($starts5 == '半顺') {
										$points5 = $num5*$money5*C('ssc_qzh_4');
									} 
									if ($starts5 == '杂六') {
										$points5 = $num5*$money5*C('ssc_qzh_5');
									} 

									$res5 = $this->add_points($id,$userid,$points5);
									
								} 
								break;
						
							//和值特码区段(BC/100)
							case 6:
								$start6 = explode('/', $list[$i]['jincai']);
								$num6 = 0;
								if(strlen($start6[0])>1){
									$starts6 = str_split($start6[0]);
									for($a=0;$a<count($starts6);$a++){
										if($current_number['tema_dw']==$starts6[$a]){
											if($starts6[$a]=='A' || $starts6[$a]=='C'){
												$points6 = $start6[1]*C('ssc_tema_qd_1');
											}else{
												$points6 = $start6[1]*C('ssc_tema_qd_2');
											}
											$num9 = 1;
										}
									}
								}else{
									if($current_number['tema_dw']==$start6[0]){
										if($start6[0]=='A' || $start6[0]=='C'){
											$points6 = $start6[1]*C('ssc_tema_qd_1');
										}else{
											$points6 = $start6[1]*C('ssc_tema_qd_2');
										}
										$num6 = 1;
									}
								}
								if($num6>0 && $points6){
									$res6 = $this->add_points($id,$userid,$points6);
								
								}
								break;		
						//二字玩法(13/13.37/100)
						case 7:
							$start7 = explode('/', $list[$i]['jincai']);
							$num71 = 0;
							$num72 = 0;
					
							$zu = explode('.', $start7[1]);
							$zu1 = str_split($zu[0]);
							$zu2 = str_split($zu[1]);
							$ws = str_split($start7[0]);
							for($a=0;$a<count($zu1);$a++){
								if ($number1[$ws[0]-1] == $zu1[$a]) {
									$num71++;
								}
							}
							for($b=0;$b<count($zu2);$b++){
								if ($number1[$ws[1]-1] == $zu2[$b]) {
									$num72++;
								}
							}
							if($num71>0 && $num72>0){
								$points7 = $num71*$num72*$start7[2]*C('pk10_he');
								$res7 = $this->add_points($id,$userid,$points6);
								if($res7){
									$this->send_msg('pointsadd',$points6,$userid);
								}
							}
							break;				
						}
					}
					$this->success('手动添加开奖成功，请查看竞猜记录该期的开奖结果!',U('Admin/Order/index'),1);
				}else{
					$this->error('当期未查找到下注记录');
				}
			}
			
		}else{
			$this->display();
		}
	}

	public function add_points($order_id,$userid,$points){
		$res = M('user')->where("id = $userid")->setInc('points',$points);
		if($res){
			$res1 = M('order')->where("id = $order_id")->setField(array('is_add'=>'1','add_points'=>$points));
		}
		if($res && $res1){
			return 1;
		}
	}

}

?>