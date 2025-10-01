<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;

/**
 * 游戏运行控制器
 */
class Run
{
    /**
     * 游戏大厅首页
     */
    public function index()
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>游戏大厅</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:linear-gradient(135deg,#667eea,#764ba2);color:white;min-height:100vh;padding:20px;} .container{max-width:1000px;margin:0 auto;} .header{background:rgba(255,255,255,0.15);border-radius:20px;padding:30px;margin-bottom:30px;text-align:center;} .games-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;} .game-card{background:rgba(255,255,255,0.1);border-radius:16px;padding:25px;text-align:center;border:2px solid rgba(255,255,255,0.2);transition:all 0.3s;} .game-card:hover{transform:translateY(-5px);background:rgba(255,255,255,0.15);} .game-icon{font-size:48px;margin-bottom:15px;} .game-title{font-size:22px;font-weight:600;margin-bottom:10px;} .game-desc{color:rgba(255,255,255,0.8);font-size:14px;margin-bottom:20px;} .game-btn{display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#ff6b6b,#ee5a24);color:white;text-decoration:none;border-radius:25px;font-weight:600;transition:all 0.3s;} .game-btn:hover{transform:translateY(-2px);} .nav-btn{display:inline-block;padding:12px 24px;background:rgba(255,255,255,0.2);color:white;text-decoration:none;border-radius:8px;margin-top:20px;}</style>';
        $html .= '</head><body>';
        
        $html .= '<div class="container">';
        $html .= '<div class="header">';
        $html .= '<h1>🎮 游戏大厅</h1>';
        $html .= '<p>选择您喜欢的游戏开始体验</p>';
        $html .= '</div>';
        
        $html .= '<div class="games-grid">';
        
        $html .= '<div class="game-card">';
        $html .= '<div class="game-icon">🎰</div>';
        $html .= '<div class="game-title">幸运飞艇</div>';
        $html .= '<div class="game-desc">刺激有趣的幸运飞艇赛车游戏</div>';
        $html .= '<a href="/run/幸运飞艇" class="game-btn">立即进入</a>';
        $html .= '</div>';
        
        $html .= '<div class="game-card">';
        $html .= '<div class="game-icon">⏰</div>';
        $html .= '<div class="game-title">时时彩</div>';
        $html .= '<div class="game-desc">经典时时彩玩法</div>';
        $html .= '<a href="/run/ssc" class="game-btn">立即进入</a>';
        $html .= '</div>';
        
        $html .= '<div class="game-card">';
        $html .= '<div class="game-icon">🎯</div>';
        $html .= '<div class="game-title">北京28</div>';
        $html .= '<div class="game-desc">热门数字游戏</div>';
        $html .= '<a href="/run/bj28" class="game-btn">立即进入</a>';
        $html .= '</div>';
        
        $html .= '<div class="game-card">';
        $html .= '<div class="game-icon">🎲</div>';
        $html .= '<div class="game-title">加拿大28</div>';
        $html .= '<div class="game-desc">国际化数字娱乐</div>';
        $html .= '<a href="/run/jnd28" class="game-btn">立即进入</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '<div style="text-align:center;">';
        $html .= '<a href="/" class="nav-btn">← 返回首页</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // 添加原始底部导航
        $html .= '<nav class="bottom-nav">';
        $html .= '<a href="/" class="nav-item"><img src="/images/menu1_hui.png" alt="首页"><span>首页</span></a>';
        $html .= '<a href="/run" class="nav-item active"><img src="/images/pay.png" alt="产品"><span>产品</span></a>';
        $html .= '<a href="#" class="nav-item"><img src="/images/menu3.png" alt="客服"><span>客服</span></a>';
        $html .= '<a href="/run/trend" class="nav-item"><img src="/images/menu2.png" alt="走势"><span>走势</span></a>';
        $html .= '<a href="/user" class="nav-item"><img src="/images/menu5.png" alt="我的"><span>我的</span></a>';
        $html .= '<div class="safe-area"></div>';
        $html .= '</nav>';
        
        // 添加导航样式
        $html .= '<style>';
        $html .= 'body{padding-bottom:calc(60px + env(safe-area-inset-bottom));}';
        $html .= '.bottom-nav{position:fixed;left:0;right:0;bottom:0;height:60px;background:rgba(0,0,0,0.85);border-top:1px solid rgba(255,255,255,0.12);backdrop-filter:blur(8px);display:flex;align-items:stretch;justify-content:space-around;z-index:2147483647;pointer-events:auto;}';
        $html .= '.bottom-nav .nav-item{flex:1;text-align:center;text-decoration:none;color:rgba(255,255,255,0.85);display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:12px;-webkit-tap-highlight-color:transparent;}';
        $html .= '.bottom-nav .nav-item img{width:22px;height:22px;display:block;margin-bottom:2px;filter:grayscale(100%) opacity(0.75);pointer-events:none;}';
        $html .= '.bottom-nav .nav-item:hover{color:#fff;}';
        $html .= '.bottom-nav .nav-item.active{color:#fff;}';
        $html .= '.bottom-nav .nav-item.active img{filter:none;}';
        $html .= '.bottom-nav .safe-area{position:absolute;left:0;right:0;bottom:0;height:env(safe-area-inset-bottom);background:rgba(0,0,0,0.85);pointer-events:none;}';
        $html .= '</style>';
        
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * 幸运飞艇游戏
     */
    public function 幸运飞艇()
    {
        // 获取房间参数
        $room = input('param.room', '1');
        
        // 模拟用户信息
        $userinfo = [
            'id' => 1,
            'username' => '游客',
            'points' => 10000,
            'headimgurl' => '/images/default.png'
        ];
        
        // 幸运飞艇游戏页面HTML
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>幸运飞艇</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:linear-gradient(135deg,#1e3c72,#2a5298);color:white;min-height:100vh;overflow-x:hidden;} body::before{content:"";position:fixed;top:0;left:0;width:100%;height:100%;background:url("data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M20 20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v20h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2H20v-2h2v-2h-2v-2h2v-2h-2v-2h2v-2h-2v-2h2v-2h-2V20z"/%3E%3C/g%3E%3C/svg%3E") repeat;pointer-events:none;z-index:-1;} .game-container{padding:20px;max-width:800px;margin:0 auto;} .header{background:rgba(255,255,255,0.15);border-radius:20px;padding:25px;margin-bottom:25px;text-align:center;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);} .room-info{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;background:rgba(255,255,255,0.1);padding:15px;border-radius:12px;} .period-info{background:rgba(255,255,255,0.15);border-radius:15px;padding:20px;margin-bottom:25px;backdrop-filter:blur(8px);} .water-track{background:linear-gradient(180deg,rgba(30,144,255,0.3),rgba(0,100,200,0.4));border-radius:20px;padding:25px;margin-bottom:25px;min-height:350px;position:relative;overflow:hidden;border:2px solid rgba(135,206,250,0.3);} .water-track::before{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:url("data:image/svg+xml,%3Csvg width="100" height="20" viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M0 10c5-5 15-5 20 0s15 5 20 0s15-5 20 0s15 5 20 0s15-5 20 0v10H0V10z" fill="%23ffffff" fill-opacity="0.1"/%3E%3C/svg%3E") repeat-x;animation:waves 4s ease-in-out infinite;pointer-events:none;} @keyframes waves{0%,100%{transform:translateX(0);} 50%{transform:translateX(-50px);}} .boat{width:70px;height:40px;background:linear-gradient(45deg,#ff6b35,#f7931e);border-radius:25px 25px 8px 8px;margin:12px 0;position:relative;animation:sailing 4s ease-in-out infinite;box-shadow:0 4px 15px rgba(0,0,0,0.3);} .boat::before{content:"⛵";position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:28px;} .boat::after{content:"";position:absolute;bottom:-8px;left:10%;width:80%;height:8px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.6),transparent);border-radius:4px;} @keyframes sailing{0%{left:0;transform:translateY(0);} 25%{transform:translateY(-5px);} 50%{left:calc(100% - 70px);transform:translateY(0);} 75%{transform:translateY(-5px);} 100%{left:0;transform:translateY(0);}} .betting-area{background:rgba(255,255,255,0.12);border-radius:20px;padding:25px;margin-bottom:25px;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);} .bet-options{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:20px;} .bet-btn{background:linear-gradient(135deg,rgba(255,255,255,0.2),rgba(255,255,255,0.1));border:2px solid rgba(255,255,255,0.3);color:white;padding:15px;border-radius:12px;cursor:pointer;transition:all 0.4s;text-align:center;font-weight:bold;box-shadow:0 4px 15px rgba(0,0,0,0.1);} .bet-btn:hover{background:linear-gradient(135deg,rgba(255,255,255,0.3),rgba(255,255,255,0.2));transform:translateY(-3px);box-shadow:0 6px 25px rgba(0,0,0,0.2);} .bet-btn.selected{background:linear-gradient(135deg,#00d2d3,#00b8d4);box-shadow:0 6px 25px rgba(0,210,211,0.4);} .amount-input{display:flex;gap:12px;margin-bottom:15px;} .amount-input input{flex:1;padding:15px;border:none;border-radius:12px;font-size:16px;background:rgba(255,255,255,0.9);box-shadow:inset 0 2px 8px rgba(0,0,0,0.1);} .amount-input button{padding:15px 25px;background:linear-gradient(135deg,#00d2d3,#00b8d4);color:white;border:none;border-radius:12px;cursor:pointer;font-weight:bold;box-shadow:0 4px 15px rgba(0,210,211,0.3);transition:all 0.3s;} .amount-input button:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,210,211,0.4);} .result-area{background:rgba(255,255,255,0.12);border-radius:20px;padding:25px;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);} .nav-buttons{display:flex;gap:15px;justify-content:center;margin-top:25px;} .nav-btn{padding:15px 30px;background:linear-gradient(135deg,rgba(255,255,255,0.2),rgba(255,255,255,0.1));color:white;text-decoration:none;border-radius:12px;transition:all 0.3s;border:1px solid rgba(255,255,255,0.3);font-weight:bold;} .nav-btn:hover{background:linear-gradient(135deg,rgba(255,255,255,0.3),rgba(255,255,255,0.2));transform:translateY(-2px);}</style>';
        $html .= '</head><body>';
        
        $html .= '<div class="game-container">';
        $html .= '<div class="header"><h1>⛵ 幸运飞艇</h1><p>房间' . $room . ' | 用户：' . $userinfo['username'] . ' | 余额：¥' . number_format($userinfo['points']) . '</p></div>';
        
        $html .= '<div class="room-info">';
        $html .= '<div><strong>当前期号：</strong>' . date('ymd') . str_pad((string)rand(1, 179), 3, '0', STR_PAD_LEFT) . '</div>';
        $html .= '<div><strong>封盘倒计时：</strong><span id="countdown">02:35</span></div>';
        $html .= '</div>';
        
        $html .= '<div class="period-info">';
        $html .= '<h3>📊 上期开奖结果</h3>';
        $html .= '<div style="display:flex;gap:10px;margin-top:10px;">';
        for ($i = 1; $i <= 10; $i++) {
            $pos = rand(1, 10);
            $html .= '<div style="background:rgba(255,255,255,0.2);width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;">' . $pos . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="water-track">';
        $html .= '<h3>� 航道赛场</h3>';
        for ($i = 1; $i <= 10; $i++) {
            $html .= '<div class="boat" style="animation-delay:' . ($i * 0.2) . 's;"></div>';
        }
        $html .= '</div>';
        
        $html .= '<div class="betting-area">';
        $html .= '<h3>💰 投注区域</h3>';
        $html .= '<div class="bet-options">';
        $positions = ['冠军', '亚军', '季军', '第四', '第五', '大', '小', '单', '双', '龙', '虎'];
        foreach ($positions as $pos) {
            $html .= '<button class="bet-btn" onclick="selectBet(\'' . $pos . '\')">' . $pos . '</button>';
        }
        $html .= '</div>';
        
        $html .= '<div class="amount-input">';
        $html .= '<input type="number" id="betAmount" placeholder="投注金额" min="10" value="100">';
        $html .= '<button onclick="submitBet()">确认投注</button>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="result-area">';
        $html .= '<h3>📈 投注记录</h3>';
        $html .= '<div id="betRecords">暂无投注记录</div>';
        $html .= '</div>';
        
        $html .= '<div class="nav-buttons">';
        $html .= '<a href="/run/fangjian?game=幸运飞艇" class="nav-btn">🏠 返回大厅</a>';
        $html .= '<a href="/user" class="nav-btn">👤 个人中心</a>';
        $html .= '<a href="/" class="nav-btn">🏠 首页</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '<script>';
        $html .= 'let selectedBet = null; let countdown = 155;';
        $html .= 'function selectBet(bet){selectedBet=bet;document.querySelectorAll(".bet-btn").forEach(btn=>{btn.classList.remove("selected");btn.style.background="";});event.target.classList.add("selected");}';
        $html .= 'function submitBet(){if(!selectedBet){alert("请先选择投注项目");return;}const amount=document.getElementById("betAmount").value;if(!amount||amount<10){alert("投注金额最低10元");return;}document.getElementById("betRecords").innerHTML="⛵ 投注成功！"+selectedBet+" - ¥"+amount+" 🌊";alert("🎉 投注成功！\\n⛵ 项目："+selectedBet+"\\n💰 金额：¥"+amount);}';
        $html .= 'function updateCountdown(){const min=Math.floor(countdown/60);const sec=countdown%60;document.getElementById("countdown").textContent=String(min).padStart(2,"0")+":"+String(sec).padStart(2,"0");countdown--;if(countdown<0){countdown=179;}}';
        $html .= 'setInterval(updateCountdown,1000);';
        $html .= '</script>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * SSC时时彩游戏
     */
    public function ssc()
    {
        // 获取房间参数
        $room = input('param.room', '1');
        
        // 模拟用户信息
        $userinfo = [
            'id' => 1,
            'username' => '游客',
            'points' => 10000,
            'headimgurl' => '/images/default.png'
        ];
        
        // 时时彩游戏页面HTML
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>时时彩SSC</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>*{margin:0;padding:0;box-sizing:border-box;} body{font-family:Arial;background:linear-gradient(135deg,#8360c3,#2ebf91);color:white;min-height:100vh;} .game-container{padding:20px;max-width:800px;margin:0 auto;} .header{background:rgba(255,255,255,0.1);border-radius:15px;padding:20px;margin-bottom:20px;text-align:center;} .period-info{background:rgba(255,255,255,0.15);border-radius:12px;padding:15px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;} .lottery-balls{display:flex;gap:15px;justify-content:center;margin:20px 0;} .ball{width:50px;height:50px;border-radius:50%;background:linear-gradient(45deg,#ff6b6b,#ee5a24);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:bold;color:white;animation:bounce 1s ease-in-out infinite;} @keyframes bounce{0%,100%{transform:translateY(0);} 50%{transform:translateY(-10px);}} .betting-panel{background:rgba(255,255,255,0.1);border-radius:15px;padding:20px;margin-bottom:20px;} .bet-section{margin-bottom:20px;} .bet-title{font-size:16px;font-weight:600;margin-bottom:10px;color:#ffeaa7;} .bet-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(60px,1fr));gap:8px;} .bet-number{background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.3);color:white;padding:10px;border-radius:8px;cursor:pointer;text-align:center;transition:all 0.3s;font-weight:bold;} .bet-number:hover,.bet-number.selected{background:rgba(0,210,211,0.8);border-color:#00d2d3;transform:scale(1.05);} .bet-input{display:flex;gap:10px;margin-top:15px;} .bet-input input{flex:1;padding:12px;border:none;border-radius:8px;font-size:16px;} .bet-input button{padding:12px 20px;background:#00d2d3;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;} .result-history{background:rgba(255,255,255,0.1);border-radius:15px;padding:20px;margin-bottom:20px;} .history-item{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.2);} .nav-buttons{display:flex;gap:10px;justify-content:center;margin-top:20px;} .nav-btn{padding:12px 24px;background:rgba(255,255,255,0.2);color:white;text-decoration:none;border-radius:8px;transition:all 0.3s;} .nav-btn:hover{background:rgba(255,255,255,0.3);}</style>';
        $html .= '</head><body>';
        
        $html .= '<div class="game-container">';
        $html .= '<div class="header"><h1>⏰ 时时彩SSC</h1><p>房间' . $room . ' | 用户：' . $userinfo['username'] . ' | 余额：¥' . number_format($userinfo['points']) . '</p></div>';
        
        $html .= '<div class="period-info">';
        $html .= '<div><strong>当前期号：</strong>' . date('ymd') . str_pad((string)rand(1, 1440), 4, '0', STR_PAD_LEFT) . '</div>';
        $html .= '<div><strong>封盘倒计时：</strong><span id="countdown">01:45</span></div>';
        $html .= '</div>';
        
        $html .= '<div style="text-align:center;margin:20px 0;">';
        $html .= '<h3>🎱 上期开奖号码</h3>';
        $html .= '<div class="lottery-balls">';
        for ($i = 0; $i < 5; $i++) {
            $num = rand(0, 9);
            $html .= '<div class="ball" style="animation-delay:' . ($i * 0.2) . 's;">' . $num . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="betting-panel">';
        $html .= '<h3>💰 投注面板</h3>';
        
        // 万位投注
        $html .= '<div class="bet-section">';
        $html .= '<div class="bet-title">万位</div>';
        $html .= '<div class="bet-grid">';
        for ($i = 0; $i <= 9; $i++) {
            $html .= '<button class="bet-number" onclick="selectNumber(\'万\', ' . $i . ')">' . $i . '</button>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        // 千位投注
        $html .= '<div class="bet-section">';
        $html .= '<div class="bet-title">千位</div>';
        $html .= '<div class="bet-grid">';
        for ($i = 0; $i <= 9; $i++) {
            $html .= '<button class="bet-number" onclick="selectNumber(\'千\', ' . $i . ')">' . $i . '</button>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        // 百位投注
        $html .= '<div class="bet-section">';
        $html .= '<div class="bet-title">百位</div>';
        $html .= '<div class="bet-grid">';
        for ($i = 0; $i <= 9; $i++) {
            $html .= '<button class="bet-number" onclick="selectNumber(\'百\', ' . $i . ')">' . $i . '</button>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        // 特殊投注
        $html .= '<div class="bet-section">';
        $html .= '<div class="bet-title">特殊玩法</div>';
        $html .= '<div class="bet-grid" style="grid-template-columns:repeat(4,1fr);">';
        $special = ['大', '小', '单', '双'];
        foreach ($special as $item) {
            $html .= '<button class="bet-number" onclick="selectSpecial(\'' . $item . '\')">' . $item . '</button>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="bet-input">';
        $html .= '<input type="number" id="betAmount" placeholder="投注金额" min="10" value="100">';
        $html .= '<button onclick="submitBet()">确认投注</button>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="result-history">';
        $html .= '<h3>📊 投注记录</h3>';
        $html .= '<div id="betHistory">暂无投注记录</div>';
        $html .= '</div>';
        
        $html .= '<div class="nav-buttons">';
        $html .= '<a href="/run/fangjian?game=ssc" class="nav-btn">🏠 返回大厅</a>';
        $html .= '<a href="/user" class="nav-btn">👤 个人中心</a>';
        $html .= '<a href="/" class="nav-btn">🏠 首页</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '<script>';
        $html .= 'let selectedBets = []; let countdown = 105;';
        $html .= 'function selectNumber(position, number){const bet=position+":"+number;if(selectedBets.includes(bet)){selectedBets=selectedBets.filter(b=>b!==bet);event.target.classList.remove("selected");}else{selectedBets.push(bet);event.target.classList.add("selected");}}';
        $html .= 'function selectSpecial(type){if(selectedBets.includes(type)){selectedBets=selectedBets.filter(b=>b!==type);event.target.classList.remove("selected");}else{selectedBets.push(type);event.target.classList.add("selected");}}';
        $html .= 'function submitBet(){if(selectedBets.length===0){alert("请先选择投注号码");return;}const amount=document.getElementById("betAmount").value;if(!amount||amount<10){alert("投注金额最低10元");return;}document.getElementById("betHistory").innerHTML="投注成功！选择："+selectedBets.join(", ")+" - ¥"+amount;alert("投注成功！\\n选择："+selectedBets.join(", ")+"\\n金额：¥"+amount);selectedBets=[];document.querySelectorAll(".bet-number").forEach(btn=>btn.classList.remove("selected"));}';
        $html .= 'function updateCountdown(){const min=Math.floor(countdown/60);const sec=countdown%60;document.getElementById("countdown").textContent=String(min).padStart(2,"0")+":"+String(sec).padStart(2,"0");countdown--;if(countdown<0){countdown=119;}}';
        $html .= 'setInterval(updateCountdown,1000);';
        $html .= '</script>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * 启动北京28游戏
     */
    public function bj28()
    {
        // 获取房间参数
        $room = input('param.room', '1');
        $fangjian = input('param.fangjian', $room);
        
        // 模拟用户信息（游客模式）
        $userinfo = [
            'id' => 1,
            'username' => '游客',
            'points' => 10000,
            'headimgurl' => '/images/default.png'
        ];
        
        // 模拟当前期号和开奖信息
        $current = [
            'periodnumber' => date('ymd') . str_pad((string)rand(1, 288), 3, '0', STR_PAD_LEFT),
            'awardtime' => date('Y-m-d H:i:s'),
            'numberOne' => rand(0, 9),
            'numberTwo' => rand(0, 9), 
            'numberThree' => rand(0, 9),
        ];
        
        // 计算和值
        $current['tema'] = $current['numberOne'] + $current['numberTwo'] + $current['numberThree'];
        $current['tema_ds'] = ($current['tema'] % 2 == 0) ? '双' : '单';
        $current['tema_dx'] = ($current['tema'] >= 14) ? '大' : '小';
        
        // 房间配置
        $room_config = [
            '1' => ['min' => 100, 'max' => 10000, 'name' => '初级房间'],
            '2' => ['min' => 1000, 'max' => 50000, 'name' => '中级房间'], 
            '3' => ['min' => 5000, 'max' => 100000, 'name' => '高级房间']
        ];
        
        $current_room = $room_config[$room] ?? $room_config['1'];
        
        // 返回完整的北京28游戏页面
        return $this->renderBj28GamePage($current, $userinfo, $current_room, $fangjian);
    }
    
    private function renderBj28GamePage($current, $userinfo, $room, $fangjian)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<title>🎲 北京28 - ' . $room['name'] . '</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>
            body{font-family:"Microsoft YaHei",Arial;background:#0f1419;margin:0;color:#fff;overflow-x:hidden;}
            .header{background:linear-gradient(135deg,#1e2832,#2d3748);padding:15px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 10px rgba(0,0,0,0.3);}
            .back-btn{color:#64b5f6;text-decoration:none;font-size:16px;display:flex;align-items:center;}
            .user-info{display:flex;align-items:center;gap:15px;font-size:14px;}
            .user-avatar{width:40px;height:40px;border-radius:50%;background:#64b5f6;}
            .current-draw{background:linear-gradient(135deg,#667eea,#764ba2);margin:10px;padding:20px;border-radius:15px;text-align:center;color:white;}
            .draw-number{font-size:24px;font-weight:700;margin-bottom:10px;}
            .draw-result{display:flex;justify-content:center;align-items:center;gap:10px;font-size:18px;margin:15px 0;}
            .number-ball{background:#fff;color:#333;width:45px;height:45px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:20px;}
            .result-sum{background:#ff4757;color:white;padding:8px 15px;border-radius:20px;font-weight:700;}
            .countdown{background:rgba(255,255,255,0.1);padding:10px 20px;border-radius:25px;margin:20px 10px;text-align:center;backdrop-filter:blur(10px);}
            .betting-area{margin:20px 10px;background:rgba(255,255,255,0.05);border-radius:15px;padding:20px;backdrop-filter:blur(10px);}
            .bet-section{margin-bottom:25px;}
            .bet-title{color:#64b5f6;font-size:16px;font-weight:600;margin-bottom:15px;display:flex;align-items:center;gap:8px;}
            .bet-buttons{display:flex;flex-wrap:wrap;gap:10px;}
            .bet-btn{background:linear-gradient(135deg,#4a5568,#2d3748);border:none;padding:12px 20px;border-radius:10px;color:white;font-size:14px;font-weight:600;cursor:pointer;transition:all 0.3s;min-width:80px;}
            .bet-btn:hover{background:linear-gradient(135deg,#64b5f6,#667eea);transform:translateY(-2px);}
            .bet-btn.active{background:linear-gradient(135deg,#48bb78,#38a169);box-shadow:0 4px 15px rgba(72,187,120,0.4);}
            .bet-input-area{background:rgba(255,255,255,0.05);padding:20px;border-radius:10px;margin-top:20px;}
            .amount-input{background:rgba(255,255,255,0.1);border:2px solid #4a5568;border-radius:10px;padding:12px 15px;color:white;font-size:16px;width:120px;text-align:center;}
            .amount-input:focus{outline:none;border-color:#64b5f6;}
            .quick-amounts{display:flex;gap:10px;margin:10px 0;flex-wrap:wrap;}
            .quick-amount{background:rgba(100,181,246,0.2);border:1px solid #64b5f6;color:#64b5f6;padding:8px 15px;border-radius:20px;cursor:pointer;font-size:12px;}
            .quick-amount:hover{background:#64b5f6;color:white;}
            .submit-btn{background:linear-gradient(135deg,#ed4956,#f093fb);border:none;padding:15px 30px;border-radius:25px;color:white;font-size:16px;font-weight:700;cursor:pointer;width:100%;margin-top:15px;transition:all 0.3s;}
            .submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(237,73,86,0.4);}
            .history{background:rgba(255,255,255,0.05);margin:20px 10px;border-radius:15px;padding:20px;backdrop-filter:blur(10px);}
            .history-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.1);}
            .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:linear-gradient(135deg,#1e2832,#2d3748);padding:15px;display:flex;justify-content:space-around;box-shadow:0 -2px 10px rgba(0,0,0,0.3);}
            .nav-item{color:#64b5f6;text-decoration:none;text-align:center;font-size:12px;}
        </style></head><body>';
        
        // 头部
        $html .= '<div class="header">';
        $html .= '<a href="/run/fangjian?game=bj28" class="back-btn">← 返回房间</a>';
        $html .= '<div class="user-info">';
        $html .= '<div class="user-avatar"></div>';
        $html .= '<div>👤 ' . $userinfo['username'] . '<br>💰 ¥' . number_format($userinfo['points']) . '</div>';
        $html .= '</div></div>';
        
        // 当前开奖
        $html .= '<div class="current-draw">';
        $html .= '<div class="draw-number">第 ' . $current['periodnumber'] . ' 期</div>';
        $html .= '<div class="draw-result">';
        $html .= '<div class="number-ball">' . $current['numberOne'] . '</div>';
        $html .= '<div style="font-size:24px;">+</div>';
        $html .= '<div class="number-ball">' . $current['numberTwo'] . '</div>';
        $html .= '<div style="font-size:24px;">+</div>';
        $html .= '<div class="number-ball">' . $current['numberThree'] . '</div>';
        $html .= '<div style="font-size:24px;">=</div>';
        $html .= '<div class="result-sum">' . $current['tema'] . '</div>';
        $html .= '</div>';
        $html .= '<div style="margin-top:10px;">' . $current['tema_ds'] . ' · ' . $current['tema_dx'] . '</div>';
        $html .= '</div>';
        
        // 倒计时
        $html .= '<div class="countdown">⏰ 距离下期开奖还有：<span id="countdown">02:45</span></div>';
        
        // 投注区域
        $html .= '<div class="betting-area">';
        $html .= '<div class="bet-section">';
        $html .= '<div class="bet-title">🎯 大小单双</div>';
        $html .= '<div class="bet-buttons">';
        $html .= '<button class="bet-btn" onclick="selectBet(\'大\', this)">大 (14-27)</button>';
        $html .= '<button class="bet-btn" onclick="selectBet(\'小\', this)">小 (0-13)</button>';
        $html .= '<button class="bet-btn" onclick="selectBet(\'单\', this)">单</button>';
        $html .= '<button class="bet-btn" onclick="selectBet(\'双\', this)">双</button>';
        $html .= '</div></div>';
        
        $html .= '<div class="bet-section">';
        $html .= '<div class="bet-title">🎲 组合玩法</div>';
        $html .= '<div class="bet-buttons">';
        $html .= '<button class="bet-btn" onclick="selectBet(\'大单\', this)">大单</button>';
        $html .= '<button class="bet-btn" onclick="selectBet(\'大双\', this)">大双</button>';
        $html .= '<button class="bet-btn" onclick="selectBet(\'小单\', this)">小单</button>';
        $html .= '<button class="bet-btn" onclick="selectBet(\'小双\', this)">小双</button>';
        $html .= '</div></div>';
        
        // 投注金额
        $html .= '<div class="bet-input-area">';
        $html .= '<div style="margin-bottom:15px;color:#64b5f6;font-weight:600;">💰 投注金额</div>';
        $html .= '<div style="display:flex;align-items:center;gap:15px;margin-bottom:10px;">';
        $html .= '<input type="number" id="betAmount" class="amount-input" value="' . $room['min'] . '" min="' . $room['min'] . '" max="' . $room['max'] . '" placeholder="金额">';
        $html .= '<div style="font-size:12px;color:#a0a0a0;">限额: ¥' . number_format($room['min']) . ' - ¥' . number_format($room['max']) . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="quick-amounts">';
        $amounts = [$room['min'], $room['min'] * 5, $room['min'] * 10, $room['min'] * 20];
        foreach ($amounts as $amount) {
            $html .= '<div class="quick-amount" onclick="setAmount(' . $amount . ')">' . number_format($amount) . '</div>';
        }
        $html .= '</div>';
        
        $html .= '<button class="submit-btn" onclick="submitBet()">🚀 确认投注</button>';
        $html .= '</div></div>';
        
        // 历史记录
        $html .= '<div class="history">';
        $html .= '<div style="color:#64b5f6;font-size:16px;font-weight:600;margin-bottom:15px;">📊 最近开奖</div>';
        for ($i = 0; $i < 5; $i++) {
            $period = date('ymd') . str_pad((string)rand(1, 288), 3, '0', STR_PAD_LEFT);
            $n1 = rand(0, 9); $n2 = rand(0, 9); $n3 = rand(0, 9); $sum = $n1 + $n2 + $n3;
            $html .= '<div class="history-item">';
            $html .= '<div>' . $period . '</div>';
            $html .= '<div>' . $n1 . '+' . $n2 . '+' . $n3 . '=' . $sum . '</div>';
            $html .= '<div>' . (($sum % 2 == 0) ? '双' : '单') . ' ' . (($sum >= 14) ? '大' : '小') . '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // 底部导航
        $html .= '<div class="bottom-nav">';
        $html .= '<a href="/simple/index" class="nav-item">🏠<br>首页</a>';
        $html .= '<a href="/run/fangjian?game=bj28" class="nav-item">🚪<br>房间</a>';
        $html .= '<a href="#" class="nav-item">📊<br>记录</a>';
        $html .= '<a href="#" class="nav-item">👤<br>我的</a>';
        $html .= '</div>';
        
        $html .= '<div style="height:80px;"></div>'; // 底部间距
        
        // JavaScript
        $html .= '<script>
        let selectedBet = "";
        let countdownTime = 165; // 2分45秒
        
        function selectBet(betType, btn) {
            document.querySelectorAll(".bet-btn").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            selectedBet = betType;
        }
        
        function setAmount(amount) {
            document.getElementById("betAmount").value = amount;
        }
        
        function submitBet() {
            const amount = parseInt(document.getElementById("betAmount").value);
            
            if (!selectedBet) {
                alert("❌ 请选择投注类型！");
                return;
            }
            
            if (!amount || amount < ' . $room['min'] . ' || amount > ' . $room['max'] . ') {
                alert("❌ 投注金额必须在 ¥' . number_format($room['min']) . ' - ¥' . number_format($room['max']) . ' 之间！");
                return;
            }
            
            if (confirm("确认投注？\\n类型: " + selectedBet + "\\n金额: ¥" + amount.toLocaleString())) {
                alert("🎉 投注成功！\\n" + selectedBet + " ¥" + amount.toLocaleString() + "\\n祝您好运！");
                
                // 重置选择
                selectedBet = "";
                document.querySelectorAll(".bet-btn").forEach(b => b.classList.remove("active"));
            }
        }
        
        function updateCountdown() {
            const minutes = Math.floor(countdownTime / 60);
            const seconds = countdownTime % 60;
            document.getElementById("countdown").textContent = 
                String(minutes).padStart(2, "0") + ":" + String(seconds).padStart(2, "0");
            
            if (countdownTime > 0) {
                countdownTime--;
            } else {
                countdownTime = 180; // 重置为3分钟
                // 这里可以添加新开奖的逻辑
            }
        }
        
        setInterval(updateCountdown, 1000);
        </script>';
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * 启动加拿大28游戏
     */
    public function jnd28()
    {
        return $this->runGame('jnd28', 'WorkermanJnd28');
    }
    
    /**
     * 启动新疆28游戏
     */
    public function xjp28()
    {
        return $this->runGame('xjp28', 'WorkermanXjp28');
    }
    
    /**
     * 启动六合彩游戏
     */
    public function lhc()
    {
        return $this->runGame('lhc', 'WorkermanLhc');
    }
    
    /**
     * 启动幸运飞艇游戏
     */
    public function xyft()
    {
        return $this->runGame('xyft', 'WorkermanXyft');
    }
    
    /**
     * 通用游戏启动方法
     */
    private function runGame($game, $controller)
    {
        try {
            // 检查进程是否已经运行
            $pid_file = "/tmp/{$game}_workerman.pid";
            if (file_exists($pid_file)) {
                $pid = file_get_contents($pid_file);
                if (posix_kill($pid, 0)) {
                    return json(['code' => 0, 'msg' => "{$game}游戏已在运行中"]);
                }
            }
            
            // 启动Workerman进程
            $class = "\\app\\controller\\{$controller}";
            if (class_exists($class)) {
                $worker = new $class();
                $worker->start();
                
                return json(['code' => 1, 'msg' => "{$game}游戏启动成功"]);
            } else {
                return json(['code' => 0, 'msg' => "控制器{$controller}不存在"]);
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '启动失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 停止游戏
     */
    public function stop()
    {
        $game = request()->param('game');
        
        if (!$game) {
            return json(['code' => 0, 'msg' => '请指定游戏类型']);
        }
        
        try {
            $pid_file = "/tmp/{$game}_workerman.pid";
            if (file_exists($pid_file)) {
                $pid = file_get_contents($pid_file);
                if (posix_kill($pid, SIGTERM)) {
                    unlink($pid_file);
                    return json(['code' => 1, 'msg' => "{$game}游戏停止成功"]);
                }
            }
            
            return json(['code' => 0, 'msg' => "{$game}游戏未在运行"]);
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '停止失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 获取游戏状态
     */
    public function status()
    {
        $games = ['幸运飞艇', 'ssc', 'bj28', 'jnd28', 'xjp28', 'lhc', 'xyft'];
        $status = [];
        
        foreach ($games as $game) {
            $pid_file = "/tmp/{$game}_workerman.pid";
            $running = false;
            
            if (file_exists($pid_file)) {
                $pid = file_get_contents($pid_file);
                $running = posix_kill($pid, 0);
            }
            
            $status[$game] = [
                'running' => $running,
                'current_issue' => Cache::get($game . '_current_issue'),
                'next_time' => Cache::get($game . '_next_time'),
                'status' => Cache::get($game . '_status', 'waiting')
            ];
        }
        
        return json(['code' => 1, 'data' => $status]);
    }
    
    /**
     * 房间选择页面
     */
    public function fangjian($game = '')
    {
        // 获取游戏参数
        $game = $game ?: input('param.game', 'bj28');
        
        // 直接返回HTML，不使用模板
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>房间选择 - ' . $game . '</title>';
        $html .= '<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}';
        $html .= '.room{background:white;margin:10px 0;padding:15px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}';
        $html .= '.room h3{margin:0 0 10px 0;color:#333;}</style></head><body>';
        $html .= '<h1>🎮 ' . strtoupper($game) . ' - 房间选择</h1>';
        $html .= '<p>👤 用户余额：<strong>¥10,000</strong></p>';
        
        $html .= '<div class="room"><h3>🏠 初级房间</h3>';
        $html .= '<p>投注限额：¥100 - ¥10,000</p>';
        $html .= '<p>👥 在线人数：' . rand(100, 300) . '</p>';
        $html .= '<a href="/run/' . $game . '?room=1" style="background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">进入房间</a></div>';
        
        $html .= '<div class="room"><h3>🏢 中级房间</h3>';
        $html .= '<p>投注限额：¥1,000 - ¥50,000</p>';
        $html .= '<p>👥 在线人数：' . rand(100, 300) . '</p>';
        $html .= '<a href="/run/' . $game . '?room=2" style="background:#ffc107;color:black;padding:10px 20px;text-decoration:none;border-radius:5px;">进入房间</a></div>';
        
        $html .= '<div class="room"><h3>🏰 高级房间</h3>';
        $html .= '<p>投注限额：¥5,000 - ¥100,000</p>';
        $html .= '<p>👥 在线人数：' . rand(100, 300) . '</p>';
        $html .= '<a href="/run/' . $game . '?room=3" style="background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">进入房间</a></div>';
        
        $html .= '<p style="margin-top:30px;"><a href="/">← 返回首页</a></p>';
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * 历史走势页面
     */
    public function trend()
    {
        // 模拟历史开奖数据 - PK10幸运飞艇和时时彩
        $kjlist = [
            [
                'game' => 'pk10',
                'gameName' => '幸运飞艇',
                'periodnumber' => date('ymd') . '001', 
                'numbers' => [
                    rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10),
                    rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10)
                ]
            ],
            [
                'game' => 'pk10',
                'gameName' => '幸运飞艇',
                'periodnumber' => date('ymd') . '002',
                'numbers' => [
                    rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10),
                    rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10), rand(1, 10)
                ]
            ],
            [
                'game' => 'ssc',
                'gameName' => '时时彩',
                'periodnumber' => date('ymd') . '003',
                'numbers' => [
                    rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9)
                ]
            ],
            [
                'game' => 'ssc',
                'gameName' => '时时彩',
                'periodnumber' => date('ymd') . '004',
                'numbers' => [
                    rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9)
                ]
            ]
        ];

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>历史走势</title>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
        $html .= '<style>';
        $html .= '* { margin: 0; padding: 0; box-sizing: border-box; }';
        $html .= 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", sans-serif; background: #f5f7fa; color: #333; padding-bottom: calc(70px + env(safe-area-inset-bottom)); }';
        
        // 标题栏样式 - 与其他页面统一
        $html .= '.nav { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }';
        $html .= '.nav h3 { font-size: 20px; margin: 0; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; }';
        
        // 内容区域样式
        $html .= '.container { max-width: 800px; margin: 0 auto; padding: 15px; }';
        $html .= '.lottery_list2 { margin-top: 0; }';
        
        // 卡片样式 - 现代化设计
        $html .= '.lottery_list2 .item { background: white; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); border: none; position: relative; transition: all 0.3s ease; }';
        $html .= '.lottery_list2 .item:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.12); }';
        
        // 游戏名称和期号
        $html .= '.lottery_list2 .item .m { margin-bottom: 15px; }';
        $html .= '.lottery_list2 .item .m b { font-size: 16px; color: #2c3e50; margin-right: 8px; font-weight: 600; }';
        $html .= '.red { color: #e74c3c; font-weight: 600; }';
        
        // 号码展示区域 - 优化视觉效果
        $html .= '.lottery_list2 .item .f { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }';
        $html .= '.lottery_list2 .item .f b { display: inline-flex; align-items: center; justify-content: center; font-size: 15px; color: #fff; width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); box-shadow: 0 2px 6px rgba(79,172,254,0.25); font-weight: 600; }';
        $html .= '.lottery_list2 .item .f .jh { color: #999; background: none !important; box-shadow: none !important; font-weight: 400; font-size: 14px; width: auto !important; height: auto !important; border-radius: 0 !important; }';
        
        // PK10号码样式 - 前三名
        $html .= '.lottery_list2 .item .f b:nth-child(1) { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); }'; // 冠军-金色
        $html .= '.lottery_list2 .item .f b:nth-child(3) { background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%); }'; // 亚军-银色  
        $html .= '.lottery_list2 .item .f b:nth-child(5) { background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%); }'; // 季军-铜色
        
        // 时时彩号码样式
        $html .= '.lottery_list2 .item .f b { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }';
        $html .= '.lottery_list2 .item .f b:last-of-type { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); box-shadow: 0 2px 8px rgba(255,154,158,0.3); }';
        
        // 详情按钮 - 现代化样式
        $html .= '.trend_btn { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); padding: 8px 16px; border-radius: 20px; border: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.3s; box-shadow: 0 2px 8px rgba(102,126,234,0.3); }';
        $html .= '.trend_btn:hover { transform: translateY(-50%) scale(1.05); box-shadow: 0 4px 15px rgba(102,126,234,0.4); }';
        
        // 响应式设计
        $html .= '@media (max-width: 768px) {';
        $html .= '  .container { padding: 10px; }';
        $html .= '  .lottery_list2 .item { padding: 15px; }';
        $html .= '  .lottery_list2 .item .f b { width: 32px; height: 32px; font-size: 14px; }';
        $html .= '  .trend_btn { position: static; transform: none; margin-top: 15px; display: inline-block; }';
        $html .= '  .lottery_list2 .item { padding-right: 15px; }';
        $html .= '}';
        
        $html .= '</style>';
        $html .= '</head><body>';

        $html .= '<div class="nav"><h3>📈 历史走势</h3></div>';
        
        $html .= '<div class="container">';
        $html .= '<div class="lottery_list2">';
        foreach ($kjlist as $item) {
            $html .= '<div class="item">';
            $html .= '<div class="m"><b>' . $item['gameName'] . '</b>第<span class="red">' . $item['periodnumber'] . '</span>期</div>';
            $html .= '<div class="f">';
            
            // 根据游戏类型显示不同的开奖号码格式
            if ($item['game'] == 'pk10') {
                // PK10显示前3名的号码
                for ($i = 0; $i < 3; $i++) {
                    $html .= '<b>' . $item['numbers'][$i] . '</b>';
                    if ($i < 2) $html .= '<span class="jh">-</span>';
                }
                $html .= '<span class="jh" style="margin: 0 8px;">|</span>';
                $html .= '<span style="font-size: 12px; color: #666;">冠亚军和: </span>';
                $html .= '<b style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">' . ($item['numbers'][0] + $item['numbers'][1]) . '</b>';
            } else if ($item['game'] == 'ssc') {
                // 时时彩显示5位数字
                foreach ($item['numbers'] as $index => $number) {
                    $html .= '<b>' . $number . '</b>';
                    if ($index < count($item['numbers']) - 1) {
                        $html .= '<span class="jh">-</span>';
                    }
                }
            }
            
            $html .= '</div>';
            $html .= '<a href="/run/trend1?game=' . $item['game'] . '" class="trend_btn">详情</a>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';

        // 添加原始底部导航
        $html .= '<nav class="bottom-nav">';
        $html .= '<a href="/" class="nav-item"><img src="/images/menu1_hui.png" alt="首页"><span>首页</span></a>';
        $html .= '<a href="/run" class="nav-item"><img src="/images/pay_hui.png" alt="产品"><span>产品</span></a>';
        $html .= '<a href="#" class="nav-item"><img src="/images/menu3.png" alt="客服"><span>客服</span></a>';
        $html .= '<a href="/run/trend" class="nav-item active"><img src="/images/menu2_red.png" alt="走势"><span>走势</span></a>';
        $html .= '<a href="/user" class="nav-item"><img src="/images/menu5.png" alt="我的"><span>我的</span></a>';
        $html .= '<div class="safe-area"></div>';
        $html .= '</nav>';
        
        // 添加导航样式
        $html .= '<style>';
        $html .= 'body{padding-bottom:calc(60px + env(safe-area-inset-bottom));}';
        $html .= '.bottom-nav{position:fixed;left:0;right:0;bottom:0;height:60px;background:rgba(0,0,0,0.85);border-top:1px solid rgba(255,255,255,0.12);backdrop-filter:blur(8px);display:flex;align-items:stretch;justify-content:space-around;z-index:2147483647;pointer-events:auto;}';
        $html .= '.bottom-nav .nav-item{flex:1;text-align:center;text-decoration:none;color:rgba(255,255,255,0.85);display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:12px;-webkit-tap-highlight-color:transparent;}';
        $html .= '.bottom-nav .nav-item img{width:22px;height:22px;display:block;margin-bottom:2px;filter:grayscale(100%) opacity(0.75);pointer-events:none;}';
        $html .= '.bottom-nav .nav-item:hover{color:#fff;}';
        $html .= '.bottom-nav .nav-item.active{color:#fff;}';
        $html .= '.bottom-nav .nav-item.active img{filter:none;}';
        $html .= '.bottom-nav .safe-area{position:absolute;left:0;right:0;bottom:0;height:env(safe-area-inset-bottom);background:rgba(0,0,0,0.85);pointer-events:none;}';
        $html .= '</style>';

        $html .= '</body></html>';
        
        return $html;
    }
}