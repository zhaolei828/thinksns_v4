<?php 
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

// gateway 进程
$gateway = new Gateway("Websocket://0.0.0.0:7273");
// 设置名称，方便status时查看
$gateway->name = 'ChatGateway';
// 设置进程数，gateway进程数建议与cpu核数相同
$gateway->count = 2;
// 分布式部署时请设置成内网ip（非127.0.0.1）
$gateway->lanIp = '127.0.0.1';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4001 4002 4003 4004 4个端口作为内部通讯端口 
$gateway->startPort = 4000;
// 心跳间隔
$gateway->pingInterval = 10;
// 心跳数据
$gateway->pingData = '{"type":"ping"}';

/* 
// 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
$gateway->onConnect = function($connection)
{
    $connection->onWebSocketConnect = function($connection , $http_header)
    {
        // 可以在这里判断连接来源是否合法，不合法就关掉连接
        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
        if($_SERVER['HTTP_ORIGIN'] != 'http://chat.workerman.net')
        {
            $connection->close();
        }
        // onWebSocketConnect 里面$_GET $_SERVER是可用的
        // var_dump($_GET, $_SERVER);
    };
}; 
*/

// #### 内部推送端口(假设内网ip为192.168.100.100) ####
$internal_gateway = new Gateway("Text://127.0.0.1:7274");
$internal_gateway->name='internalGateway';
$internal_gateway->startPort = 2800;
// #### 内部推送端口设置完毕 ####


// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'ChatBusinessWorker';
// bussinessWorker进程数量
$worker->count = 4;
// WebServer
// $web = new WebServer("http://0.0.0.0:55155");
// // WebServer数量
// $web->count = 2;
// // 设置站点根目录
// $web->addRoot('www.comper.cn', __DIR__.'/Web');
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}