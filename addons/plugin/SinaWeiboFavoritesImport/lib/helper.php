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
    public function downOptions($input_options = null){
        $system_default = model('Xdata')->get('admin_Config:attach');
        if(empty($system_default['attach_path_rule']) || empty($system_default['attach_max_size']) || empty($system_default['attach_allow_extension'])) {
                $system_default['attach_path_rule'] = 'Y/md/H/';
                $system_default['attach_max_size'] = '2'; 		// 默认2M
                $system_default['attach_allow_extension'] = 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf';
                model('Xdata')->put('admin_Config:attach', $system_default);
        }
        // 上传若为图片，则修改为图片配置
        $image_default = model('Xdata')->get('admin_Config:attachimage');
        $system_default['attach_max_size'] = $image_default['attach_max_size'];
        $system_default['attach_allow_extension'] = $image_default['attach_allow_extension'];
        $system_default['auto_thumb'] = $image_default['auto_thumb'];
        $default_options = array();
        $default_options['custom_path']	= date($system_default['attach_path_rule']);					// 应用定义的上传目录规则：'Y/md/H/'
        $default_options['max_size'] = floatval($system_default['attach_max_size']) * 1024 * 1024;		// 单位: 兆
        $default_options['allow_exts'] = $system_default['attach_allow_extension']; 					// 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf'
        $default_options['save_path'] =	UPLOAD_PATH.'/'.$default_options['custom_path'];
        $default_options['save_name'] =	''; //指定保存的附件名.默认系统自动生成
        $default_options['save_to_db'] = true;
        $default_options['auto_thumb'] = $system_default['auto_thumb'];
        $options = is_array($input_options) ? array_merge($default_options,$input_options) : $default_options;
        return $options;
    }
}
