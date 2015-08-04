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
        include_once $this->path . "/lib/helper.php";
        $data['file_url'] = 'http://ww4.sinaimg.cn/bmiddle/6b658c6bjw1euiacvskhmj20hs0cdtam.jpg';
        $data['upload_type'] = 'image';
        $data['thumb_w'] = 240;
        $data['thumb_h'] = 'auto';
        $data['thumb_cut'] = false;
        $data['auto_thumb'] = 1;
        $helper = new helper();
        $options = $helper->downOptions(null,$data);
        echo '<pre>';print_r($options);echo '</pre>';
        $infos = $helper->curlDownload($options,$data);
        echo '<pre>';print_r($infos);echo '</pre>';
//        $attachModel = D('AttachModel');
//        $attachModel->save();
    }
}
