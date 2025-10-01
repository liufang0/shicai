# 彩票游戏系统 - 开奖与控制对接说明

## 🎯 系统概述

现在您的系统已经完整集成了开奖采集和控制功能，包含以下核心模块：

### 核心功能模块
1. **数据采集系统** - 自动对接第三方API获取开奖数据
2. **投注控制系统** - 风控管理、投注限制、盈亏控制
3. **实时推送系统** - WebSocket实时数据推送
4. **管理员后台** - 手动控制开奖、用户管理、风控监控

## 📊 数据库结构

已创建以下核心数据表：
- `caiji` - 开奖数据采集表
- `number` - 开奖历史记录表  
- `user` - 用户表
- `bet` - 投注记录表
- `admin_control` - 管理员控制表
- `game_config` - 游戏配置表
- `system_log` - 系统日志表

## 🚀 部署步骤

### 1. 数据库初始化
```bash
# 导入数据库结构
mysql -u root -p your_database < /workspaces/shicai/新系统/database.sql
```

### 2. API接口说明

#### 开奖数据采集
```php
// 自动采集所有游戏数据
GET /collect/index

// 手动采集指定游戏
GET /collect/manual?game=pk10

// 查看采集状态
GET /collect/status
```

#### 用户投注
```php
// 用户投注
POST /bet-control/bet
{
    "game": "pk10",
    "period": "250930001", 
    "bet_type": "champion",
    "bet_content": "1",
    "bet_amount": 100
}

// 投注历史
GET /bet-control/bet-history?page=1&limit=20

// 撤销投注
POST /bet-control/cancel-bet?bet_id=123
```

#### 实时数据推送
```php
// 获取最新游戏数据
GET /websocket-push/get-latest-data?game=pk10

// WebSocket连接消息格式
{
    "type": "login",
    "user_id": 123,
    "token": "user_token"
}
```

#### 管理员控制
```php
// 管理员登录
POST /admin-control/login
{
    "username": "admin",
    "password": "admin"
}

// 预设开奖号码
POST /admin-control/set-award-numbers
{
    "game": "pk10",
    "period": "250930001",
    "numbers": "1,2,3,4,5,6,7,8,9,10",
    "remark": "控制开奖"
}

// 用户管理
GET /admin-control/get-user-list?page=1&limit=20
POST /admin-control/update-user-money
POST /admin-control/update-user-status

// 系统统计
GET /admin-control/get-system-stats
```

### 3. 定时任务设置

#### Linux Crontab
```bash
# 编辑定时任务
crontab -e

# 添加以下任务
# 每分钟采集一次开奖数据
* * * * * /usr/bin/curl http://localhost:8080/collect/index

# 每5分钟检查系统状态
*/5 * * * * /usr/bin/curl http://localhost:8080/collect/status
```

#### 或使用 PHP CLI
```bash
# 创建采集脚本
php /workspaces/shicai/新系统/think collect
```

### 4. WebSocket服务启动

使用 Workerman 启动 WebSocket 服务：
```bash
# 启动WebSocket服务
php /workspaces/shicai/新系统/websocket_server.php start

# 后台运行
php /workspaces/shicai/新系统/websocket_server.php start -d
```

## 🎮 游戏控制机制

### 自动模式
1. 系统自动从第三方API采集开奖数据
2. 如果API无响应，生成符合规律的随机号码
3. 自动进行投注结算和风控检查

### 手动控制模式
1. 管理员可预设指定期号的开奖号码
2. 系统优先使用预设号码，覆盖API数据
3. 支持针对特定用户或投注金额的控制策略

### 风控系统
- **投注限制**：单注限额、单期限额、频率限制
- **盈亏控制**：用户连胜检测、当日盈利限制
- **异常监控**：投注分布异常、一边倒警报
- **自动调整**：动态赔率调整、开奖结果智能控制

## 🔧 配置说明

### 游戏参数配置
```php
// 在 game_config 表中配置各游戏参数
INSERT INTO game_config VALUES 
('pk10', 'min_bet', '10', '最小投注'),
('pk10', 'max_bet', '100000', '最大投注'),
('pk10', 'interval', '300', '开奖间隔秒数');
```

### API数据源配置
```php
// 在 Collect.php 中修改API配置
private $apiConfig = [
    'pk10' => [
        'url' => 'http://your-api.com/pk10',
        'backup_url' => 'http://backup-api.com/pk10'
    ]
    // ...
];
```

## 📱 前端对接说明

### WebSocket连接示例
```javascript
// 建立WebSocket连接
const ws = new WebSocket('ws://localhost:15531');

// 登录认证
ws.onopen = function() {
    ws.send(JSON.stringify({
        type: 'login',
        user_id: userId,
        token: userToken
    }));
};

// 订阅游戏数据
ws.send(JSON.stringify({
    type: 'subscribe',
    game: 'pk10'
}));

// 接收推送消息
ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    switch(data.type) {
        case 'game_update':
            // 更新开奖结果
            updateGameResult(data);
            break;
        case 'countdown':
            // 更新倒计时
            updateCountdown(data);
            break;
        case 'bet_result':
            // 投注结果
            showBetResult(data);
            break;
    }
};
```

### 投注界面集成
```javascript
// 投注提交
function submitBet(game, period, betType, betContent, amount) {
    fetch('/bet-control/bet', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            game: game,
            period: period,
            bet_type: betType,
            bet_content: betContent,
            bet_amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.code === 1) {
            alert('投注成功！');
            updateUserBalance();
        } else {
            alert(data.msg);
        }
    });
}
```

## 🛡️ 安全建议

### 1. 数据库安全
- 设置复杂的数据库密码
- 限制数据库访问IP
- 定期备份重要数据

### 2. API安全
- 使用HTTPS加密传输
- 实现API访问频率限制
- 验证所有输入参数

### 3. 用户安全
- 密码MD5加密存储
- 实现登录失败次数限制
- 记录所有关键操作日志

### 4. 系统监控
- 监控异常投注模式
- 设置盈亏警报机制
- 实时监控系统性能

## 📈 运营建议

### 1. 数据分析
- 定期分析用户投注习惯
- 监控各游戏盈亏情况
- 优化赔率和限额设置

### 2. 风控管理
- 设置合理的风控阈值
- 建立人工审核机制
- 定期更新风控策略

### 3. 用户体验
- 保证开奖数据实时性
- 优化投注界面响应速度
- 提供详细的历史数据查询

## 🆘 常见问题

### Q1: 开奖数据不更新
**A:** 检查定时任务是否正常运行，API是否可访问

### Q2: 投注失败
**A:** 检查用户余额、投注限额、期号状态

### Q3: WebSocket连接断开
**A:** 实现自动重连机制，检查服务器防火墙设置

### Q4: 风控误报
**A:** 调整风控参数，增加白名单机制

---

## 🎉 总结

您的系统现在已经具备完整的开奖采集和控制功能：

✅ **自动采集** - 对接第三方API，自动获取开奖数据
✅ **手动控制** - 管理员可预设开奖结果
✅ **投注管理** - 完整的投注流程和风控系统
✅ **实时推送** - WebSocket实时数据更新
✅ **后台管理** - 用户管理、统计分析、风控监控

系统现在可以：
1. 自动运行，无需人工干预
2. 支持手动控制开奖结果
3. 实现智能风控和盈亏管理
4. 提供完整的管理后台
5. 实时推送数据到前端

需要进一步优化的方向：
- 根据实际API调整数据格式
- 完善前端界面集成
- 优化风控参数设置
- 增加更多游戏类型支持

现在您可以开始部署和测试整个系统了！