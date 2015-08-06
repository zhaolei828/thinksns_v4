<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CurlDowload
 *
 * @author zhaolei
 */
class CurlDowload extends UploadFile{
    public function curlDownImage($fileUrl,$savePath =''){
        mkdir($savePath,0777,true);
        //如果不指定保存文件名，则由系统默认
        if(empty($savePath)) {
            $savePath = $this->savePath;
        }
        // 检查上传目录
        if(!is_dir($savePath)) {
            // 检查目录是否编码后的
            if(is_dir(base64_decode($savePath))) {
                $savePath = base64_decode($savePath);
            }else{
                // 尝试创建目录
                if(!mkdir($savePath,0777,true)){
                    $this->error  =  '上传目录'.$savePath.'不存在';
                    return false;
                }
            }
        }else {
            if(!is_writeable($savePath)) {
                $this->error  =  '上传目录'.$savePath.'不可写';
                return false;
            }
        }
        $file['extension'] = $this->getExt($fileUrl);
        if($file['extension'] == null || $file['extension'] == ''){
            $file['extension'] = 'jpg';
        }
        $file['savepath']   = $savePath;
        if(empty($this->saveName)){
            $file['savename']   = uniqid().'.'.$file['extension'];
        }else{
            $file['savename'] = $this->saveName;
        }
        $filename = $file['savepath'].$file['savename'];
        Http::curl_download($fileUrl, $filename);
        return $file;
    }
    private function getExt($filename)
    {
        $pathinfo = pathinfo($filename);
        return $pathinfo['extension'];
    }
}
