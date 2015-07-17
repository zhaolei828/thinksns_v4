<?php
class Jpush {
	private $app_key = '';
	private $master_secret = '';
	private $error = '';
	private $url = "https://api.jpush.cn/v3/push";      //推送的地址
	private $production = 1; //是否生产环境

	function __construct($config = array()) {
		if (! empty ( $config )) {
			$this->app_key = $config ['JPUSH_KEY'];
			$this->master_secret = $config ['JPUSH_SECRET'];
		}
	}
	
	public function push($receiver='all',$content='',$extras=''){
        $base64=base64_encode("$this->app_key:$this->master_secret");
        $header=array("Authorization:Basic $base64","Content-Type:application/json");
        $data = array();
        $data['platform'] = 'android,ios';          //目标用户终端手机的平台类型android,ios,winphone
        $data['audience'] = $receiver;      //目标用户。all 或 tag 或 alias
        $data['notification'] = array(
            //安卓自定义
            "alert"=>$content,
            "android"=>array(    
                // "alert"=>$content,
                // "title"=>"",
                "builder_id"=>1,
            ),
            //ios的自定义
            "ios"=>array(
                // "alert"=>$content,
                "badge"=>"+1",
                "sound"=>"default",
            ),
        );
        if($extras){
        	$data['notification']['ios']['extras'] = $extras;
        	$data['notification']['android']['extras'] = $extras;
        }
        $data['options'] = array(
            "sendno" => time(),
            "apns_production" => $this->production,    //指定 APNS 通知发送环境：0开发环境，1生产环境。
        );

        $param = json_encode($data);
        $res = $this->push_curl($param,$header);
         
        // if($res){       //得到返回值--成功已否后面判断
        //     return $res;
        // }else{          //未得到返回值--返回失败
        //     return false;
        // }
        $res_arr = json_decode($res, true);
		if (count($res_arr['error'])){
			return array('status'=>0,'error'=>implode('-', $res_arr['error']));
		}else{
			return array('status'=>1);
		}
    }
 
    //推送的Curl方法
    public function push_curl($param="",$header="") {
        if (empty($param)) { return false; }
        $postUrl = $this->url;
        $curlPost = $param;
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);           // 增加 HTTP Header（头）里的字段 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch);
        return $data;
    }

	// public function getError(){
	// 	return $this->error;
	// }

	//通过UID对单人推送
	public function pushByUID($uid=0, $content='', $extras=array()){
		if(!$uid || !$content){
			return false;
		}

		$res = $this->push( array('alias'=>array($uid)) , $content, $extras);
		if(!$res['status']){
			$log['uid'] = $uid;
            $log['push_content'] = $content;
            $log['errors'] = $res['error'].'aaa';
            $log_message = "============================ \n "
            ." \n ".var_export($log,true)." \n ";
            $log_file = "/home/wwwroot/chat.comper.cn/Applications/Chat/log.txt";
            error_log($log_message, 3, $log_file);
			return false;
		}else{
			return true;
		}
	}
}