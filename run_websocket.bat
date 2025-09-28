@echo off
echo 正在启动WebSocket服务...
"D:\UPUPW_ANK_W64\Modules\PHPX\PHP54\php.exe" "%~dp0start_io.php" start
echo.
echo 如果没有错误信息，服务已成功启动
echo 请保持此窗口打开，关闭窗口将停止服务
echo.
pause