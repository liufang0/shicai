<?php
declare(strict_types=1);

namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Log;

/**
 * 管理员控制后台
 * 负责开奖控制、用户管理、风控管理等
 */
class AdminControl extends Base
{
    /**
     * 管理员登录验证
     */
    protected function checkAdmin()
    {
        $adminId = session('admin.id');
        if (!$adminId) {
            return json(['code' => 0, 'msg' => '请先登录管理后台']);
        }

        $admin = Db::table('user')
            ->where('id', $adminId)
            ->where('user_type', 3)
            ->find();

        if (!$admin) {
            return json(['code' => 0, 'msg' => '管理员权限不足']);
        }

        return true;
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        $username = request()->param('username');
        $password = request()->param('password');

        if (!$username || !$password) {
            return json(['code' => 0, 'msg' => '用户名和密码不能为空']);
        }

        $admin = Db::table('user')
            ->where('username', $username)
            ->where('user_type', 3)
            ->find();

        if (!$admin || md5($password) !== $admin['password']) {
            return json(['code' => 0, 'msg' => '用户名或密码错误']);
        }

        if ($admin['status'] != 1) {
            return json(['code' => 0, 'msg' => '账户已被禁用']);
        }

        // 保存管理员session
        session('admin', [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname']
        ]);

        // 记录登录日志
        Db::table('system_log')->insert([
            'type' => 'admin_login',
            'user_id' => $admin['id'],
            'content' => "管理员{$admin['username']}登录后台",
            'ip' => request()->ip(),
            'create_time' => date('Y-m-d H:i:s')
        ]);

        return json(['code' => 1, 'msg' => '登录成功']);
    }

    /**
     * 管理员登出
     */
    public function logout()
    {
        session('admin', null);
        return json(['code' => 1, 'msg' => '登出成功']);
    }

    /**
     * 预设开奖号码
     */
    public function setAwardNumbers()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $game = request()->param('game');
        $periodNumber = request()->param('period');
        $awardNumbers = request()->param('numbers');
        $remark = request()->param('remark', '');

        if (!$game || !$periodNumber || !$awardNumbers) {
            return json(['code' => 0, 'msg' => '参数不完整']);
        }

        if (!in_array($game, ['pk10', 'ssc', 'bj28', 'jnd28'])) {
            return json(['code' => 0, 'msg' => '不支持的游戏类型']);
        }

        // 验证号码格式
        $validation = $this->validateNumbers($game, $awardNumbers);
        if (!$validation['valid']) {
            return json(['code' => 0, 'msg' => $validation['msg']]);
        }

        // 检查期号是否已开奖
        $exists = Db::table('caiji')
            ->where('game', $game)
            ->where('periodnumber', $periodNumber)
            ->find();

        if ($exists) {
            return json(['code' => 0, 'msg' => '该期号已开奖，无法修改']);
        }

        // 检查是否已有预设
        $control = Db::table('admin_control')
            ->where('game', $game)
            ->where('periodnumber', $periodNumber)
            ->find();

        $adminId = session('admin.id');
        $controlData = [
            'game' => $game,
            'periodnumber' => $periodNumber,
            'control_type' => 'manual',
            'awardnumbers' => $awardNumbers,
            'admin_id' => $adminId,
            'remark' => $remark,
            'create_time' => date('Y-m-d H:i:s')
        ];

        if ($control) {
            // 更新现有记录
            Db::table('admin_control')
                ->where('id', $control['id'])
                ->update($controlData);
        } else {
            // 插入新记录
            Db::table('admin_control')->insert($controlData);
        }

        // 记录操作日志
        Db::table('system_log')->insert([
            'type' => 'admin_control_award',
            'user_id' => $adminId,
            'content' => "预设{$game}期号{$periodNumber}开奖号码：{$awardNumbers}",
            'ip' => request()->ip(),
            'create_time' => date('Y-m-d H:i:s')
        ]);

