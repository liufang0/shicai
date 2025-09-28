<?php

namespace app\controller;

use think\facade\View;
use think\facade\Db;
use think\facade\Config;

class Index extends BaseController
{
    public function index()
    {
        // 临时简化逻辑用于调试
        try {
            return "网站迁移成功！ThinkPHP 8.1.3 正在运行";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }

    /**
     * 授权检查方法（从原项目迁移）
     */
    private function authCheck($authCode, $host)
    {
        // 这里需要根据原项目的auth_check函数实现
        // 临时返回true用于测试
        return true;
    }
}
