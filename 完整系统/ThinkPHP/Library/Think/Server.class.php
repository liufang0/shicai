<?php
// +----------------------------------------------------------------------
//	workerman
// +----------------------------------------------------------------------

namespace Think;
require_once 'Workerman\Autoloader.php';
//use Workerman\Worker;

/**
 * Worker控制器扩展类
 */
abstract class Server
{
    protected $worker;
    protected $socket    = '';
    protected $protocol  = 'websocket';
    protected $host      = '0.0.0.0';
    protected $port      = '2346';
    protected $processes = 10;
    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        // 实例化 Websocket 服务
        $this->worker = new \Workerman\Worker($this->socket ?: $this->protocol . '://' . $this->host . ':' . $this->port);
        // 设置进程数
        $this->worker->count = $this->processes;
		// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
		$this->worker->uidConnections = array();
        // 初始化
        $this->worker->init();
		
        // 设置回调
        foreach (['onWorkerStart', 'onConnect', 'onMessage', 'onClose', 'onError', 'onBufferFull', 'onBufferDrain', 'onWorkerStop', 'onWorkerReload'] as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }
        // Run worker
        \Workerman\Worker::runAll();
    }
    protected function init(){
    	
    }
}