        return json(['code' => 1, 'msg' => '预设成功']);
    }

    /**
     * 验证开奖号码格式
     */
    private function validateNumbers($game, $numbers)
    {
        $numbersArray = explode(',', $numbers);

        switch ($game) {
            case 'pk10':
                // PK10需要10个数字，1-10，不重复
                if (count($numbersArray) != 10) {
                    return ['valid' => false, 'msg' => 'PK10需要10个号码'];
                }
                
                foreach ($numbersArray as $num) {
                    if (!is_numeric($num) || $num < 1 || $num > 10) {
                        return ['valid' => false, 'msg' => 'PK10号码范围1-10'];
                    }
                }
                
                if (count(array_unique($numbersArray)) != 10) {
                    return ['valid' => false, 'msg' => 'PK10号码不能重复'];
                }
                break;

            case 'ssc':
                // 时时彩需要5个数字，0-9
                if (count($numbersArray) != 5) {
                    return ['valid' => false, 'msg' => '时时彩需要5个号码'];
                }
                
                foreach ($numbersArray as $num) {
                    if (!is_numeric($num) || $num < 0 || $num > 9) {
                        return ['valid' => false, 'msg' => '时时彩号码范围0-9'];
                    }
                }
                break;

            case 'bj28':
            case 'jnd28':
                // 28系列只需要和值
                if (count($numbersArray) != 1) {
                    return ['valid' => false, 'msg' => '28系列只需要1个和值'];
                }
                
                $sum = intval($numbersArray[0]);
                if ($sum < 0 || $sum > 27) {
                    return ['valid' => false, 'msg' => '28系列和值范围0-27'];
                }
                break;

            default:
                return ['valid' => false, 'msg' => '不支持的游戏类型'];
        }

        return ['valid' => true, 'msg' => ''];
    }

    /**
     * 获取预设开奖列表
     */
    public function getControlList()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $page = intval(request()->param('page', 1));
        $limit = intval(request()->param('limit', 20));
        $game = request()->param('game', '');

        $where = [];
        if ($game) {
            $where[] = ['game', '=', $game];
        }

        $list = Db::table('admin_control')
            ->where($where)
            ->order('id', 'desc')
            ->page($page, $limit)
            ->select();

        $total = Db::table('admin_control')
            ->where($where)
            ->count();

        return json([
            'code' => 1,
            'data' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * 用户管理 - 获取用户列表
     */
    public function getUserList()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $page = intval(request()->param('page', 1));
        $limit = intval(request()->param('limit', 20));
        $keyword = request()->param('keyword', '');

        $where = [['user_type', '<', 3]]; // 排除管理员
        if ($keyword) {
            $where[] = ['username|nickname', 'like', "%{$keyword}%"];
        }

        $list = Db::table('user')
            ->where($where)
            ->field('id,username,nickname,phone,money,frozen_money,total_bet,total_win,status,last_login_time,reg_time')
            ->order('id', 'desc')
            ->page($page, $limit)
            ->select();

        $total = Db::table('user')
            ->where($where)
            ->count();

        return json([
            'code' => 1,
            'data' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * 用户管理 - 修改用户余额
     */
    public function updateUserMoney()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $userId = request()->param('user_id');
        $money = floatval(request()->param('money'));
        $type = request()->param('type'); // add增加, sub减少, set设置
        $remark = request()->param('remark', '');

        if (!$userId || !in_array($type, ['add', 'sub', 'set'])) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $user = Db::table('user')->where('id', $userId)->find();
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }

        $oldMoney = $user['money'];
        $newMoney = 0;

        switch ($type) {
            case 'add':
                $newMoney = $oldMoney + $money;
                break;
            case 'sub':
                $newMoney = max(0, $oldMoney - $money);
                break;
            case 'set':
                $newMoney = max(0, $money);
                break;
        }

        Db::table('user')
            ->where('id', $userId)
            ->update(['money' => $newMoney]);

        // 记录操作日志
        $adminId = session('admin.id');
        Db::table('system_log')->insert([
            'type' => 'admin_update_money',
            'user_id' => $adminId,
            'content' => "修改用户{$user['username']}余额：{$oldMoney} -> {$newMoney}，操作：{$type}，金额：{$money}，备注：{$remark}",
            'ip' => request()->ip(),
            'create_time' => date('Y-m-d H:i:s')
        ]);

        return json(['code' => 1, 'msg' => '修改成功']);
    }

    /**
     * 用户管理 - 禁用/启用用户
     */
    public function updateUserStatus()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $userId = request()->param('user_id');
        $status = intval(request()->param('status')); // 1启用, 0禁用

        if (!$userId || !in_array($status, [0, 1])) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $user = Db::table('user')->where('id', $userId)->find();
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }

        Db::table('user')
            ->where('id', $userId)
            ->update(['status' => $status]);

        $action = $status ? '启用' : '禁用';
        
        // 记录操作日志
        $adminId = session('admin.id');
        Db::table('system_log')->insert([
            'type' => 'admin_update_user_status',
            'user_id' => $adminId,
            'content' => "{$action}用户{$user['username']}",
            'ip' => request()->ip(),
            'create_time' => date('Y-m-d H:i:s')
        ]);

        return json(['code' => 1, 'msg' => $action . '成功']);
    }

    /**
     * 投注管理 - 获取投注列表
     */
    public function getBetList()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $page = intval(request()->param('page', 1));
        $limit = intval(request()->param('limit', 20));
        $game = request()->param('game', '');
        $userId = request()->param('user_id', '');
        $status = request()->param('status', '');

        $where = [];
        if ($game) {
            $where[] = ['b.game', '=', $game];
        }
        if ($userId) {
            $where[] = ['b.user_id', '=', $userId];
        }
        if ($status !== '') {
            $where[] = ['b.status', '=', $status];
        }

        $list = Db::table('bet')
            ->alias('b')
            ->leftJoin('user u', 'b.user_id = u.id')
            ->where($where)
            ->field('b.*,u.username,u.nickname')
            ->order('b.id', 'desc')
            ->page($page, $limit)
            ->select();

        $total = Db::table('bet')
            ->alias('b')
            ->leftJoin('user u', 'b.user_id = u.id')
            ->where($where)
            ->count();

        return json([
            'code' => 1,
            'data' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * 系统统计
     */
    public function getSystemStats()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $today = date('Y-m-d');
        $todayStart = $today . ' 00:00:00';
        $todayEnd = $today . ' 23:59:59';

        // 今日统计
        $todayStats = [
            'bet_count' => Db::table('bet')
                ->where('bet_time', 'between', [$todayStart, $todayEnd])
                ->count(),
            'bet_amount' => Db::table('bet')
                ->where('bet_time', 'between', [$todayStart, $todayEnd])
                ->sum('bet_amount'),
            'win_amount' => Db::table('bet')
                ->where('bet_time', 'between', [$todayStart, $todayEnd])
                ->where('status', 1)
                ->sum('win_amount'),
            'new_users' => Db::table('user')
                ->where('reg_time', 'between', [$todayStart, $todayEnd])
                ->count(),
            'active_users' => Db::table('user')
                ->where('last_login_time', 'between', [$todayStart, $todayEnd])
                ->count()
        ];

        // 总体统计
        $totalStats = [
            'total_users' => Db::table('user')->where('user_type', '<', 3)->count(),
            'total_bets' => Db::table('bet')->count(),
            'total_bet_amount' => Db::table('bet')->sum('bet_amount'),
            'total_win_amount' => Db::table('bet')->where('status', 1)->sum('win_amount')
        ];

        // 游戏统计
        $gameStats = Db::table('bet')
            ->field('game, count(*) as bet_count, sum(bet_amount) as bet_amount, sum(win_amount) as win_amount')
            ->where('bet_time', 'between', [$todayStart, $todayEnd])
            ->group('game')
            ->select();

        return json([
            'code' => 1,
            'data' => [
                'today' => $todayStats,
                'total' => $totalStats,
                'games' => $gameStats
            ]
        ]);
    }

    /**
     * 风控警报列表
     */
    public function getRiskAlerts()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $page = intval(request()->param('page', 1));
        $limit = intval(request()->param('limit', 20));

        $list = Db::table('system_log')
            ->where('type', 'bet_risk_alert')
            ->order('id', 'desc')
            ->page($page, $limit)
            ->select();

        foreach ($list as &$item) {
            $item['content'] = json_decode($item['content'], true);
        }

        $total = Db::table('system_log')
            ->where('type', 'bet_risk_alert')
            ->count();

        return json([
            'code' => 1,
            'data' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * 游戏配置管理
     */
    public function updateGameConfig()
    {
        $checkResult = $this->checkAdmin();
        if ($checkResult !== true) {
            return $checkResult;
        }

        $game = request()->param('game');
        $configKey = request()->param('config_key');
        $configValue = request()->param('config_value');

        if (!$game || !$configKey) {
            return json(['code' => 0, 'msg' => '参数不完整']);
        }

        $exists = Db::table('game_config')
            ->where('game', $game)
            ->where('config_key', $configKey)
            ->find();

        if ($exists) {
            Db::table('game_config')
                ->where('game', $game)
                ->where('config_key', $configKey)
                ->update(['config_value' => $configValue]);
        } else {
            Db::table('game_config')->insert([
                'game' => $game,
                'config_key' => $configKey,
                'config_value' => $configValue
            ]);
        }

        // 清除相关缓存
        Cache::delete("game_config_{$game}_{$configKey}");

        return json(['code' => 1, 'msg' => '配置更新成功']);
    }
}