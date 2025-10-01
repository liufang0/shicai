<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;

class Cai extends BaseController
{
    /**
     * 采集时时彩数据
     */
    public function sc2()
    {
        $url = "http://www.bwlc.net/bulletin/trax.html";
        $cpk = $this->httpGet($url);
        
        preg_match_all('/<td>(.*?)<\/td>/', $cpk, $out);
        
        if (isset($out[1]) && $out[1]) {
            $periodnumber = strval($out[1][0]);
            $awardnumbers = strval($out[1][1]);
            $awardtime = strval($out[1][2]) . ":30";
        }
        
        if ($periodnumber && $awardnumbers && $awardtime) {
            $data = [
                'periodnumber' => $periodnumber,
                'awardnumbers' => $awardnumbers,
                'awardtime' => $awardtime,
                'game' => '幸运飞艇',
                'addtime' => time()
            ];
            
            $caijinum = Db::table('caiji')
                ->where("game", '幸运飞艇')
                ->order("id desc")
                ->limit(1)
                ->find();
                
            if (strval($caijinum['periodnumber']) != $periodnumber && $periodnumber > $caijinum['periodnumber']) {
                Db::table('caiji')->insert($data);
            }
        }
    }

    /**
     * 采集幸运飞艇数据
     */
    public function cpkpk()
    {
        $url = "http://u7a.chengdashizheng.com/chatbet_v3/game/loginweb.php";
        $cpk = file_get_contents($url);
        
        $cr_data = json_decode($cpk);
        foreach ($cr_data as $key => $value) {
            $periodnumber = $key;
            $awardtime = $value->dateline;
            $awardnumbers = $value->number;
            break;
        }
        
        if ($periodnumber && $awardnumbers && $awardtime) {
            $data = [
                'periodnumber' => $periodnumber,
                'awardnumbers' => $awardnumbers,
                'awardtime' => $awardtime,
                'game' => '幸运飞艇',
                'addtime' => time()
            ];
            
            $caijinum = Db::table('caiji')
                ->where("game", '幸运飞艇')
                ->order("id desc")
                ->limit(1)
                ->find();
                
            if (strval($caijinum['periodnumber']) != $periodnumber && $periodnumber > $caijinum['periodnumber']) {
                Db::table('caiji')->insert($data);
            }
        }
    }

    /**
     * HTTP GET请求
     */
    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if (strpos($url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $result = curl_error($ch);
        }
        
        curl_close($ch);
        return $result;
    }
}