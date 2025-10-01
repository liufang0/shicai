<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 加拿大28 WebSocket控制器  
 */
class WorkermanJnd28 extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15534';
    protected $game = 'jnd28';
    
    protected function initializeGame()
    {
        // 加拿大28特有初始化逻辑
    }
    
    protected function formatGameData($caiji)
    {
        return jnd28_format($caiji);
    }
}