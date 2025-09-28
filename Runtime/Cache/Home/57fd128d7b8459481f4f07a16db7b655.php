<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title><?php echo C('sitename');?></title>

    <!-- 引入样式 -->
    <link rel="stylesheet" href="/images/css/common.css"/>
    <link rel="stylesheet" href="/images/css/swiper-3.4.2.min.css"/>
    <link rel="stylesheet" href="/images/css/index.css"/>
    <!-- 公用js 自适应js-->
    <script src="/images/js/sizeChange.js"></script>
    <style>
        * { box-sizing: border-box; }
        
        body { 
            margin: 0; 
            padding: 0; 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Microsoft YaHei', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            padding: 60px 0 40px 0;
        }
        
        .header h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 40px;
        }
        
        .back-btn {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        
        .product-card {
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            max-width: 520px;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        
        .product-icon {
            font-size: 22px;
            margin-bottom: 6px;
            text-align: center;
        }
        
        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
        }
        
        .product-desc {
            color: #666;
            line-height: 1.5;
            margin-bottom: 6px;
            font-size: 12px;
        }
        
        .product-features {
            list-style: none;
            padding: 0;
            margin-bottom: 8px;
        }
        
        .product-features li {
            padding: 0;
            color: #555;
            font-size: 12px;
            line-height: 18px;
        }
        
        .product-features li:before {
            content: "✓ ";
            color: #52c41a;
            font-weight: bold;
            margin-right: 6px;
            font-size: 12px;
        }
        
        .product-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .product-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .coming-soon {
            opacity: 0.7;
        }
        
        .coming-soon .product-btn {
            background: #ccc;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .container {
                padding: 15px;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Microsoft YaHei', sans-serif;">
<input type="hidden" id="user_id" value="2434"/>
<input id="loginType" type="text" style="display: none" value=""/>
<input type="hidden" id="user_nickname" value="555555"/>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; padding-bottom: 110px;">
    <!-- 公告 start-->
    <div style="width:100%;height:32px;padding: .05rem .2rem .05rem .1rem;background: rgba(255,255,255,0.95);float: left;border-radius: 10px;margin-bottom: 20px;box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="width:8%;float:left;font-size:0.23rem;text-align:center;line-height:22px;color: #000;">
            <img src="/images/laba.png" style="width: .27rem;height: .25rem;margin-top: .08rem;">
        </div>
        <div style="width:92%;float:left;font-size:0.23rem;color:#000;line-height:22px;">
            <marquee><?php echo ($gdxx["content"]); ?></marquee>
        </div>
    </div>
    <!-- 通栏轮播图 start-->
    <div class="swiper-container banner" style="border-radius: 20px;overflow: hidden;box-shadow: 0 10px 30px rgba(0,0,0,0.2);margin-bottom: 20px;">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="/images/lun2.png" style="border-radius: .2rem;"></div>
            <div class="swiper-slide"><img src="/images/lun3.png" style="border-radius: .2rem;"></div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>

    <div class="scroll_text">
        <div class="swiper-container scroll_text1">
            <div class="swiper-wrapper">
                <?php if(is_array($scroll)): foreach($scroll as $key=>$vo): ?><div class="swiper-slide">
                    <span><?php echo ($vo[0]); ?></span>
                    <span><?php echo ($vo[1]); ?></span>
                    <span><?php echo ($vo[2]); ?></span>
                </div><?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <!-- 产品展示 start-->
    <div class="products-grid" style="display: grid;grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));gap: 15px;margin-top: 20px;">
            <div class="product-card">
                <div class="product-icon">🔗</div>
                <div class="product-title">螞蟻收益解决方案</div>
                <div class="product-desc">
                    基于蚂蚁链技术的企业级螞蟻收益解决方案，提供安全、高效、可扩展的螞蟻收益基础设施。
                </div>
                <ul class="product-features">
                    <li>高性能TPS处理能力</li>
                    <li>企业级安全保障</li>
                    <li>一站式开发工具</li>
                    <li>丰富的应用场景</li>
                </ul>
                <a href="<?php echo U('Home/Run/fangjian/game/bj28');?>" style="display: block; text-decoration: none;">
                    <button class="product-btn">了解更多</button>
                </a>
            </div>
            
            <div class="product-card">
                <div class="product-icon">🔒</div>
                <div class="product-title">螞蟻收益平台</div>
                <div class="product-desc">
                    先进的隐私保护计算技术，在保护数据隐私的同时实现多方安全计算和数据价值挖掘。
                </div>
                <ul class="product-features">
                    <li>多方安全计算</li>
                    <li>联邦学习支持</li>
                    <li>差分隐私保护</li>
                    <li>数据不出域</li>
                </ul>
                <a href="<?php echo U('Home/Run/fangjian/game/jnd28');?>" style="display: block; text-decoration: none;">
                    <button class="product-btn">了解更多</button>
                </a>
            </div>

    </div>
</div>
<!-- <div class="tuichuup" style="display:none">
    <div class="tuichu" id="tuichu">
        <span id="tuichuT" class="tuichuT" 
            style="font-size: 0.25rem; color: rgb(255, 255, 255); margin: 0.12rem 0.13rem;">
            退
        </span>
    </div>
</div> -->

<?php $a=6;?>
<nav class="bottom-nav">
    <a href="/index.php/Home/Shou/index" class="nav-item <?php echo ($a==1?'active':''); ?>">
        <img src="/images/menu1<?php echo ($a==1?'':'_hui'); ?>.png" alt="首页">
        <span>首页</span>
    </a>
    <a href="/index.php/Home/Run/index" class="nav-item <?php echo ($a==6?'active':''); ?>">
        <img src="/images/pay.png" alt="产品">
        <span>产品</span>
    </a>
    <a href="<?php echo U('Home/Run/trend');?>" class="nav-item <?php echo ($a==2?'active':''); ?>">
        <img src="/images/menu2<?php echo ($a==2?'_red':''); ?>.png" alt="走势">
        <span>走势</span>
    </a>
    <a href="<?php echo C('zxkf');?>" class="nav-item">
        <img src="/images/menu3.png" alt="客服">
        <span>客服</span>
    </a>
    <a href="<?php echo U('Home/Run/history');?>" class="nav-item <?php echo ($a==4?'active':''); ?>">
        <img src="/images/menu4<?php echo ($a==4?'_red':''); ?>.png" alt="购买">
        <span>购买</span>
    </a>
    <a href="<?php echo U('Home/User/index');?>" class="nav-item <?php echo ($a==5?'active':''); ?>">
        <img src="/images/menu5<?php echo ($a==5?'_red':''); ?>.png" alt="我的">
        <span>我的</span>
    </a>
    <div class="safe-area"></div>
    <!-- iOS 安全区占位 -->
</nav>

<style>
    /* 预留底部导航空间，包含安全区 */
    body { padding-bottom: calc(60px + env(safe-area-inset-bottom)); }

    .bottom-nav {
        position: fixed;
        left: 0; right: 0; bottom: 0;
        height: 60px;
        background: rgba(0,0,0,0.85);
        border-top: 1px solid rgba(255,255,255,0.12);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: stretch;
        justify-content: space-around;
        z-index: 2147483647; /* 提升层级，确保在一切覆盖层之上 */
        pointer-events: auto;
    }

    .bottom-nav .nav-item {
        flex: 1;
        text-align: center;
        text-decoration: none;
        color: rgba(255,255,255,0.85);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        -webkit-tap-highlight-color: transparent;
    }

    /* 防止其他覆盖层抢占事件，保障导航可点 */
    .bottom-nav, .bottom-nav * { pointer-events: auto; }

    .bottom-nav .nav-item img {
        width: 22px; height: 22px; display: block; margin-bottom: 2px;
        filter: grayscale(100%) opacity(0.75);
        pointer-events: none; /* 不拦截点击，事件交给 a */
    }

    .bottom-nav .nav-item:hover { color: #fff; }
    .bottom-nav .nav-item.active { color: #fff; }
    .bottom-nav .nav-item.active img { filter: none; }

    /* iOS 安全区 */
    .bottom-nav .safe-area {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        height: env(safe-area-inset-bottom);
        height: constant(safe-area-inset-bottom);
        background: rgba(0,0,0,0.85);
        pointer-events: none; /* 安全区不拦截点击 */
    }

    /* 移除 .tips 的样式，避免覆盖底部导航导致无法点击 */
</style>

<!-- 无伸缩脚本，保持底部一排静态导航 -->
<!-- 轮播js -->
<script src="/images/js/swiper.min.js"></script>
    
    <script>
    var swiper = new Swiper('.banner', {
        pagination: {
            el: '.swiper-pagination',
        },
    });

    var swiper = new Swiper('.scroll_text1', {
        loop : true,
        autoplay:true,
        autoplay: { delay: 800},
        direction: 'vertical',
        pagination: {
            clickable: true,
        },
    });

    // 添加产品卡片动画效果
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });
    });
    </script>
</body>
</html>