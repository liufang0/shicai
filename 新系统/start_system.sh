#!/bin/bash

# 彩票系统启动脚本
# Usage: ./start_system.sh [start|stop|restart|status]

WORKDIR="/workspaces/shicai"
WEBSERVER_PORT=8080
WEBSOCKET_SERVER="$WORKDIR/workerman_server.php"
WEB_SERVER="$WORKDIR/tp6_framework"

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_debug() {
    echo -e "${BLUE}[DEBUG]${NC} $1"
}

# 检查PHP环境
check_php() {
    log_info "Checking PHP environment..."
    
    if ! command -v php &> /dev/null; then
        log_error "PHP is not installed or not in PATH"
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    log_info "PHP Version: $PHP_VERSION"
    
    # 检查必要的PHP扩展
    REQUIRED_EXTENSIONS=("mysqli" "pdo" "json" "curl" "mbstring" "openssl")
    
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if php -m | grep -q "^$ext$"; then
            log_info "Extension $ext: ✓"
        else
            log_error "Extension $ext: ✗ (Required)"
            exit 1
        fi
    done
}

# 检查数据库连接
check_database() {
    log_info "Checking database connection..."
    
    cd $WEB_SERVER
    php -r "
    require_once 'vendor/autoload.php';
    try {
        \think\App::getInstance()->initialize();
        \$db = \think\facade\Db::connect();
        \$result = \$db->query('SELECT 1');
        if (\$result) {
            echo 'Database connection: OK\n';
            exit(0);
        }
    } catch (\Throwable \$e) {
        echo 'Database connection failed: ' . \$e->getMessage() . '\n';
        exit(1);
    }
    "
    
    if [ $? -eq 0 ]; then
        log_info "Database connection: ✓"
    else
        log_error "Database connection: ✗"
        exit 1
    fi
}

# 启动WebSocket服务器
start_websocket() {
    log_info "Starting WebSocket servers..."
    
    if pgrep -f "workerman_server.php" > /dev/null; then
        log_warn "WebSocket servers are already running"
        return
    fi
    
    cd $WORKDIR
    nohup php workerman_server.php start -d > websocket.log 2>&1 &
    
    sleep 3
    
    if pgrep -f "workerman_server.php" > /dev/null; then
        log_info "WebSocket servers started successfully"
        log_info "Game servers running on ports: 15531-15538"
    else
        log_error "Failed to start WebSocket servers"
        cat websocket.log
        exit 1
    fi
}

# 启动Web服务器
start_webserver() {
    log_info "Starting web server on port $WEBSERVER_PORT..."
    
    if pgrep -f "php.*think.*run" > /dev/null; then
        log_warn "Web server is already running"
        return
    fi
    
    cd $WEB_SERVER
    nohup php think run --host=0.0.0.0 --port=$WEBSERVER_PORT > ../webserver.log 2>&1 &
    
    sleep 2
    
    if pgrep -f "php.*think.*run" > /dev/null; then
        log_info "Web server started successfully"
        log_info "Access URL: http://localhost:$WEBSERVER_PORT"
    else
        log_error "Failed to start web server"
        cat ../webserver.log
        exit 1
    fi
}

# 停止所有服务
stop_services() {
    log_info "Stopping all services..."
    
    # 停止WebSocket服务器
    if pgrep -f "workerman_server.php" > /dev/null; then
        cd $WORKDIR
        php workerman_server.php stop
        log_info "WebSocket servers stopped"
    fi
    
    # 停止Web服务器
    pkill -f "php.*think.*run"
    if [ $? -eq 0 ]; then
        log_info "Web server stopped"
    fi
    
    log_info "All services stopped"
}

# 重启服务
restart_services() {
    log_info "Restarting services..."
    stop_services
    sleep 2
    start_services
}

# 启动所有服务
start_services() {
    log_info "=== Starting Lottery System ==="
    
    check_php
    check_database
    start_websocket
    start_webserver
    
    log_info "=== System Started Successfully ==="
    log_info "Web Interface: http://localhost:$WEBSERVER_PORT"
    log_info "WebSocket Games: ws://localhost:15531-15538"
    log_info ""
    log_info "Available games:"
    log_info "  - PK10:  ws://localhost:15531"
    log_info "  - SSC:   ws://localhost:15532"  
    log_info "  - LHC:   ws://localhost:15533"
    log_info "  - BJ28:  ws://localhost:15534"
    log_info "  - JND28: ws://localhost:15535"
    log_info "  - XYFT:  ws://localhost:15537"
    log_info "  - K3:    ws://localhost:15538"
    log_info ""
    log_info "Logs:"
    log_info "  - WebSocket: $WORKDIR/websocket.log"
    log_info "  - Web Server: $WORKDIR/webserver.log"
}

# 检查服务状态
check_status() {
    log_info "=== System Status ==="
    
    # 检查WebSocket服务器
    if pgrep -f "workerman_server.php" > /dev/null; then
        log_info "WebSocket Servers: ✓ Running"
        WEBSOCKET_PIDS=$(pgrep -f "workerman_server.php")
        log_debug "PIDs: $WEBSOCKET_PIDS"
    else
        log_warn "WebSocket Servers: ✗ Stopped"
    fi
    
    # 检查Web服务器
    if pgrep -f "php.*think.*run" > /dev/null; then
        log_info "Web Server: ✓ Running"
        WEB_PID=$(pgrep -f "php.*think.*run")
        log_debug "PID: $WEB_PID"
    else
        log_warn "Web Server: ✗ Stopped"
    fi
    
    # 检查端口占用
    log_info ""
    log_info "Port Status:"
    for port in 8080 15531 15532 15533 15534 15535 15537 15538; do
        if netstat -tlnp 2>/dev/null | grep ":$port " > /dev/null; then
            log_info "  Port $port: ✓ In use"
        else
            log_warn "  Port $port: ✗ Free"
        fi
    done
}

# 主程序
case "$1" in
    start)
        start_services
        ;;
    stop)
        stop_services
        ;;
    restart)
        restart_services
        ;;
    status)
        check_status
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status}"
        echo ""
        echo "Commands:"
        echo "  start   - Start all services (web server + websocket servers)"
        echo "  stop    - Stop all services"
        echo "  restart - Restart all services"  
        echo "  status  - Check service status"
        echo ""
        echo "Example:"
        echo "  $0 start    # Start the lottery system"
        echo "  $0 status   # Check if services are running"
        echo "  $0 stop     # Stop all services"
        exit 1
        ;;
esac