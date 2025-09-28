<?php

namespace Admin\Controller;
use Think\Controller;

class YusheController extends BaseController{
	
	public function index(){
		
		if(IS_GET){

			$caiji = M('caiji');
			$map['game'] = $_GET['type'];
			$list = $caiji->where($map)->limit('0,1')->order('id DESC')->select();
			$this->assign('list',$list);
			$this->display('index');
			
		}
		if(IS_POST){
			$caiji = M('caiji');
			$caiji_model = M("caiji_admin");
			
			$map['game'] = $_GET['type'];
			$list = $caiji->where($map)->limit(($_POST['page'] - 1)*$_POST['limit'].','.$_POST['limit'])->order('id DESC')->select();
			$counts = 0;
			$count = $caiji->where($map)->count();
			
			if($_POST['page'] == 1){
				$maps['game'] = $_GET['type'];
				$maps['periodnumber']  = array('GT',$list[0]['periodnumber']);
				$lists = $caiji_model->where($maps)->order('id DESC')->select();
				
				$counts = $caiji_model->where($maps)->count();
				
				$list = array_merge($lists,$list);
			}
			
			echo json_encode([
				'code'	=>0,
				'msg'	=>'成功',
				'count'	=>$count + $counts,
				'data'	=>$list
			]);
		}
	}
	
	public function add(){
		if(IS_POST){
			$data = $_POST;
			
			$caiji_model = M("caiji_admin");
			$caiji = M('caiji');
			
			$mess['game'] = $data['type'];
			$mess['periodnumber'] = $data['periodnumber'];
			
			if($data['type'] == 'xyft' || $data['type'] == 'pk10'){
				foreach($data['code'] as &$val){
					if(strlen($val) == 1){
						$val = '0'.$val;
					}
				}
			}
			
			$mess['awardnumbers'] = join(',',$data['code']);
			
			$map['game'] = $mess['game'];
			$map['periodnumber'] = $mess['periodnumber'];
			$info = $caiji_model->where($map)->find();
			$infos = $caiji->where($map)->find();
			
			if($info || $infos){
				$this->error('该号码已预设');
			}else {
				$flag = $caiji_model->add($mess);
				$this->success('添加成功！');
			}
		}
		
	}
	
	public function update(){
		if(IS_POST){
			
			$data = $_POST;
			
			$caiji_model = M("caiji_admin");
			$caiji = M('caiji');
			
			$mess['game'] = $data['type'];
			$mess['periodnumber'] = $data['periodnumber'];
 
			$map['game'] = $data['type'];
			$map['periodnumber'] = $data['periodnumber'];
			$infos=$caiji->where($map)->find();
			if($infos){
				$this->error('该期数已开奖不能修改');
			}else {
				$caiji_model->where($mess)->save(array("awardnumbers"=>$data['code']));
				$this->success('修改成功！');
			}
		}
		
	}
}

?>