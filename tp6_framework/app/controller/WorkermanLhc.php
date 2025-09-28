<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 六合彩 WebSocket控制器
 */
class WorkermanLhc extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15536';
    protected $game = 'lhc';
    
    protected function initializeGame()
    {
        // 六合彩特有初始化逻辑
    }
    
    protected function formatGameData($caiji)
    {
        return lhc_format($caiji);
    }
}