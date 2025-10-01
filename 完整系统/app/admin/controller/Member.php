<?php
declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;

/**
 * 会员管理控制器
 */
class Member extends BaseController
{
    /**
     * 会员列表
     */
    public function index()
    {
        $page = request()->param('page', 1);
        $limit = request()->param('limit', 20);
        $keyword = request()->param('keyword', '');
        $status = request()->param('status', '');
        
        $query = Db::table('user');
        
        if ($keyword) {
            $query->where('username|phone|nickname', 'like', "%{$keyword}%");
        }
        
        if ($status !== '') {
            $query->where('status', $status);
        }
        
        $total = $query->count();
        $users = $query->order('id', 'desc')
                      ->page($page, $limit)
                      ->select()
                      ->toArray();
        
        if (request()->isAjax()) {
            return json(['code' => 0, 'msg' => '', 'count' => $total, 'data' => $users]);
        }
        
        View::assign('users', $users);
        return View::fetch();
    }
    
    /**
     * 添加会员
     */
    public function add()
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            // 验证数据
            if (empty($data['username']) || empty($data['password'])) {
                return json(['code' => 0, 'msg' => '用户名和密码不能为空']);
            }
            
            // 检查用户名是否已存在
            if (Db::table('user')->where('username', $data['username'])->count()) {
                return json(['code' => 0, 'msg' => '用户名已存在']);
            }
            
            $insert_data = [
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'nickname' => $data['nickname'] ?? $data['username'],
                'phone' => $data['phone'] ?? '',
                'money' => floatval($data['money'] ?? 0),
                'status' => intval($data['status'] ?? 1),
                'level' => intval($data['level'] ?? 0),
                'create_time' => time(),
                'update_time' => time()
            ];
            
            $result = Db::table('user')->insert($insert_data);
            
            if ($result) {
                return json(['code' => 1, 'msg' => '添加成功']);
            } else {
                return json(['code' => 0, 'msg' => '添加失败']);
            }
        }
        
        return View::fetch();
    }
    
    /**
     * 编辑会员
     */
    public function edit()
    {
        $id = request()->param('id');
        
        if (request()->isPost()) {
            $data = request()->post();
            
            $update_data = [
                'nickname' => $data['nickname'],
                'phone' => $data['phone'],
                'money' => floatval($data['money']),
                'status' => intval($data['status']),
                'level' => intval($data['level']),
                'update_time' => time()
            ];
            
            // 如果有新密码，更新密码
            if (!empty($data['password'])) {
                $update_data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $result = Db::table('user')->where('id', $id)->update($update_data);
            
            if ($result !== false) {
                return json(['code' => 1, 'msg' => '修改成功']);
            } else {
                return json(['code' => 0, 'msg' => '修改失败']);
            }
        }
        
        $user = Db::table('user')->where('id', $id)->find();
        if (!$user) {
            return '用户不存在';
        }
        
        View::assign('user', $user);
        return View::fetch();
    }
    
    /**
     * 删除会员
     */
    public function delete()
    {
        $id = request()->param('id');
        
        $result = Db::table('user')->where('id', $id)->delete();
        
        if ($result) {
            return json(['code' => 1, 'msg' => '删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
    
    /**
     * 调整余额
     */
    public function adjustMoney()
    {
        $id = request()->param('id');
        $money = request()->param('money', 0);
        $type = request()->param('type', 'add'); // add 或 sub
        $remark = request()->param('remark', '');
        
        $user = Db::table('user')->where('id', $id)->find();
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }
        
        Db::startTrans();
        try {
            if ($type == 'add') {
                Db::table('user')->where('id', $id)->inc('money', $money);
            } else {
                if ($user['money'] < $money) {
                    throw new \Exception('余额不足');
                }
                Db::table('user')->where('id', $id)->dec('money', $money);
            }
            
            // 记录资金变动
            Db::table('money_log')->insert([
                'user_id' => $id,
                'type' => $type == 'add' ? 1 : 2,
                'amount' => $money,
                'before_money' => $user['money'],
                'after_money' => $type == 'add' ? $user['money'] + $money : $user['money'] - $money,
                'remark' => $remark ?: '管理员调整',
                'create_time' => time()
            ]);
            
            Db::commit();
            return json(['code' => 1, 'msg' => '调整成功']);
            
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }
    
    /**
     * 会员详情
     */
    public function detail()
    {
        $id = request()->param('id');
        
        $user = Db::table('user')->where('id', $id)->find();
        if (!$user) {
            return '用户不存在';
        }
        
        // 投注统计
        $bet_stats = Db::table('bet_log')
                      ->where('user_id', $id)
                      ->field('COUNT(*) as bet_count, SUM(bet_amount) as total_bet, SUM(win_amount) as total_win')
                      ->find();
        
        // 最近投注记录
        $recent_bets = Db::table('bet_log')
                        ->where('user_id', $id)
                        ->order('create_time', 'desc')
                        ->limit(10)
                        ->select();
        
        // 资金记录
        $money_logs = Db::table('money_log')
                       ->where('user_id', $id)
                       ->order('create_time', 'desc')
                       ->limit(20)
                       ->select();
        
        View::assign('user', $user);
        View::assign('bet_stats', $bet_stats);
        View::assign('recent_bets', $recent_bets);
        View::assign('money_logs', $money_logs);
        return View::fetch();
    }
}