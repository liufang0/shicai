#!/bin/bash

# 食彩游戏管理系统 - 自动部署脚本

echo "🚀 开始部署食彩游戏管理系统..."

# 设置变量
WEB_DIR="/var/www/html"  # 修改为你的网站目录
DEPLOY_DIR="$(pwd)"

# 检查是否为root用户
if [ "$EUID" -ne 0 ]; then
    echo "❌ 请使用root权限运行此脚本"
    exit 1
fi

# 检查PHP是否安装
if ! command -v php &> /dev/null; then
    echo "❌ PHP未安装，正在安装..."
    apt-get update
    apt-get install -y php php-sqlite3 apache2
fi

# 创建目录结构
echo "📁 创建目录结构..."
mkdir -p ${WEB_DIR}
mkdir -p ${WEB_DIR}/uploads

# 复制文件
echo "📄 复制核心文件..."
cp admin.php ${WEB_DIR}/
cp shicai.db ${WEB_DIR}/
cp htaccess-example.txt ${WEB_DIR}/.htaccess

# 设置权限
echo "🔐 设置文件权限..."
chown -R www-data:www-data ${WEB_DIR}
chmod 644 ${WEB_DIR}/admin.php
chmod 666 ${WEB_DIR}/shicai.db
chmod 755 ${WEB_DIR}/uploads
chmod 644 ${WEB_DIR}/.htaccess

# 启用Apache模块
echo "⚙️ 配置Apache..."
a2enmod rewrite
a2enmod expires
a2enmod deflate

# 重启Apache
echo "🔄 重启Web服务..."
systemctl restart apache2
systemctl enable apache2

# 获取服务器IP
SERVER_IP=$(curl -s ifconfig.me || hostname -I | awk '{print $1}')

echo ""
echo "✅ 部署完成！"
echo ""
echo "🌐 访问地址："
echo "   管理后台: http://${SERVER_IP}/admin.php"
echo "   或者:     http://${SERVER_IP}/admin"
echo ""
echo "🔑 默认账号: admin / admin"
echo ""
echo "⚠️  重要提醒："
echo "   1. 立即修改管理员密码"
echo "   2. 配置防火墙规则"
echo "   3. 启用HTTPS（推荐）"
echo "   4. 定期备份数据库"
echo ""

# 检查服务状态
if systemctl is-active --quiet apache2; then
    echo "✅ Apache服务运行正常"
else
    echo "❌ Apache服务启动失败，请检查配置"
fi

# 检查数据库文件
if [ -f "${WEB_DIR}/shicai.db" ]; then
    echo "✅ 数据库文件就绪"
else
    echo "❌ 数据库文件缺失"
fi

echo ""
echo "🎮 部署完成，开始测试吧！"