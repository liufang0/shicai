<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;

/**
 * 游戏运行控制器
 */
class Run extends BaseController
{
    /**
     * 启动PK10游戏
     */
    public function pk10()
    {
        return $this->runGame('pk10', 'WorkermanPk10');
    }
    
    /**
     * 启动时时彩游戏
     */
    public function ssc()
    {
        return $this->runGame('ssc', 'WorkermanSsc');
    }
    
    /**
     * 启动北京28游戏
     */
    public function bj28()
    {
        return $this->runGame('bj28', 'WorkermanBj28');
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
        $games = ['pk10', 'ssc', 'bj28', 'jnd28', 'xjp28', 'lhc', 'xyft'];
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
}