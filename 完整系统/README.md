# 食彩管理系统 - 完整版

## 系统说明
这是一个完整的食彩竞猜游戏管理系统，包含前台用户端和后台管理端。

## 目录结构
```
完整系统/
├── app/                    # 新版控制器（ThinkPHP 8）
├── Application/            # 原版控制器（ThinkPHP 3）
├── public/                 # 新版入口文件和资源
├── Public/                 # 原版静态资源
├── Template/               # 原版模板文件
├── ThinkPHP/              # ThinkPHP 3.x 框架
├── config/                # 配置文件
├── images/                # 图片资源
├── index.php              # 前台入口文件
├── shicai.db              # SQLite数据库
├── workerman_server.php   # WebSocket服务器
├── start_system.sh        # 启动脚本
└── .htaccess              # 重写规则
```

## 快速启动

### 1. 启动管理后台
```bash
cd 完整系统/public
php -S localhost:8000 admin.php
```

### 2. 启动用户前台
```bash
cd 完整系统
php -S localhost:8001 index.php
```

### 3. 启动WebSocket服务（可选）
```bash
cd 完整系统
php workerman_server.php start
```

## 访问地址
- 管理后台：http://localhost:8000 （用户名：admin，密码：admin）
- 用户前台：http://localhost:8001
- 数据库：SQLite文件 shicai.db

## 主要功能

### 管理后台
- ✅ 用户管理（注册、登录、积分管理）
- ✅ 游戏管理（开奖预设、投注记录）
- ✅ 收款设置（USDT、支付宝、微信）
- ✅ 统计分析（用户统计、资金统计）
- ✅ 系统配置（游戏配置、机器人设置）

### 用户前台
- ✅ 用户注册登录
- ✅ 游戏投注（时时彩、急速飞艇、PK10等）
- ✅ 充值提现
- ✅ 投注记录
- ✅ 个人中心

## 技术栈
- PHP 8.0+
- ThinkPHP 8.1.3 + ThinkPHP 3.x（双框架兼容）
- SQLite 数据库
- Workerman WebSocket
- jQuery + LayUI

## 数据库表结构
- user: 用户表
- admin: 管理员表
- caiji: 开奖数据表
- bet: 投注记录表
- game_config: 游戏配置表
- system_log: 系统日志表
- admin_control: 管理员操作记录表
- number: 号码数据表

## 注意事项
1. 确保PHP版本 >= 8.0
2. 需要SQLite扩展支持
3. WebSocket服务需要pcntl扩展（Linux环境）
4. 生产环境请修改默认密码
5. 建议使用nginx或apache作为Web服务器

## 开发说明
- 管理后台使用现代化单页面应用设计
- 支持多种收款方式配置
- 集成实时数据推送功能
- 完整的权限管理系统
- 响应式设计，支持移动端

## 更新日志
- v1.0.0: 初始版本，基础功能完整
- v1.1.0: 新增数字货币收款功能
- v1.2.0: 优化用户界面和交互体验
- v1.3.0: 新增开奖预设和统计功能