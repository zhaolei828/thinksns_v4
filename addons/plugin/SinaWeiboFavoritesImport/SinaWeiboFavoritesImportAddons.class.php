<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SinaWeiboFavoritesImportAddons
 *
 * @author zhaolei
 */
class SinaWeiboFavoritesImportAddons extends NormalAddons{
    protected $version = "1.0";
    protected $author  = "六角生活";
    protected $site    = "http://6jlife.com";
    protected $info    = "将新浪微博的收藏内容导入到自己的ts里";
    protected $pluginName = "从新浪微博收藏导入";
    protected $tsVersion = '4.0';
    public function getHooksInfo() {
        $hooks['list']=array('SinaWeiboFavoritesImportHooks');
        return $hooks;
    }
    public function adminMenu(){
        $menu = array();
        $menu['config'] = '从新浪微博收藏导入';
        return $menu;
    }
    public function start(){
        return true;
    }
    public function install(){
        return true;
    }
    public function uninstall(){
        return true;
    }
}
