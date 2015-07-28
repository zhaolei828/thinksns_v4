<?php
/**
 * Created by PhpStorm.
 * User: 伟
 * Date: 2015/7/17
 * Time: 15:04
 */

namespace Config;


class Db {

    public static $thinksns = array(
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'password' => '',
        'dbname'   => 'thinksns_4_0',
        'charset'  => 'utf8',
    );

}

// 同步Thinksns Db Config
Db::$thinksns['host'] = Thinksns::get('DB_HOST', Db::$thinksns['host']);
Db::$thinksns['port'] = Thinksns::get('DB_PORT', Db::$thinksns['port']);
Db::$thinksns['user'] = Thinksns::get('DB_USER', Db::$thinksns['user']);
Db::$thinksns['password'] = Thinksns::get('DB_PWD', Db::$thinksns['password']);
Db::$thinksns['dbname']  = Thinksns::get('DB_NAME', Db::$thinksns['dbname']);
Db::$thinksns['charset'] = Thinksns::get('DB_CHARSET', Db::$thinksns['charset']);