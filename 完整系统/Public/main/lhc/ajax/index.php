<?php 
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
date_default_timezone_set("Asia/Shanghai");
include_once "../../../Public/config.php";

$shengxiao = array(
	1=>'狗', 13=>'狗', 25=>'狗', 37=>'狗', 49=>'狗', 
	12=>'猪', 24=>'猪', 36=>'猪', 48=>'猪', 
	11=>'鼠', 23=>'鼠', 35=>'鼠', 47=>'鼠', 
	10=>'牛', 22=>'牛', 34=>'牛', 46=>'牛', 
	9=>'虎', 21=>'虎', 33=>'虎', 45=>'虎',
	8=>'兔', 20=>'兔', 32=>'兔', 44=>'兔',
	7=>'龙', 19=>'龙', 31=>'龙', 43=>'龙',
	6=>'蛇', 18=>'蛇', 30=>'蛇', 42=>'蛇',
	5=>'马', 17=>'马', 29=>'马', 41=>'马',
	4=>'羊', 16=>'羊', 28=>'羊', 40=>'羊',
	3=>'猴', 15=>'猴', 27=>'猴', 39=>'猴',
	2=>'鸡', 14=>'鸡', 26=>'鸡', 38=>'鸡',
);
$sebo = array(
	'1'=>'红波','2'=>'红波','7'=>'红波','8'=>'红波','12'=>'红波','13'=>'红波','18'=>'红波','19'=>'红波','23'=>'红波','24'=>'红波','29'=>'红波','30'=>'红波','34'=>'红波','35'=>'红波','40'=>'红波','45'=>'红波','46'=>'红波',
	'3'=>'蓝波','4'=>'蓝波','9'=>'蓝波','10'=>'蓝波','14'=>'蓝波','15'=>'蓝波','20'=>'蓝波','25'=>'蓝波','26'=>'蓝波','31'=>'蓝波','36'=>'蓝波','37'=>'蓝波','41'=>'蓝波','42'=>'蓝波','47'=>'蓝波','48'=>'蓝波',
	'5'=>'绿波','6'=>'绿波','11'=>'绿波','16'=>'绿波','17'=>'绿波','21'=>'绿波','22'=>'绿波','27'=>'绿波','28'=>'绿波','32'=>'绿波','33'=>'绿波','38'=>'绿波','39'=>'绿波','43'=>'绿波','44'=>'绿波','49'=>'绿波'
);
$url='https://1680660.com/smallSix/findSmallSixInfo.do';
$res = file_get_contents($url);
$jsons = json_decode($res, true);
$data = $jsons['result']['data'];

/*测试开始*/
$topcode = db_query("select * from `fn_open` where `type`=9 order by `term` desc limit 1");
$topcode = db_fetch_array();

$qihao = $topcode['term']+1;
$opentime = strtotime($topcode['time'])+30;
$next_term = $topcode['term']+2;
$nexttime = strtotime($topcode['time'])+60;

$data = array();
$data['drawIssue'] = $topcode['term']+1;
$data['drawTime'] = date('Y-m-d H:i:s', strtotime($topcode['time'])+30);
$data['preDrawCode'] = $topcode['code'];
$data['preDrawIssue'] = $topcode['term']; 
$data['preDrawTime'] = $topcode['time'];
/*测试结束*/

$code = explode(',',$data['preDrawCode']);
$opentm = '';
$opentm.=array_sum($code).',';
$opentm.=array_sum($code)>=175 ? '大,' : '小,';
$opentm.=array_sum($code) % 2 == 0 ? '双,' : '单,';
$opentm.=$code[6]<=24 ? '小,' : '大,';
$opentm.=$code[6] % 2==0 ? '双,' : '单,';
if($code[6]==49){
	$opentm.='和,';
}else{
	$opentm.=array_sum(str_split($code[6]))>=7 ? '合大,' : '合小,';	
}
if($code[6]==49){
	$opentm.='和,';
}else{
	$opentm.=array_sum(str_split($code[6]))%2==0 ? '合双,' : '合单,';	
}

if($code[6]==49){
	$opentm.='和';	
}else{
	$opentm.=end(str_split($code[6]))>=5 ? '尾大' : '尾小';
}
$json['success'] = 1;
/*测试开始*/

/*测试结束*/
$json['info'] = array(
					'BetIndex'=>$data['drawIssue'],
					'BetTime'=>strtotime($data['drawTime'])-time(), //应该减点
					'Game'=>'six',
					'Name'=>'六合彩',
					'OpenDateTime'=>date('m.d', strtotime($data['preDrawTime'])).' 21:30',
					'OpenIndex'=>$data['preDrawIssue'],
					'OpenLh' =>$shengxiao[$code[0]].','.$shengxiao[$code[1]].','.$shengxiao[$code[2]].','.$shengxiao[$code[3]].','.$shengxiao[$code[4]].','.$shengxiao[$code[5]].','.$shengxiao[$code[6]],
					'OpenNumber' =>$data['preDrawCode'],
					'OpenTime'=>strtotime($data['drawTime'])-time(),
					'OpenTm'=>$opentm,
					'ServerTime'=>(int)time().'000'
				);
echo json_encode($json);
//echo '{"success":1,"info":{"Game":"six","Name":"\u516d\u5408\u5f69","ServerTime":"1523432085123","OpenIndex":"2018037","OpenNumber":"15,43,48,34,1,23,25","OpenTm":"189,\u5927,\u5355,\u5927,\u5355,\u5408\u5927,\u5408\u5355,\u5c3e\u5927","OpenLh":"\u7334,\u9f99,\u732a,\u725b,\u72d7,\u9f20,\u72d7","OpenDateTime":"04.10 21:30","BetIndex":"2018038","OpenTime":107995,"BetTime":107715}}';
?>