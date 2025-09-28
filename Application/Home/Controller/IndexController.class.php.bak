<?php
namespace Home\Controller;

use Think\Controller;

header('content-type:text/html;charset=utf-8');

class IndexController extends BaseController
{
    public function index()
    {

        if (C('is_open') == 0) {
            //$this->redirect('error');
        }
        if ($this->yanzheng($_SERVER['HTTP_HOST']) == 0) {
            //$this->redirect('error');
        }
        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);

        if (!$auth) {
            echo "未授权或授权已过期";
            exit;
        }
        $t_id = I('t');
        session('tid', $t_id);
        if (C('is_baidu')) {
            $this->display("baidu");
        } else {
            $result_index = 'http://' . C('siteurl') . '/Home/Run/index';
            header("location:" . $result_index);
        }
    }

    public function wxlogin()
    {
        if (C('is_weixin') == '1' && is_weixin()) {

            if (C('mp_choose') == '1') {
                $host_url = 'http://' . C('siteurl') . '/Home/Index/redirect_url';
                $redirect_uri = 'http://' . C('siteurl') . '/Home/Index/redirect_url';
            } else {
                $redirect_uri = 'http://' . C('siteurl') . '/Home/Index/redirect_url';
            }
            $appid = C('WEIXINPAY_CONFIG')['APPID'];
            $appsecret = C('WEIXINPAY_CONFIG')['APPSECRET'];
            $result_index = $this->weixin($redirect_uri, $appid, $appsecret);
            header("location:" . $result_index);
        } else {
            $result_index = 'http://' . C('siteurl') . '/Home/Run/index';
            header("location:" . $result_index);
        }

    }

    public function auth_cb()
    {
        $oauth = load_wechat('Oauth');
        $result = session('result');
        if (!$result) {
            $result = $oauth->getOauthAccessToken();
            session('result', $result);
        }
        $wx = M('wx');
        $user = M('user');
        $res = $wx->where("openid = '{$result['openid']}'")->find();
        if ($res) {
            if ($res['expires_in'] < time()) {
                $wx->where("openid = '{$result['openid']}'")->setField('access_token', $result['access_token']);
            }
            $info = $user->where("id = {$res['userid']}")->find();
            if ($info['status'] == 0) {
                //$this->redirect('error');
            }
            $siteurl = $_SERVER['SERVER_NAME'];
            $url = 'http://' . $siteurl . '?t=' . $info['id'];
            $img = qrcode($url);
            $user->where("id = {$res['userid']}")->setField('qrcode', '/' . $img);
            $info['qrcode'] = '/' . $img;
            session('user', $info);
            $this->redirect('Home/Run/index');
        } else {
            if (C('is_open_reg') == 0) {
                //$this->redirect('error');
            }
            $userinfo = session('userinfo');
            if (!$userinfo) {
                $userinfo = $oauth->getOauthUserinfo($result['access_token'], $result['openid']);
                session('userinfo', $userinfo);
            }
            $t_id = session('tid');
            if ($t_id) {
                $data['t_id'] = $t_id;
            }
            $data['nickname'] = $userinfo['nickname'];
            $headimgurl = substr($userinfo['headimgurl'], 0, -2);
            $data['headimgurl'] = $userinfo['headimgurl'];
            $data['country'] = $userinfo['country'];
            $data['province'] = $userinfo['province'];
            $data['sex'] = $userinfo['sex'];
            $data['user_agent'] = serialize(get__browser());
            $data['city'] = $userinfo['city'];
            $data['reg_ip'] = get_client_ip();
            $data['last_ip'] = get_client_ip();
            $data['reg_time'] = time();
            $data['last_time'] = time();
            $data['logins'] = 1;
            $username = mt_rand(100000, 999999);
            $user_find = M('user')->where("username={$username}")->find();
            if ($user_find) {
                $username = mt_rand(100000, 999999);
            }
            $data['username'] = $username;
            $userid = $user->add($data);
            if ($userid) {
                $siteurl = $_SERVER['SERVER_NAME'];
                $url = 'http://' . $siteurl . '?t=' . $userid;
                $img = qrcode($url);
                $user->where("id = {$userid}")->setField('qrcode', '/' . $img);
                $data1['userid'] = $userid;
                $data1['openid'] = $result['openid'];
                $data1['access_token'] = $result['access_token'];
                $data1['expires_in'] = time() + $result['expires_in'];
                $res2 = $wx->add($data1);
                if ($res2) {
                    $data['id'] = $userid;
                    $data['qrcode'] = '/' . $img;
                    session('user', $data);
                    $this->redirect('Home/Run/index');
                }
            }
        }
    }

    public function baidu()
    {
        $value = I('baidu_value');
        if (C('baidu_value') != $value) {
            $url = 'https://www.baidu.com/s?wd=' . $value;
            header("location:" . $url);
            exit;
        } else {
            if (C('is_weixin') == '1' && is_weixin()) {
                $oauth = load_wechat('Oauth');
                if (C('mp_choose') == '1') {
                    $host_url = 'http://' . C('siteurl') . '/Home/Index/redirect_url';
                    $redirect_uri = 'http://' . C('mp_host_url') . '/Home/Index/auth_cb?host_url=' . urlencode($host_url);
                } else {
                    $redirect_uri = 'http://' . C('siteurl') . '/Home/Index/redirect_url';
                }
                $result_index = $oauth->getOauthRedirect($redirect_uri, $state, 'snsapi_userinfo');
                header("location:" . $result_index);
            } else {
                $result_index = 'http://' . C('siteurl') . '/Home/Run/index';
                header("location:" . $result_index);
            }
        }
    }

    public function login()
    {
        if (IS_POST) {
            if (!IS_AJAX) {
                $this->success('提交方式不正确！');
            } else {
                $username = I('username');
                $password = md5(I('password'));
                $res = M('user')->where("username = '{$username}'  && password = '{$password}'")->find();
                if ($res) {
                    session('user', $res);
                    $map['last_ip'] = get_client_ip();
                    $map['last_time'] = time();
                    M('user')->where("id = {$res['id']}")->save($map);
                    if ($res['status'] == 0) {
                        //$this->redirect('error');
                    }
                    $siteurl = $_SERVER['SERVER_NAME'];
                    $url = 'http://' . $siteurl . '?t=' . $res['id'];
                    $img = qrcode($url);
                    M('user')->where("id = {$res['id']}")->setField('qrcode', '/' . $img);
                    $this->success('登录成功,跳转中~', U('/Home/Run/index'), 1);
                } else {
                    $this->error('用户名或密码错误');
                }
            }
        } else {
            $this->display();
        }
    }

    public function register()
    {
        if (IS_POST) {
            if (!IS_AJAX) {
                $this->error('提交方式不正确！');
                die;
            }
            $username = trim(I('username'));
            $phone = trim(I('phone'));
            $password = trim(I('password'));
            $password1 = trim(I('password1'));
            $txpassword = trim(I('txpassword'));
            $t_id = trim(I('t_id'));
            $code = trim(I('code'));


            if (preg_match('/[\\x{4e00}-\\x{9fa5}]/u', $username) > 0) {
                $this->error('不支持中文用户名'); die;
            }
            if (strlen($username) < 5 || strlen($username) > 16) {
                $this->error('用户名必须5-16个字符'); die;
            }
            if (!$phone) {
                $this->error('手机号码不能为空'); die;
            }
            if ($password != $password1) {
                $this->error('两次密码输入不正确');die;
            }
			if (!$txpassword) {
                $this->error('提现密码不能为空');die;
            }
            $verify = new \Think\Verify();
            if (!$verify->check($code)) {
                $this->error('验证码输入不正确');die;
            }


            $password = md5($password);
            $txpassword = md5($txpassword);
            $res = M('user')->where("username = '{$username}'")->find();
            if ($res) {
                $this->error('用户名已存在，请重新输入');die;
            }

            $reg_data = array(
                'nickname' => $username,
                'headimgurl' => '/Public/Home/img/face/3.png',
                'username' => $username,
                'phone'    => $phone,
                'password' => $password,
                'txpassword' => $txpassword,
                'reg_time' => time(),
                'reg_ip'   => get_client_ip()
            );
            if ($t_id) {
                $reg_data['t_id'] = $t_id;
            }
            $reg_id = M('user')->add($reg_data);
            if (!$reg_id) {
                $this->error('注册失败，请重试'); die;
            }

            $siteurl = $_SERVER['SERVER_NAME'];
            $url = 'http://' . $siteurl . '?t=' . $reg_id;
            $img = qrcode($url);
            M('user')->where("id = {$reg_id}")->setField('qrcode', '/' . $img);
            $user = M('user')->where("id={$reg_id}")->find();
            session('user', $user);
            $this->success('注册成功', U('/Home/User/index'), 1);


        } else {
            $this->display();
        }
    }

    public function getcode()
    {
        ob_clean();
        $Verify = new \Think\Verify();
        $Verify->imageW = 146;    // 验证码宽度
        $Verify->expire = 1800;   // 验证码过期时间（s）
        $Verify->fontSize = 20;     // 验证码字体大小(px)
        $Verify->length = 4;      // 验证码位数
        $Verify->useNoise = false;  // 是否添加杂点
        $Verify->fontttf = '5.ttf'; // 验证码字体使用
        $Verify->entry();
    }

    public function logout()
    {
        session(null);
        $this->redirect('/Home/Run/index');
    }

    public function redirect_url()
    {
        $result = session('result');
        if (!$result) {
            $result = $this->oauth();
            session('result', $result);
        }
        $wx = M('wx');
        $user = M('user');
        $res = $wx->where("openid = '{$result['openid']}'")->find();
        if ($res) {
            if ($res['expires_in'] < time()) {
                $wx->where("openid = '{$result['openid']}'")->setField('access_token', $result['access_token']);
            }
            $info = $user->where("id = {$res['userid']}")->find();
            if ($info['status'] == 0) {
                //$this->redirect('error');
            }
            $siteurl = $_SERVER['SERVER_NAME'];
            $url = 'http://' . $siteurl . '?t=' . $info['id'];
            $img = qrcode($url);
            $user->where("id = {$res['userid']}")->setField('qrcode', '/' . $img);
            $info['qrcode'] = '/' . $img;
            session('user', $info);
            $this->redirect('Home/Run/index');
        } else {
            if (C('is_open_reg') == 0) {
                //$this->redirect('error');
            }
            $userinfo = session('userinfo');
            if (!$userinfo) {
                $mmurl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $result['access_token'] . '&openid=' . $result['openid'];
                $getstring = $this->https_request($mmurl);
                $userinfo = json_decode($getstring, true);
                session('userinfo', $userinfo);
            }
            $t_id = session('tid');
            if ($t_id) {
                $data['t_id'] = $t_id;
            }
            $data['nickname'] = $userinfo['nickname'];
            $data['headimgurl'] = $userinfo['headimgurl'];
            $data['country'] = $userinfo['country'];
            $data['province'] = $userinfo['province'];
            $data['sex'] = $userinfo['sex'];
            $data['txpassword'] = md5("123456");
            $data['user_agent'] = serialize(get__browser());
            $data['city'] = $userinfo['city'];
            $data['reg_ip'] = get_client_ip();
            $data['last_ip'] = get_client_ip();
            $data['reg_time'] = time();
            $data['last_time'] = time();
            $data['logins'] = 1;
            $username = mt_rand(100000, 999999);
            $user_find = M('user')->where("username={$username}")->find();
            if ($user_find) {
                $username = mt_rand(100000, 999999);
            }
            $data['username'] = $username;
            $userid = $user->add($data);
            if ($userid) {
                $siteurl = $_SERVER['SERVER_NAME'];
                $url = 'http://' . $siteurl . '?t=' . $userid;
                $img = qrcode($url);
                $user->where("id = {$userid}")->setField('qrcode', '/' . $img);
                $data1['userid'] = $userid;
                $data1['openid'] = $result['openid'];
                $data1['access_token'] = $result['access_token'];
                $data1['expires_in'] = time() + $result['expires_in'];
                $res2 = $wx->add($data1);
                if ($res2) {
                    $data['id'] = $userid;
                    $data['qrcode'] = '/' . $img;
                    session('user', $data);
                    $this->redirect('Home/Run/index');
                }
            }
        }
    }

    public function bind()
    {
        $id = I('id');
        $user = M('user')->where("id={$id}")->find();
        if (!$user['username']) {
            $username = mt_rand(100000, 999999);
            $user_find = M('user')->where("username={$username}")->find();
            if ($user_find) {
                $username = mt_rand(100000, 999999);
            }
            $user['username'] = $username;
        }
        $this->assign("user", $user);
        $this->display();
    }

    public function bind_action()
    {
        $data = I();
        $data['password'] = md5(I('password'));
        $res = M('user')->where("id={$data['id']}")->save($data);
        if ($res) {
            $this->success("绑定成功");
        } else {
            $this->error("绑定失败");
        }
    }

    function oauth()
    {

        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $appid = C('WEIXINPAY_CONFIG')['APPID'];
            $appsecret = C('WEIXINPAY_CONFIG')['APPSECRET'];
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $_GET['code'] . '&grant_type=authorization_code';
            $getstring = $this->https_request($url);
            $res = json_decode($getstring, true);
            return $res;
        } else {
            return "NO CODE";
        }
    }

    function getmm($maccess_token, $mopenid)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $maccess_token . '&openid=' . $mopenid;
        $getstring = $this->https_request($url);
        $userres = json_decode($getstring, true);
        return $userres;
    }

    function weixin($redirect_murl, $appid, $appsecret)
    {
        $redirect_url = $redirect_murl;
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlEncode($redirect_url) . '&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
        return $url;
    }

    function get_content($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $rs = curl_exec($ch);
        curl_close($ch);
        file_put_contents('ceshi.txt', var_dump($rs));
        return $rs;
    }

    function check()
    {
        $f = @trim($_GET['f']);
        if (function_exists($f)) {
            echo '支持' . $f;
        } else {
            echo '不支持' . $f;
        }
        echo '<br />' . mt_rand(1, time());
    }

    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    function coller()
    {
        $this->display();
    }

    function jianadacoller()
    {
        $mm = M('message');
        $numbershuju = $mm->count();
        $data['type'] = 0;
        file_put_contents('ceshi.txt', $numbershuju);
        if ($numbershuju >= 200) {
            $shu = $numbershuju - 100;
            if ($shu < 0) {
                $shu = 0;
            }
            $where['game'] = array('NEQ', '123');
            M('message')->where($where)->order('id asc')->limit($shu)->delete();
            $data['type'] = 1;
        }
        $this->ajaxReturn($data);
    }

    function g_curl($murl)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $murl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    function yanzheng($murl)
    {
        return 1;
    }
}