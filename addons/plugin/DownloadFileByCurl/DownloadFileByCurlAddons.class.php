<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DownloadFileByCurlAddons extends NormalAddons{
    protected $version = "1.0";
    protected $author  = "六角生活";
    protected $site    = "http://6jlife.com";
    protected $info    = "下载网络图片到服务器";
    protected $pluginName = "下载网络图片";
    protected $tsVersion = '4.0';
    public function adminMenu() {
        
    }

    public function getHooksInfo() {
        $hooks['list']=array('DownloadFileByCurlHooks');
        return $hooks;
    }

    public function install() {
        return true;
    }

    public function start() {
        return true;
    }

    public function uninstall() {
        return true;
    }

}
