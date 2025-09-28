<?php
declare(strict_types=1);

namespace app\controller;

/**
 * 北京28 WebSocket控制器
 */
class WorkermanBj28 extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15532';
    protected $game = 'bj28';
    
    protected function initializeGame()
    {
        // 北京28特有初始化逻辑
    }
    
    protected function formatGameData($caiji)
    {
        return bj28_format($caiji);
    }
}