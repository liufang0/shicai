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
        return ssc_format($caiji);
    }
}

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

/**
 * 幸运飞艇 WebSocket控制器
 */
class WorkermanXyft extends WorkermanBase
{
    protected $socket = 'websocket://0.0.0.0:15537';
    protected $game = 'xyft';
    
    protected function initializeGame()
    {
        // 幸运飞艇特有初始化逻辑
    }
    
    protected function formatGameData($caiji)
    {
        return xyft_format($caiji);
    }
}