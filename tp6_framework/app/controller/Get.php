<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;

/**
 * 数据获取API控制器
 */
class Get extends BaseController
{
    /**
     * 获取六合彩数据
     */
    public function getLhc()
    {
        $caiji = Db::table('caiji')->where('game', 'lhc')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        // 格式化数据
        if (config('index_page') == '1') {
            $format = json_decode(lhc_format($caiji), true);
        } else {
            $format = json_decode(lhc_format2($caiji), true);
        }
        
        return json($format);
    }
    
    /**
     * 获取PK10数据
     */
    public function getPk10()
    {
        $caiji = Db::table('caiji')->where('game', 'pk10')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(pk10_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取时时彩数据
     */
    public function getEr75sc()
    {
        $caiji = Db::table('caiji')->where('game', 'er75sc')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(er75sc_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取幸运飞艇数据
     */
    public function getXyft()
    {
        $caiji = Db::table('caiji')->where('game', 'xyft')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(xyft_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取北京28数据
     */
    public function getBj28()
    {
        $caiji = Db::table('caiji')->where('game', 'bj28')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(bj28_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取加拿大28数据
     */
    public function getJnd28()
    {
        $caiji = Db::table('caiji')->where('game', 'jnd28')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(jnd28_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取新疆28数据
     */
    public function getXjp28()
    {
        $caiji = Db::table('caiji')->where('game', 'xjp28')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(xjp28_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取K3数据
     */
    public function getK3()
    {
        $caiji = Db::table('caiji')->where('game', 'k3')->order('id', 'desc')->find();
        
        if (!$caiji) {
            return json(['code' => 0, 'msg' => '暂无数据']);
        }
        
        $format = json_decode(k3_format($caiji), true);
        return json($format);
    }
    
    /**
     * 获取游戏列表数据
     */
    public function getList()
    {
        $game = request()->param('game', 'pk10');
        $limit = request()->param('limit', 20);
        
        $list = Db::table('caiji')
               ->where('game', $game)
               ->order('id', 'desc')
               ->limit($limit)
               ->select();
        
        return json(['code' => 1, 'data' => $list]);
    }
    
    /**
     * 获取用户投注统计
     */
    public function getUserBet()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            return json(['code' => 0, 'msg' => '请先登录']);
        }
        
        $game = request()->param('game');
        $issue = request()->param('issue');
        
        $query = Db::table('bet_log')->where('user_id', $user_id);
        
        if ($game) {
            $query->where('game', $game);
        }
        
        if ($issue) {
            $query->where('issue', $issue);
        }
        
        $bets = $query->order('create_time', 'desc')->select();
        
        return json(['code' => 1, 'data' => $bets]);
    }
    
    /**
     * 获取游戏状态
     */
    public function getGameStatus()
    {
        $game = request()->param('game', 'pk10');
        
        $status = [
            'current_issue' => Cache::get($game . '_current_issue'),
            'next_time' => Cache::get($game . '_next_time'),
            'status' => Cache::get($game . '_status', 'waiting'),
            'countdown' => Cache::get($game . '_countdown', 0)
        ];
        
        return json(['code' => 1, 'data' => $status]);
    }
}