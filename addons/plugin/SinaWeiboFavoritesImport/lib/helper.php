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
    public function downOptions($data,$input_options = null){
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
    public function curlDownload($options,$fileArray,$data=null){
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
        foreach ($fileArray as $furl) {
            $file = $c->curlDownImage($furl);
            $filefullname= $file['savepath'].$file['savename'];
            if(function_exists($c->hashType)) {
                $fun =  $c->hashType;
                $file['hash']   =  $fun(auto_charset($filefullname,'utf-8','gbk'));
            }
            $image = exif_imagetype($file['savename']); 
            $mime_type = image_type_to_mime_type($image); 
            $file['type']= $mime_type;
            $file['size']=  filesize($filefullname);
            $file['uid'] = $data['uid'];
            $upload_info[] = $file;
        }
        
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
                        $data['uid'] = $u['uid'];
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
        $data['upload_type'] = 'image';
        $options = $this->downOptions($data);
        if( is_array( $ms['favorites'] ) ){
            foreach( $ms['favorites'] as $item ){
                $status = $item['status'];
                $uid = $this->saveWeiboUser($status);
                if($uid){
                    $wid = $status['id'];
                    $dbFeedId = model('Feed')->findFeedIdByWeiboId($wid);
                    if($dbFeedId){
                        continue;
                    }
                    $text = $status['text'];
                    $pic_urls = $status['pic_urls'];
                    $retweeted_status = $status['retweeted_status'];
                    $attachIds='|';
                    if(is_array($retweeted_status)){//转发内容
                        $rwid = $retweeted_status['id'];
                        $dbReFeedId = model('Feed')->findFeedIdByWeiboId($rwid);
                        if($dbReFeedId){
                            continue;
                        }
                        $retweeted_uid = $this->saveWeiboUser($retweeted_status);
                        $data['uid'] = $retweeted_uid;
                        $retweeted_text = $retweeted_status['text'];
                        $retweeted_pic_urls = $retweeted_status['pic_urls'];
                        if(is_array($retweeted_pic_urls)){
                            foreach($retweeted_pic_urls as $retweeted_pic_url){
                                $thumbnail_pic = $retweeted_pic_url['thumbnail_pic'];
                                $bmiddle_pic = str_replace("/thumbnail/","/bmiddle/",$thumbnail_pic);
                                $fileUrls[] = $bmiddle_pic;
                            }
                            $infos = $this->curlDownload($options, $fileUrls,$data);
                            foreach ($infos as $info) {
                                $aid = $info['attach_id'];
                                $attachIds.=$aid.'|';
                            }
                            unset($fileUrls);
                            unset($retweeted_pic_urls);
                        }
                        //保存转发的微博
                        $filterBodyStatus = filter_words ( $retweeted_text );
                        if (! $filterBodyStatus ['status']) {
                            $return = array (
                                            'status' => 0,
                                            'data' => $filterBodyStatus ['data'] 
                            );
                            exit ( json_encode ( $return ) );
                        }
                        $d ['weibo_id'] = $rwid;
                        $d ['body'] = $filterBodyStatus ['data'];
                        $d ['body'] = preg_replace ( "/#[\s]*([^#^\s][^#]*[^#^\s])[\s]*#/is", '#' . trim ( "\${1}" ) . '#', $d ['body'] );
                        $d ['attach_id'] = trim ( $attachIds , "|" );
                        if (! empty ( $d ['attach_id'] )) {
                            $d ['attach_id'] = explode ( '|', $d ['attach_id'] );
                            array_map ( 'intval', $d ['attach_id'] );
                        }
                        // 发送分享的类型
                        $type = 'postimage';
                        // 附件信息
                        // 所属应用名称
                        $app = 'public'; // 当前动态产生所属的应用
                        if (! $feedResult = model ( 'Feed' )->put ( $retweeted_uid, $app, $type, $d,0, 'feed',null,null,true,0,false)) {
                                $return = array (
                                                'status' => 0,
                                                'data' => model ( 'Feed' )->getError () 
                                );
                                exit ( json_encode ( $return ) );
                        }
                        $feed_id = $feedResult['feed_id'];
                        
                        //分享
                        $filterBodyStatusShare = filter_words ( $text );
                        if (! $filterBodyStatusShare ['status']) {
                                $return = array (
                                                'status' => 0,
                                                'data' => $filterBodyStatusShare ['data'] 
                                );
                                exit ( json_encode ( $return ) );
                        }
                        $post ['body'] = $filterBodyStatusShare ['data'];
                        $post ['curid'] = $feed_id;
                        $post ['sid'] = $feed_id;
                        
                        // 判断资源是否删除
                        if (empty ( $post ['curid'] )) {
                                $map ['feed_id'] = intval ( $post ['sid'] );
                        } else {
                                $map ['feed_id'] = intval ( $post ['curid'] );
                        }
                        $map ['is_del'] = 0;
                        $isExist = model ( 'Feed' )->where ( $map )->count ();
                        if ($isExist == 0) {
                                $return ['status'] = 0;
                                $return ['data'] = '内容已被删除，转发失败';
                                exit ( json_encode ( $return ) );
                        }
                        $post['type'] = 'feed';
                        $post['app_name'] = 'public';
                        $post['curtable'] = 'feed';
                        $post['weibo_id'] = $wid;
                        $shareResult = model ( 'Share' )->shareFeed ( $post, 'share',null,$uid,false );
                        
                        if ($shareResult ['status'] == 1) {
                            $app_name = $post ['app_name'];
			
                            // 添加积分
                            if ($app_name == 'public') {
                                    model ( 'Credit' )->setUserCredit ( $uid, 'forward_weibo' );
                                    // 分享被转发
                                    $suid = model ( 'Feed' )->where ( $map )->getField ( 'uid' );
                                    model ( 'Credit' )->setUserCredit ( $suid, 'forwarded_weibo' );
                            }
                        }
                        unset($retweeted_status);
                    }else{
                        $data['uid'] = $uid;
                        if(is_array($pic_urls)){
                            foreach($pic_urls as $pic_url){
                                $thumbnail_pic = $pic_url['thumbnail_pic'];
                                $bmiddle_pic = str_replace("/thumbnail/","/bmiddle/",$thumbnail_pic);
                                $fileUrls[] = $bmiddle_pic;
                            }
                            $infos = $this->curlDownload($options, $fileUrls,$data);
                            foreach ($infos as $info) {
                                $aid = $info['attach_id'];
                                $attachIds.=$aid.'|';
                            }
                            unset($fileUrls);
                            unset($pic_urls);
                        }
                        //保存微博
                        $filterBodyStatus = filter_words ( $text );
                        if (! $filterBodyStatus ['status']) {
                            $return = array (
                                            'status' => 0,
                                            'data' => $filterBodyStatus ['data'] 
                            );
                            exit ( json_encode ( $return ) );
                        }
                        $d ['weibo_id'] = $wid;
                        $d ['body'] = $filterBodyStatus ['data'];
                        $d ['body'] = preg_replace ( "/#[\s]*([^#^\s][^#]*[^#^\s])[\s]*#/is", '#' . trim ( "\${1}" ) . '#', $d ['body'] );
                        $d ['attach_id'] = trim ( $attachIds , "|" );
                        if (! empty ( $d ['attach_id'] )) {
                            $d ['attach_id'] = explode ( '|', $d ['attach_id'] );
                            array_map ( 'intval', $d ['attach_id'] );
                        }
                        // 发送分享的类型
                        $type = 'postimage';
                        // 附件信息
                        // 所属应用名称
                        $app = 'public'; // 当前动态产生所属的应用
                        if (! $data = model ( 'Feed' )->put ( $uid, $app, $type, $d,0, 'feed',null,null,true,0,false)) {
                                $return = array (
                                                'status' => 0,
                                                'data' => model ( 'Feed' )->getError () 
                                );
                                exit ( json_encode ( $return ) );
                        }
                    }
                    
                }
            }
        }
    }
    private function saveWeiboUser($status){
        $weiboUser = $status['user'];
        $weiboId = $weiboUser['id'];//微博用户id，避免重复插入
        if($weiboId && $weiboId>0){
            $uid = M('User')->findUnameByWeiboId($weiboId);
            if(!$uid){
                $weiboName = $weiboUser['name'];//用户名
                $uid2 = M('User')->isChangeUserName($weiboName);
                if(!$uid2){
                    $map['uname'] = $weiboName.'_'.$weiboId;
                }  else {
                    $map['uname'] = $weiboName;
                }

                $map['weibo_id'] = $weiboId;
                $map['intro'] = $weiboUser['description'];//描述
                //$map[''] = $weiboUser['location'];//北京 朝阳区
                $map['sex'] = $weiboUser['gender']=='m'?1:2;//m f 性别
                $password = 'fromweibo6jlife';
                $login_salt = rand(11111, 99999);
                $map['login_salt'] = $login_salt;
                $map['password'] = md5(md5($password).$login_salt);
                $map['reg_ip'] = get_client_ip();
                $map['ctime'] = time();
                $map['is_audit'] = 1;
                $map['is_active'] = 1;
                $map['is_init'] = 1;
                $map['first_letter'] = getFirstLetter($map['uname']);
                //如果包含中文将中文翻译成拼音
                if ( preg_match('/[\x7f-\xff]+/', $map['uname'] ) ){
                        //昵称和呢称拼音保存到搜索字段
                        $map['search_key'] = $map['uname'].' '.model('PinYin')->Pinyin( $map['uname'] );
                } else {
                        $map['search_key'] = $map['uname'];
                }
                $uid = M('User')->add($map);
                $avatarLargeUrl = $weiboUser['avatar_large'];//头像大图
                $data['upload_type'] = 'image';
                $options = $this->downOptions($data);
                $options['custom_path']='avatar' . model('Avatar')->convertUidToPath($uid).'/';
                $options['save_name']='original.jpg';
                $options['save_path'] = UPLOAD_PATH . '/avatar' . model('Avatar')->convertUidToPath($uid).'/' ;
                $fileArray[] = $avatarLargeUrl;
//                echo '<pre>';print_r($options);echo '</pre>';
                $infos = $this->curlDownload($options, $fileArray,$data);
//                echo '<pre>';print_r($infos);echo '</pre>';
                unset($fileArray);
                if($uid) {
                        // 添加积分
                        model('Credit')->setUserCredit($uid,'init_default');
                        // 如果是邀请注册，则邀请码失效

                        // 添加至默认的用户组
                        $userGroup = model('Xdata')->get('admin_Config:register');
                        $userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
                        model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));

                        //注册来源-第三方帐号绑定

                        //判断是否需要审核
                    return $uid;
                } else {
                        // 注册失败
                    return null;
                }
            }  else {
                return $uid;
            }
        }else{
            return null;
        }
    }
}
