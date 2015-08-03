<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of DownloadFileByUrlHooks
 *
 * @author zhaolei
 */
class DownloadFileByCurlHooks extends Hooks{
    // 默认文件保存路径
    public $savePath = '';
    
    public function __construct($maxSize='',$allowExts='',$allowTypes='',$savePath=UPLOAD_PATH,$saveRule='')
    {
        if(!empty($maxSize) && is_numeric($maxSize)) {
            $this->maxSize = $maxSize;
        }
        if(!empty($allowExts)) {
            if(is_array($allowExts)) {
                $this->allowExts = array_map('strtolower',$allowExts);
            }else {
                $this->allowExts = explode(',',strtolower($allowExts));
            }
        }
        if(!empty($allowTypes)) {
            if(is_array($allowTypes)) {
                $this->allowTypes = array_map('strtolower',$allowTypes);
            }else {
                $this->allowTypes = explode(',',strtolower($allowTypes));
            }
        }
        if(!empty($saveRule)) {
            $this->saveRule = $saveRule;
        }else{
            $this->saveRule	=	C('UPLOAD_FILE_RULE');
        }
        $this->savePath = $savePath;
    }
    
    function curlDownloadImg($file_url, $savePath =''){
        mkdir($savePath,0777,true);
        //如果不指定保存文件名，则由系统默认
        if(empty($savePath)) {
            $savePath = $this->savePath;
        }
    }
}
