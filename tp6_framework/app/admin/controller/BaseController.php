<?php
declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController as Base;
use think\facade\Session;

/**
 * 管理后台基础控制器
 */
class BaseController extends Base
{
    protected $admin;
    
    public function initialize()
    {
        parent::initialize();
        
        // 检查登录状态
        $this->admin = Session::get('admin');
        
        $controller = strtolower(request()->controller());
        $action = strtolower(request()->action());
        
        // 登录控制器不需要验证
        if ($controller !== 'login') {
            if (!$this->admin || !isset($this->admin['id'])) {
                if (request()->isAjax()) {
                    return json(['code' => -1, 'msg' => '请先登录', 'url' => '/admin/login']);
                }
                redirect('/admin/login')->send();
                exit;
            }
        }
    }
    
    /**
     * 检查权限
     */
    protected function checkAuth($action = '')
    {
        // 超级管理员跳过权限检查
        if ($this->admin['level'] == 1) {
            return true;
        }
        
        // TODO: 实现权限检查逻辑
        return true;
    }
}