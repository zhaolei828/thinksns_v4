<?php
define('SITE_PATH', dirname(__FILE__));

$conf = array(
    'DB_TYPE'           =>  'mysql',
    'DB_HOST'           =>  '115.29.168.253',
    'DB_NAME'           =>  'ts4',
    'DB_USER'           =>  'ts4',
    'DB_PWD'            =>  'ts4',
    'DB_PORT'           =>  3306,
    'DB_PREFIX'         =>  'ts_',
    'DB_CHARSET'        =>  'utf8',

    'JPUSH_KEY'         =>  '01693e083dbdfa6b322f3a39',
    'JPUSH_SECRET'      =>  '6924125f0280e586d34f5b51',

    'SITE_URL'          =>  'http://demo.thinksns.com/ts4/',
);
$conf2 = include SITE_PATH . '/../../../config/config.inc.php';
$conf  = array_merge($conf, $conf2);
return $conf;
