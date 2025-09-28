<?php
declare(strict_types=1);

namespace app\controller;

use think\App;
use think\exception\ValidateException;
use think\facade\View;
use think\facade\Session;
use think\facade\Db;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        // 获取配置 - 替代原来的getConfigs()
        $this->loadConfigs();
        
        // 检测登录状态
        $userid = Session::get('user');
        $controllerName = $this->request->controller();
        
        if (config('is_weixin') == '1' && $this->isWeixin()) {
            if ($controllerName != 'Index') {
                if (empty($userid['id'])) {
                    return redirect('/index/wxlogin');
                }
            }
        } else {
            if ($controllerName != 'Index' && $controllerName != 'Run') {
                if (empty($userid['id'])) {
                    return redirect('/index/login');
                }
            }
        }
        
        if (isset($userid['id'])) {
            $userinfo = Db::table('user')->where("id", $userid['id'])->find();
            if (!$userinfo) {
                Session::clear();
                return redirect('/index/index');
            }
        } else {
            $userinfo = [];
        }
        
        View::assign('userinfo', $userinfo);
        View::assign('version', config('app.version', '1.0'));
    }

    /**
     * 加载配置
     */
    protected function loadConfigs()
    {
        // 从数据库加载配置，替代原来的getConfigs函数
        // 这里可以实现缓存机制
    }

    /**
     * 检测是否微信浏览器
     */
    protected function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate)->message($message);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $result = $v->batch($batch)->check($data);

        if (!$result) {
            if ($batch) {
                return $v->getError();
            } else {
                throw new ValidateException($v->getError());
            }
        }

        return $result;
    }
}