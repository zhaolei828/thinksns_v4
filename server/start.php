<?php
/**
 * run with command 
 * php start.php start
 */

ini_set('display_errors', 'on');
use Workerman\Worker;

// 检查扩展
if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl extension. See http://doc3.workerman.net/install/install.html\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix extension. See http://doc3.workerman.net/install/install.html\n");
}

// 标记是全局启动
define('GLOBAL_START', 1);

require_once __DIR__ . '/Workerman/Autoloader.php';


// 启动Message
if(is_file(__DIR__.'/Thinksns/Message/msgd.php')){
    require_once __DIR__.'/Thinksns/Message/msgd.php';
}
// 加载所有Thinksns/*/start.php，以便启动所有服务
foreach(glob(__DIR__.'/Thinksns/*/start*.php') as $start_file)
{
    require_once $start_file;
}
// 运行所有服务
Worker::runAll();