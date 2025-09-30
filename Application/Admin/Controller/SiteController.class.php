<?php

namespace Admin\Controller;
use Think\Controller;
class SiteController extends BaseController {
	
	public function 幸运飞艇(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		if(IS_POST){
			$is_ctrl_xjp28 = $_POST["is_ctrl_xjp28"];
			M('config_one')->where("name = 'is_ctrl_xjp28'")->setField('value',$is_ctrl_xjp28);
			$this -> sitesave('site.php');
		}else{
			$config_one = M('config_one')->where("name = 'is_ctrl_xjp28'")->field('value')->find();
			$this->assign('is_ctrl_xjp28',$config_one['value']);
			$this->display();
		}
	}

	public function agent(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}

		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_幸运飞艇(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function xiazhu_er75sc(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_ft(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_ssc(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_bj28(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function xiazhu_bj28b(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function xiazhu_bj28c(){
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_jnd28(){
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function xiazhu_jnd28b(){
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function xiazhu_jnd28c(){
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function xiazhu_xjp28(){
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_k3(){
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function xiazhu_lhc(){
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}

	public function message(){
		$auth = auth_check(C('auth_code'),$_SERVER['HTTP_HOST']);
		if (!$auth) {
			echo "未授权或授权已过期";exit;
		}
		
		if(IS_POST){
			$this -> sitesave('site.php');
		}else{
			$this->display();
		}
	}
	
	public function index() {
		if (IS_POST) {
			$this -> sitesave('site.php');
		} else {
			$this -> display();
		}
	}

	public function setting() {
		if (IS_POST) {
			$this -> sitesave('route.php');
		} else {
			$this -> display();
		}
	}

	private function sitesave($filename) {

		if ($this -> update_config($_POST, $filename)) {

			$this -> success('修改成功，跳转中~', U('site/' . ACTION_NAME));

		} else {

			$this -> error('操作失败', U('site/' . ACTION_NAME));

		}
	}

	private function update_config($new_config, $filename) {
		if (isset($new_config['min_point'])) {
			$log = array(
				'username' => session('admin')['username'],
				'type' => 4,
				'addtime' => time(),
				'content' => "修改下注配置倍率"
			);
			M('admin_log')->add($log);
		}


		$config_file = CONF_PATH . $filename;
		if (is_file($config_file) && is_writable($config_file)) {

			$config =
			require $config_file;

			$config = array_merge($config, $new_config);

			file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);

			@unlink(RUNTIME_FILE);

			return true;

		} else {

			return false;

		}

	}

    /**
     * 获取微博短链接
     */
	public function ajax_short_url(){
	    $url = I('url');
	    //$content = file_get_contents('http://kndwz.com/api-tcn.php?link='.$url);
	    $content[]['url_short'] = $url;
		
	    return $this->ajaxReturn(json_encode($content));
    }
}
