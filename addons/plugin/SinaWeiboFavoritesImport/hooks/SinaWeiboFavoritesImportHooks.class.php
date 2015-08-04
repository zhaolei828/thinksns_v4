<?php
//include_once( '../lib/config.php' );
//include_once( '../lib/saetv2.ex.class.php' );
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SinaWeiboFavoritesHooks
 *
 * @author zhaolei
 */
class SinaWeiboFavoritesImportHooks extends Hooks {
    
    public function config(){
        include_once $this->path . "/lib/config.php";
        include_once $this->path . "/lib/saetv2.ex.class.php";
        session_start();
        $token = $_SESSION['token'];
        if (!$token) {
            $o = new SaeTOAuthV2(WB_AKEY , WB_SKEY );
            $code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
            $this->assign('code_url',$code_url);
            $this->display('config'); 
        }else{
            $this->importFavoritesWeibo(); 
        }
    }
    public function importFavoritesWeibo(){
        include_once $this->path . "/lib/config.php";
        include_once $this->path . "/lib/saetv2.ex.class.php";
        session_start();
        $token = $_SESSION['token'];
        if(!$token){
            $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

            if (isset($_REQUEST['code'])) {
                    $keys = array();
                    $keys['code'] = $_REQUEST['code'];
                    $keys['redirect_uri'] = WB_CALLBACK_URL;
                    try {
                            $token = $o->getAccessToken( 'code', $keys ) ;
                    } catch (OAuthException $e) {
                    }
            }
            if ($token) {
                $_SESSION['token'] = $token;
                setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
            }
        }
        $oauth_code = 0;
        if($token){
            $oauth_code = 1;
            $oauth_result = '授权成功';
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
            $ms  = $c->get_favorites();
        }else{
            $ms =  array(
            'post','repost','postimage','postfile',
            'weiba_post','weiba_repost',
            'blog_post', 'blog_repost',
            'event_post', 'event_repost',
            'vote_post', 'vote_repost',
            'photo_post', 'photo_repost');
            $oauth_result = '授权失败';
        }
        $this->assign('oauth_code',$oauth_code);
        $this->assign('oauth_result',$oauth_result);
        $this->assign('ms',$ms);
        $this->display('myfavorites');
    }
    public function test(){
        require_once SITE_PATH.'/addons/library/CurlDowload.class.php';
        include_once $this->path . "/lib/helper.php";
        $params['file_url'] = 'http://demo.thinksns.com/ts4/data/upload/2015/0803/18/55bf45248f2bd.jpg';
        $helper = new helper();
        $options = $helper->downOptions();
        echo '==================================================';
        echo '<pre>';print_r($options);echo '</pre>';
        $c = new CurlDowload($options['max_size'], $options['allow_exts'], $options['allow_types']);
        $c->savePath = $options['save_path'];
        $c->autoSub = false;
        $c->saveName = $options['save_name'];
        $c->saveRule = $options['save_rule'];
        mkdir($c->save_path, 0777, true);
        $file = $c->curlDownImage($params['file_url']);
        $file['custom_path']   = $options['custom_path'];
        echo '==================================================';
        echo '<pre>';print_r($file);echo '</pre>';
        if ($options['auto_thumb'] == 1) {
            $width = 120;
            $height = 120;
            $cut = true;
            $info = getThumbImage ( $file['custom_path']. $file['save_name'], $width, $height, $cut );
            echo '==================================================';
            echo '<pre>';print_r($info);echo '</pre>';
            echo UPLOAD_URL . $info['src'];
            $file['thumb']=$info;
            echo '==================================================';
            echo '<pre>';print_r($file);echo '</pre>';
        } 
    }
}
