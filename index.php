<?php
//设置错误级别
error_reporting(0);
/** ///调试、找错时请去掉///前空格
ini_set('display_errors',true);
error_reporting(E_ALL); 
set_time_limit(0);
define('DEBUG',	true);
// */

// # 判断PHP版本是否满足
if(version_compare(PHP_VERSION, '5.3.0', '<'))  exit('require PHP > 5.3.0 !');

//安装检查开始：如果您已安装过ThinkSNS，可以删除本段代码
if(is_dir('install/') && !file_exists('install/install.lock')){
	header('location:' . 'install/install.php');
}
//安装检查结束

// //禁止前台访问后台，使用后台入口index.php
// if($_REQUEST['app']=='admin'){
// 	exit;
// }

//网站根路径设置
define('SITE_PATH', dirname(__FILE__));

//载入核心文件
require(SITE_PATH.'/core/core.php');

//实例化一个网站应用实例
$App = new App();
$App->run();


$mem_run_end = memory_get_usage();
$time_run_end = microtime(TRUE);

if(C('APP_DEBUG')){
	//数据库查询信息
	echo '<div align="left">';
	//缓存使用情况
	$log = Log::$log;
	$sqltime = 0;
	$sqllog = '';
	foreach($log as $l){
		$l = explode('SQL:', $l);
		$l = $l[1];
		preg_match('/RunTime\:([0-9\.]+)s/', $l, $match);
		$sqltime += floatval($match[1]);
		$sqllog .= $l.'<br/>';
	}
	//print_r(Cache::$log);
	echo '<hr>';
	echo ' Memories: '."<br/>";
	echo 'ToTal: ',number_format(($mem_run_end - $mem_include_start)/1024),'k',"<br/>";
	echo 'Include:',number_format(($mem_run_start - $mem_include_start)/1024),'k',"<br/>";
	echo 'Run:',number_format(($mem_run_end - $mem_run_start)/1024),'k<br/><hr/>';
	echo 'Time:<br/>';
	echo 'ToTal: ',$time_run_end - $time_include_start,"s<br/>";
	echo 'Include:',$time_run_start - $time_include_start,'s',"<br/>";
	echo 'SQL:',$sqltime,'s<br/>';
	echo 'Run:',$time_run_end - $time_run_start,'s<br/>';
	echo 'RunDetail:<br />';
	$last_run_time = 0;
	foreach( $time_run_detail as $k => $v ){
		if( $last_run_time > 0 ){
			echo '==='.$k.' '. floatval( $v - $time_run_start ).'s<br />';
			$last_run_time = floatval($v);
		}else{
			echo '==='.$k.' '. floatval( $v - $last_run_time ).'s<br />';
			$last_run_time = floatval($v);
		}
	}
	echo '<hr>';
	echo 'Run '.count($log).'SQL, '.$sqltime.'s <br />';
	echo $sqllog;
	echo '<hr>';
	$files = get_included_files();
	echo 'Include '.count($files).'files';
    dump($files);
    echo '<hr />';
}
// # The end