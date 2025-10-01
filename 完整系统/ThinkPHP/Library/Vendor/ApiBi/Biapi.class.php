<?php

require "Des.php";	//加密文件


class Biapi extends JoDES{
	
    /*
	配置参数
	*/
	protected $key='xxxxxx';                                     //站点秘钥
	
	protected $WebSiteCode='0bbd331d-55c5-47b8-b582-9331db697bfe'; //站点code
	
	protected $password='jinyu123'; 								//站点用户密码
	
	protected $biUrl = 'api.gebbs.net';
	// 额度转换
	 public function zzmoney($game,$user,$type,$money)          //用户额度转换  	游戏名	用户名	转入'IN'转出'OUT'	转账金额
	{
		$t = time();
		$str_check = "platformCode=" . $game . '&userName=' . $user .'&userPassWord='.$this->password .'&TimeStamp='.$t;
		$str_encheck = $this->encode($str_check, $this->key);
		if ($type === 'IN')
		{
			$url = 'http://'.$this->biUrl.'/Api/Register?parameter=' . $str_encheck . '&WebSiteCode=' . $this->WebSiteCode;
			$res = $this->https_request($url);
		}
		
        $data = "platformCode=" . $game . '&userName=' . $user . '&transferType=' . $type . '&credit=' . $money.'&TimeStamp='.$t;
        $urlen = ($this->encode($data, $this->key));
        $url = 'http://'.$this->biUrl.'/Api/Transfer?parameter=' . $urlen . '&WebSiteCode=' . $this->WebSiteCode;
		
        $res = $this->https_request($url);
        if ($res['retCode'] === 0) {
            return true;
        } else {
            return false;
        }
	}
	// 获取余额
	public function balances($game,$user)							//子钱包查询方法 游戏名 用户名  
	{
		$t=time();
		$pp=($this->encode('platformCode='.$game.'&userName='.$user.'&userPassWord='.$this->password.'&TimeStamp='.$t,$this->key));
		$url='http://'.$this->biUrl.'/Api/GetUserBalance?parameter='.$pp.'&WebSiteCode='.$this->WebSiteCode;
		$res=$this->https_request($url);
		return $res['retMsg'];
	}
	// 进入游戏
	public function loginbi($game,$user,$gametype=null,$devices=null,$gameId = null, $gameName = null)
	{
		if ($devices == null && $this->isMobile())
			$devices = 1;
		$str = 'platformCode='.$game.'&userName='.$user.'&userPassWord='.$this->password;
		// 是否有游戏类型
		if ($gametype != null)
			$str .= '&gameType='.$gametype;
		// 是否为手机端
		if ($devices != 0)
			$str .= '&devices='.$devices;
		// 是否有gameId，仅限TTG
		if ($gameId != null)
			$str .='&gameId='.$gameId;
		// 是否有gameName，仅限TTG
		if ($gameName != null)
			$str .='&gameName='.$gameName;
		// 加密参数
		$str = $this->encode($str, $this->key);
		$url='http://'.$this->biUrl.'/Api/Login?parameter='.$str.'&WebSiteCode='.$this->WebSiteCode;
		return $url;
	}
	// 获取投注记录
	public function GetMerchantReport($platformCode, $StartTime, $EndTime, $TimeStamp, $PageIndex, $PageSize) {
        $Str = "platformCode=" . $platformCode . "&StartTime=" . $StartTime . "&EndTime=" . $EndTime . "&TimeStamp=" . $TimeStamp . "&PageIndex=" . $PageIndex . "&PageSize=" . $PageSize . "";
        $DesStr = ($this->encode($Str, $this->key));
		
        $url = 'http://report.gebbs.net/QueryApi/GetMerchantReport?parameter=' . $DesStr . '&WebSiteCode=' . $this->WebSiteCode; 
        $data_array = $this->https_request($url);
        return $data_array;
    }
	// 获取商户平台余额
	public function BusinessBalance(){
		$url='http://'.$this->biUrl.'/Api/BusinessBalance?WebSiteCode='.$this->WebSiteCode;
		$Business_data= $this->https_request($url);
		return $Business_data;
	}
	
	//接口请求函数
	public function https_request($url,$data = null)
	{
		$curl = curl_init();//开启请求会话
		curl_setopt($curl, CURLOPT_URL, $url);  //请求的地址
		
		//默认执行GET请求
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  //请求模式为get的设置
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); //请求模式为get的设置
		
		//执行POST请求
		if (!empty($data))
		{
			curl_setopt($curl, CURLOPT_POST, 1);   // 设置POST请求方式
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  // 设置POST请求方式,并且加上json数据
		}
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //执行以后返回文件流，而不直接输出
		
		//执行请求
		$output = curl_exec($curl);
		//关闭资源
		curl_close($curl);
		//吧返回的JSON转成数组
		$output=json_decode($output,true); 
		return $output;
	}
	
	/*移动端判断*/
	function isMobile()
	{ 
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
		{
			return true;
		} 
		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if (isset ($_SERVER['HTTP_VIA']))
		{ 
			// 找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		} 
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if (isset ($_SERVER['HTTP_USER_AGENT']))
		{
			$clientkeywords = array ('nokia',
				'sony',
				'ericsson',
				'mot',
				'samsung',
				'htc',
				'sgh',
				'lg',
				'sharp',
				'sie-',
				'philips',
				'panasonic',
				'alcatel',
				'lenovo',
				'iphone',
				'ipod',
				'blackberry',
				'meizu',
				'android',
				'netfront',
				'symbian',
				'ucweb',
				'windowsce',
				'palm',
				'operamini',
				'operamobi',
				'openwave',
				'nexusone',
				'cldc',
				'midp',
				'wap',
				'mobile'
				); 
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
			{
				return true;
			} 
		} 
		// 协议法，因为有可能不准确，放到最后判断
		if (isset ($_SERVER['HTTP_ACCEPT']))
		{ 
			// 如果只支持wml并且不支持html那一定是移动设备
			// 如果支持wml和html但是wml在html之前则是移动设备
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
			{
				return true;
			} 
		} 
		return false;
	} 


}

?>