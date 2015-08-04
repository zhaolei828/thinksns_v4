<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of helper
 *
 * @author zhaolei
 */
class helper {
    public function downOptions($input_options = null,$data){
        $system_default = model('Xdata')->get('admin_Config:attach');
        if(empty($system_default['attach_path_rule']) || empty($system_default['attach_max_size']) || empty($system_default['attach_allow_extension'])) {
                $system_default['attach_path_rule'] = 'Y/md/H/';
                $system_default['attach_max_size'] = '2'; 		// 默认2M
                $system_default['attach_allow_extension'] = 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf';
                model('Xdata')->put('admin_Config:attach', $system_default);
        }
        // 上传若为图片，则修改为图片配置
        if ($data['upload_type'] === 'image') {
                $image_default = model('Xdata')->get('admin_Config:attachimage');
                $system_default['attach_max_size'] = $image_default['attach_max_size'];
                $system_default['attach_allow_extension'] = $image_default['attach_allow_extension'];
                $system_default['auto_thumb'] = $image_default['auto_thumb'];
        }
        $default_options = array();
        $default_options['custom_path']	= date($system_default['attach_path_rule']);					// 应用定义的上传目录规则：'Y/md/H/'
        $default_options['max_size'] = floatval($system_default['attach_max_size']) * 1024 * 1024;		// 单位: 兆
        $default_options['allow_exts'] = $system_default['attach_allow_extension']; 					// 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf'
        $default_options['save_path'] =	UPLOAD_PATH.'/'.$default_options['custom_path'];
        $default_options['save_name'] =	''; //指定保存的附件名.默认系统自动生成
        $default_options['save_to_db'] = true;
        $options = is_array($input_options) ? array_merge($default_options,$input_options) : $default_options;
        return $options;
    }
    public function curlDownload($options,$data){
        require_once SITE_PATH.'/addons/library/CurlDowload.class.php';
//        $data['file_url'] = 'http://ww4.sinaimg.cn/bmiddle/6b658c6bjw1euiacvskhmj20hs0cdtam.jpg';
//        $data['upload_type'] = 'image';
//        $data['thumb_w'] = 240;
//        $data['thumb_h'] = 'auto';
//        $data['thumb_cut'] = false;
//        $data['auto_thumb'] = 1;
//        $options = $this->downOptions(null,$data);
        $c = new CurlDowload($options['max_size'], $options['allow_exts'], $options['allow_types']);
        $c->savePath = $options['save_path'];
        $c->autoSub = false;
        $c->saveName = $options['save_name'];
        $c->saveRule = $options['save_rule'];
        mkdir($c->save_path, 0777, true);
        $file = $c->curlDownImage($data['file_url']);
//        $file['custom_path']   = $options['custom_path'];
//        if ($data['auto_thumb'] == 1) {
//            $info = getThumbImage ( $file['custom_path']. $file['save_name'], $data['thumb_w'], $data['thumb_h'], $data['thumb_cut'] );
//            echo '<pre>';print_r($info);echo '</pre>';
//            return $info;
//        } else{
//            $info['dirname']=$file['custom_path'];
//            $info['basename']=$file['save_name'];
//            $info['extension']=$file['extension'];
//            return $info;
//        }
        $filefullname= $file['savepath'].$file['savename'];
        if(function_exists($c->hashType)) {
            $fun =  $c->hashType;
            $file['hash']   =  $fun(auto_charset($filefullname,'utf-8','gbk'));
        }
        $image = exif_imagetype($file['savename']); 
        $mime_type = image_type_to_mime_type($image); 
        $file['type']= $mime_type;
        $file['size']=  filesize($filefullname);
        $upload_info = array();
        array_push($upload_info, $file);
        $infos = $this->saveInfo($upload_info,$options);
        return $infos;
    }
    private function saveInfo($upload_info,$options){
            $data = array(
                    'table' => t($data['table']),
                    'row_id' => t($data['row_id']),
                    'app_name' => t($data['app_name']),
                    'attach_type' => t($options['attach_type']),
                    'uid' =>  (int) $data['uid'] ? $data['uid'] : $GLOBALS['ts']['mid'],
                    'ctime' => time(),
                    'private' => $data['private'] > 0 ? 1 : 0,
                    'is_del' => 0,
                    'from' => isset($data['from']) ? intval($data['from']) : getVisitorClient(),
            );
//            echo '<pre>';print_r($data);echo '</pre>';
            if($options['save_to_db']) {
                    foreach($upload_info as $u) {
                        $name = t($u['name']);
                        $data['name'] = $name ? $name : $u['savename'];
                        $data['type'] = $u['type'];
                        $data['size'] = $u['size'];
                        $data['extension'] = strtolower($u['extension']);
                        $data['hash'] = $u['hash'];
                        $data['save_path'] = $options['custom_path'];
                        $data['save_name'] = $u['savename'];
                        if (in_array(strtolower($u['extension']), array('jpg', 'gif', 'png', 'jpeg', 'bmp')) && !in_array($options['attach_type'], array('feed_file', 'weiba_attach'))) {
                                list($width, $height) = getImageInfo($data['save_path'].$data['save_name']);
                                $data['width'] = $width;
                                $data['height'] = $height;
                        } else {
                                $data['width'] = 0;
                                $data['height'] = 0;
                        }
//                        echo '<pre>';print_r($data);echo '</pre>';
                        $aid = M('Attach')->add($data);
//                        echo  M('Attach')->getLastSql();
                        $data['attach_id'] = intval($aid);
                        $data['key'] = $u['key'];
                        $data['size'] = byte_format($data['size']);
//                        echo '<pre>';print_r($data);echo '</pre>';
                        $infos[] = $data;
                        unset($data['attach_id']);
                        unset($data['key']);
                        unset($data['size']);
                    }
            } else {
                    foreach($upload_info as $u) {
                        $name = t($u['name']);
                        $data['name'] = $name ? $name : $u['savename'];
                        $data['type'] = $u['type'];
                        $data['size'] = byte_format($u['size']);
                        $data['extension'] = strtolower($u['extension']);
                        $data['hash'] = $u['hash'];
                        $data['save_path'] = $options['custom_path'];
                        $data['save_name'] = $u['savename'];
                        //$data['save_domain'] = C('ATTACH_SAVE_DOMAIN'); 	//如果做分布式存储，需要写方法来分配附件的服务器domain
                        $data['key'] = $u['key'];
                        $infos[] = $data;
                    }
            }
            return $infos;
	}
    public function jxWeibo($ms){
        if( is_array( $ms['favorites'] ) ){
            foreach( $ms['favorites'] as $item ){
                $status = $item['status'];
                $weiboUser = $status['user'];
                $map[''] = $weiboUser['name'];//用户名
                $map[''] = $weiboUser['description'];//描述
                $map[''] = $weiboUser['profile_image_url'];//头像
            }
        }
    }    
}
