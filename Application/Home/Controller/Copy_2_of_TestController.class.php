<?php
namespace Home\Controller;

use Think\Controller;

class TestController extends Controller
{

    public function index()
    {
        $typearr = array(
            1 => 'pk10'
        ); // 2 => 'xyft', // 3 => 'cqssc' // 幸运飞艇 // 重庆时时彩
        
        $open_model = M("caiji");
        foreach ($typearr as $k => $v) {
            // 北京赛车
            $currentAward = $open_model->where('game="' . $v . '"')
                ->order('next_term desc')
                ->find();
            
            $json_ori = http_get('http://101.200.62.204/Home/Crons/result.html?key=FB7EA78459751D07AB4DFEFBAE1ABD55&type=1');
            $json = json_decode($json_ori, 1);
            // die_dump($json);
            
            if (! empty($json)) {
                
                $open_model->add(array(
                    'game' => $v,
                    'periodnumber' => $json[current][periodNumber],
                    'awardnumbers' => $json[current][awardNumbers],
                    'awardtime' => $json[current][awardTime],
                    'addtime' => NOW_TIME,
                    'next_term' => $json['next'][periodNumber],
                    'next_time' => $json['next'][awardTime]
                ));
                $code = $this->typearr[$k];
                echo "更新 $code 成功！<br>";
            }
        }
        // $this->webRefresh($currentAward);
    }

    public function webRefresh($currentAward)
    {
        // echo "<pre>";
        // var_dump($currentAward);
        
        // zepto 2017-10-13
        echo "系统当前时间戳为 ";
        echo "";
        echo time();
        // <!--JS 页面自动刷新 -->
        echo ("<script type=\"text/javascript\">");
        echo ("function fresh_page()");
        echo ("{");
        echo ("window.location.reload();");
        echo ("}");
        echo ("setTimeout('fresh_page()',3000);");
        echo ("</script>");
    }
}

?>