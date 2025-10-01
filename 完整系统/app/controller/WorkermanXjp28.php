<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 新疆28 WebSocket控制器
 */
class WorkermanXjp28 extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15535';
    protected $game = 'xjp28';
    
    protected function initializeGame()
    {
        // 新疆28特有初始化逻辑
    }
    
    protected function formatGameData($caiji)
    {
        return xjp28_format($caiji);
    }
}