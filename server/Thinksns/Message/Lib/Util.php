<?php
/**
 * Created by PhpStorm.
 * User: 伟
 * Date: 2015/7/17
 * Time: 14:12
 */

namespace Lib;


class Util {

    /**
     * 取得指定session name的值
     * @param $name session名称
     * @param null $default 默认值
     * @return mixed
     */
    public static function getSession($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    /**
     * 设置一个session
     * @param $name session的名称
     * @param $value session的值
     * @return void
     */
    public static function setSession($name, $value)
    {
        if(null === $name){
            $_SESSION = array();
        }elseif(null === $value){
            unset($_SESSION[$name]);
        }else{
            $_SESSION[$name] = $value;
        }
    }

    /**
     * 检查一个session name是否存在
     * @param $name session名称
     * @return bool
     */
    public static function hasSession($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * html编码，默认包括单引号
     * @param string $string
     * @param integer $flags
     * @return string
     */
    public static function htmlEncode($string, $flags = ENT_QUOTES, $charset = 'UTF-8')
    {
        return htmlspecialchars($string, $flags, $charset);
    }

    /**
     * html编码，默认包括单引号
     * @param string $string
     * @param integer $flags
     * @return string
     */
    public static function htmlDecode($string, $flags = ENT_QUOTES)
    {
        return htmlspecialchars_decode($string, $flags);
    }

    /**
     * 返回数组中指定的一列，可兼容php5.5 array_column函数
     * 注意：只能兼容通过键名指定列，不支持通过整数索引指定列，使用键名指定通常已经够用
     * @param array $input 需要取出数组列的多维数组（或结果集）
     * @param mixed $column_key 需要返回值列的键名或NULL
     * @param mixed $index_key 作为返回数组的索引/键的列，该列的键名。
     * @return array 从多维数组中返回单列数组或重置列索引或键名的数组
     */
    public static function arrayColumn(array $input ,$column_key, $index_key = null){
        if(function_exists('array_column')){
            return array_column($input, $column_key, $index_key);
        }
        $array = array();
        foreach($input as $key => $val) {
            $key = null === $index_key ? $key : $val[$index_key];
            $val = null === $column_key ? $val : $val[$column_key];
            $array[$key] = $val;
        }
        return $array;
    }

    /**
     * 取得一个16位的随机字符串
     * return string
     */
    public static function randString(){
        $str = str_shuffle('0123456789abcdef');
        $str = substr($str, rand(0, 13), 3);
        return uniqid() . $str;
    }

    public static function desEncrypt($string, $key = null){
        if($key === null){
            $key = \Config\Thinksns::get('SECURE_CODE', '');
        }
        $des = new \Lib\TsDesMobile();
        return $des->setKey($key)->encrypt($string);
    }

    public static function desDecrypt($string, $key = null){
        if($key === null){
            $key = \Config\Thinksns::get('SECURE_CODE', '');
        }
        $des = new \Lib\TsDesMobile();
        return $des->setKey($key)->decrypt($string);
    }

    /**
     * 将一个包含id列表的数组或字符串格式化为标准的逗号分隔值
     * @param array|string $ints 需要整理的id列表
     * @param string $default 如果列表中没有符合的ID，则返回此值
     * @param integer $type 设置列表中int的类型，0:允许0和正数，1:只允许正数，其他:不限制
     * @param boolean $unique 是否需要去除重复
     * @return string 整理好的字符串，如果没有则返回默认值
     */
    public static function formatIntList($ints, $default = '', $type = 1, $unique = true){
        if(!is_array($ints)) {
            $ints = explode(',', $ints);
        }
        $list = array();
        foreach($ints as $int){
            $int = intval(trim($int));
            if($type == 1 || $type == 0){
                if($int >= $type) $list[] = $int;
            }else{
                $list[] = $int;
            }
        }
        if(!$list) return $default;
        if($unique) $list = array_unique($list);
        return implode(',', $list);
    }
}