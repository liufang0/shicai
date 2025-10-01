<?php

namespace Agent\Controller;
use Think\Controller;

class BaseController extends Controller{
	
	public function _initialize(){
		if(CONTROLLER_NAME!='Login'){
			if(empty($_SESSION['agent'])){
				$this->redirect('Agent/Login/index');
			}
		}
		
	}
	
	
}


?>