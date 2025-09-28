<?php
$lastNo=$this->getGameLastNo(112);
$zddata = $this->getGameZdData(112,$lastNo['actionNo']);
$opencode =$zddata?$zddata:randKeys();


header('Content-type: application/xml');
echo'<?xml version="1.0" encoding="utf-8"?>';
echo '<xml><row expect="'.$lastNo['actionNo'].'" opencode="'.$opencode.'" opentime="'.$lastNo['actionTime'].'"/></xml>';


/* 生成随机数 */
function randKeys($len=3){
	$str='6038519724';
	$rand='';
	for($x=0;$x<$len;$x++){
		$rand.=($rand!=''?',':'').substr($str,rand(0,strlen($str)-1),1);
	}
	return $rand;
}
?>