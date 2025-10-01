<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 时时彩 WebSocket控制器
 */
class WorkermanSsc extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15533';
    protected $game = 'ssc';
    
    protected function initializeGame()
    {
        // 时时彩特有初始化逻辑
    }
    
    protected function formatGameData($caiji)
    {
        return er75sc_format($caiji);
    }
}