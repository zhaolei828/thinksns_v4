<?php
/**
 * Created by PhpStorm.
 * User: 伟
 * Date: 2015/7/21
 * Time: 11:10
 */

namespace Config;


class Thinksns
{

    protected static $loaded = false;
    protected static $configs = array();

    public static function get($key = null, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }
        if (null === $key) {
            return self::$configs;
        } elseif (isset(self::$configs[$key])) {
            return self::$configs[$key];
        } else {
            return $default;
        }
    }

    public static function load($config_file = null, $merge = true)
    {
        self::$loaded = true;
        if (!$config_file) {
            if (!defined('SITE_PATH')) return;
            $config_file = SITE_PATH . '/config/config.inc.php';
        }
        if (is_file($config_file)) {
            $configs = @include_once $config_file;
            if (is_array($configs)) {
                if (self::$configs && $merge) {
                    self::$configs = array_merge(self::$configs, $configs);
                } else {
                    self::$configs = $configs;
                }
                return true;
            }
        }
        return false;
    }
}