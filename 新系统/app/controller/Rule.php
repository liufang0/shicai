<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 游戏规则控制器
 */
class Rule extends BaseController
{
    /**
     * 北京28规则
     */
    public function bj28()
    {
        return $this->gameRule('北京28', [
            '游戏说明' => '北京28是一种数字竞猜游戏，每期开出3个数字',
            '开奖时间' => '每5分钟开奖一次',
            '投注类型' => [
                '大小' => '总和14-27为大，0-13为小，赔率1.96',
                '单双' => '总和为奇数或偶数，赔率1.96',
                '组合' => '大单、大双、小单、小双，赔率3.7-4.2',
                '极值' => '极小(0-5)、极大(22-27)，赔率25',
                '豹子' => '三个数字相同，赔率200',
                '对子' => '两个数字相同，赔率8',
                '顺子' => '三个连续数字，赔率20'
            ]
        ]);
    }
    
    /**
     * 时时彩规则
     */
    public function ssc()
    {
        return $this->gameRule('时时彩', [
            '游戏说明' => '时时彩每期开出5个数字，可投注万、千、百、十、个位',
            '开奖时间' => '每10分钟开奖一次',
            '投注类型' => [
                '定位胆' => '选择任意位置的数字，赔率9.8',
                '大小' => '数字5-9为大，0-4为小，赔率1.96',
                '单双' => '奇数或偶数，赔率1.96',
                '龙虎' => '万位与个位比较大小，赔率1.96',
                '前三直选' => '前三位精确顺序，赔率1000',
                '前三组选' => '前三位任意顺序，赔率166'
            ]
        ]);
    }
    
    private function gameRule($gameName, $rules)
    {
        $html = '<!DOCTYPE html><html lang="zh-CN"><head>';
        $html .= '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<title>' . $gameName . '游戏规则</title>';
        $html .= '<style>
            body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Microsoft YaHei",sans-serif;background:#f5f7fa;margin:0;padding:20px;}
            .container{max-width:800px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.1);overflow:hidden;}
            .header{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:30px;text-align:center;}
            .header h1{margin:0;font-size:28px;font-weight:600;}
            .content{padding:30px;}
            .rule-section{margin-bottom:30px;}
            .rule-section h2{color:#333;font-size:20px;margin:0 0 15px;padding-bottom:10px;border-bottom:2px solid #667eea;}
            .rule-item{background:#f8f9ff;border-left:4px solid #667eea;padding:15px;margin:10px 0;border-radius:0 8px 8px 0;}
            .rule-item strong{color:#333;display:block;margin-bottom:8px;font-size:16px;}
            .rule-item p{color:#666;margin:0;line-height:1.6;}
            .odds{background:#e8f5e8;border-left-color:#28a745;}
            .nav-bottom{display:flex;gap:15px;margin-top:30px;}
            .btn{flex:1;padding:12px;text-align:center;background:#667eea;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;transition:all 0.3s;}
            .btn:hover{background:#5a67d8;transform:translateY(-2px);}
            .btn.secondary{background:#28a745;}
            .btn.secondary:hover{background:#218838;}
        </style></head><body>';
        
        $html .= '<div class="container">';
        $html .= '<div class="header"><h1>🎲 ' . $gameName . ' 游戏规则</h1></div>';
        $html .= '<div class="content">';
        
        foreach ($rules as $title => $content) {
            if (is_array($content)) {
                $html .= '<div class="rule-section"><h2>' . $title . '</h2>';
                foreach ($content as $key => $value) {
                    $html .= '<div class="rule-item odds">';
                    $html .= '<strong>' . $key . '</strong>';
                    $html .= '<p>' . $value . '</p>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            } else {
                $html .= '<div class="rule-item">';
                $html .= '<strong>' . $title . '</strong>';
                $html .= '<p>' . $content . '</p>';
                $html .= '</div>';
            }
        }
        
        $html .= '<div class="nav-bottom">';
        $html .= '<a href="/run/fangjian?game=' . strtolower(str_replace(['北京', '时时彩'], ['bj', 'ssc'], $gameName)) . '" class="btn">🎮 开始游戏</a>';
        $html .= '<a href="/" class="btn secondary">🏠 返回首页</a>';
        $html .= '</div></div></div></body></html>';
        
        return $html;
    }
}