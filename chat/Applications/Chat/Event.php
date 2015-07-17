<?php
/**
 * 
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 * @author walkor < walkor@workerman.net >
 * 
 */
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Store;

class Event
{
   
   /**
	* 有消息时
	* @param int $client_id
	* @param string $message
	*/
   public static function onMessage($client_id, $message)
   {
		// debug
		//echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
		// 客户端传递的是json数据
		$message_data = json_decode($message, true);
		if(!$message_data){
			return ;
		}
		// 根据类型执行不同的业务
		switch($message_data['type']){
			// 客户端回应服务端的心跳
			case 'pong':
                $uid = intval($message_data['uid']);
                $oauth_token = @htmlspecialchars($message_data['oauth_token']);
                $oauth_token_secret =  @htmlspecialchars($message_data['oauth_token_secret']);
				$res = self::getSociaxObject()->pingpong($client_id,$uid,$oauth_token);
				if(!$res){
				   $msg['type'] = 'remote_login';
				}else{
                   $msg['type'] = 'remote_ready';
                }
                Gateway::sendToCurrentClient(json_encode($msg));
				break;
			// 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
			case 'login':
				Gateway::sendToCurrentClient(json_encode($msg));
				$from_uid = (int) $message_data['from_uid'];
				// $from_avatar = self::getSociaxObject()->getUserAvatar($from_uid);
				$from_uname = htmlspecialchars($message_data['from_uname']);
				$oauth_token =  @htmlspecialchars($message_data['oauth_token']);
				$oauth_token_secret =  @htmlspecialchars($message_data['oauth_token_secret']);
				$status = (int) self::getSociaxObject()->checkLogin($from_uid, $oauth_token, $oauth_token_secret);
				// $msg = '{"type":"login","status":'.$status.',"client_id":'.$client_id.',"from_uid":'. $from_uid.',"from_uname":"'. $from_uname.'","from_avatar":"'. $from_avatar.'","time":"'. time().'"}';
				$msg['type'] = "login";
				$msg['status'] = $status;
				$msg['client_id'] = $client_id;
				$msg['from_uid'] = $from_uid;
				$msg['from_uname'] = $from_uname;
				// $msg['from_avatar'] = $from_avatar;
				$msg['time'] = (string)time();
				//添加ClientID到数据库
				self::getSociaxObject()->addClient($client_id, $from_uid);

				//发给登录信息给自己
				Gateway::sendToCurrentClient(json_encode($msg));

				//发送离线消息给自己
				// $messages = self::getSociaxObject()->getOfflineMessage($from_uid);
				// $log['from_uid'] = $from_uid;
				// $log['messages'] = $messages;
				// $log_message = "============================ \n "
				//        ." \n ".var_export($log,true)." \n ";
				// $log_file = "/home/wwwroot/workerman-chat/applications/Chat/log.txt";
				// error_log($log_message, 3, $log_file);

				// if($messages){
				//   foreach ($messages as $msg) {
				//     $msg['content'] = urldecode($msg['content']);
				//     Gateway::sendToCurrentClient(json_encode($msg['content']));
				//   }
				// }
				return;
			case 'say':
			  $list_id = @intval($message_data['list_id']);
			  $from_uid = @intval($message_data['from_uid']);
			  $from_avatar = (string)$message_data['from_avatar'];
			  $from_uname = @htmlspecialchars($message_data['from_uname']);
			  // $from_avatar = @self::getSociaxObject()->getUserAvatar($from_uid);
			  $to_uid = $message_data['to_uid'];
			  // $to_avatar = @self::getSociaxObject()->getUserAvatar($to_uid);
			  $content = @nl2br(htmlspecialchars($message_data['content']));
			  $msg_id = @intval($message_data['msg_id']);

			  $data = self::getSociaxObject()->get_clients_and_uids($to_uid);

			  if($data['online_client_array']){ //发送在线
				  $msg['type'] = 'say';
				  $msg['list_id'] = (string)$list_id;
				  $msg['msg_id'] = (string)$msg_id;
				  $msg['from_uname'] = (string)$from_uname;
				  $msg['content'] = (string)$content;
				  $msg['time'] = (string)time();
				  // Gateway::sendToCurrentClient(json_encode(array('client_ids'=>implode(',', $data['online_client_array']))));
				  Gateway::sendToAll(json_encode($msg), $data['online_client_array']);
			  }
			  if($data['offline_uids'] || 1){    // 不在线，极光推送离线
				  $from_uname = @htmlspecialchars($message_data['from_uname']);
				  // Gateway::sendToCurrentClient(json_encode(array('uids'=>implode(',', $data['offline_uids']))));
				  $data['type'] = 'say';
				  $data['list_id'] = (string)$list_id;
				  // self::jpush($data, $data['offline_uids'], $message_data['from_uname'].'给您发了新消息：'.$message_data['content'], $from_uid, 0);
				  self::jpush($data, array_unique(array_filter(explode(',', $to_uid))), $message_data['from_uname'].'给您发了新消息：'.$message_data['content'], $from_uid, 0);
			  }
			  Gateway::sendToCurrentClient(json_encode(array('status' => '1')));
			  break;
			case 'notify':
			  $list_id  = (string) @intval($message_data['list_id']);
			  $msg_id  = (string) @intval($message_data['msg_id']);
			  $from_uid = (int) $message_data['from_uid'];
			  $from_uname = @htmlspecialchars($message_data['from_uname']);
			  $title =  (string) @htmlspecialchars($message_data['title']);
			  $content =  $message_data['content']; //格式：除被操作者之外的成员接收到的离线消息|被操作者接收到的离线消息
			  $to_uid = $message_data['to_uid'];    //格式：全部接收者uid|被操作者uid

			  $content_arr = explode('|', $content);
			  $to_uid_arr = explode('|', $to_uid);

			  $data = self::getSociaxObject()->get_clients_and_uids($to_uid_arr[0]);
			  if($data['online_client_array']){ //发送在线
				  $msg['type'] = 'notify';
				  $msg['list_id'] = (string)$list_id;
				  $msg['msg_id'] = (string)$msg_id;
				  $msg['time'] = (string)time();
				  Gateway::sendToCurrentClient(json_encode(array('client_ids'=>implode(',', $data['online_client_array']))));
				  Gateway::sendToAll(json_encode($msg), $data['online_client_array']);
			  }
			  if($data['offline_uids']  || 1){    // 不在线，极光推送离线
				  // Gateway::sendToCurrentClient(json_encode(array('uids'=>implode(',', $data['offline_uids']))));
				  $to_uids = array_unique(array_filter(explode(',', $to_uid)));
				  if(count($content_arr)==1){ //创建群聊或修改群聊标题
					self::jpush(array('type'=>'say'), $to_uids, $content_arr[0], $from_uid, 0);  //消息中显示‘您’
				  }else{
					$controlled_uids = explode(',', $to_uid_arr[1]);
					foreach ($to_uids as $k => $v) {
					  if(in_array($v, $controlled_uids)){ //是被操作者
						$uids1[] = $v;
					  }else{
						$uids2[] = $v;
					  }
					}
					self::jpush(array('type'=>'say'), $uids2, $content_arr[0], $from_uid, 0);  //消息中显示成员uname
					self::jpush(array('type'=>'say'), $uids1, $content_arr[1], $from_uid, 0);  //消息中显示‘您’
				  }
				  
			  }
			  Gateway::sendToCurrentClient(json_encode(array('status' => '1')));
			  break;
			case 'comment':
			case 'digg':
			case 'message':
			  $to_uid = $message_data['to_uid'];
			  $data = self::getSociaxObject()->get_clients_and_uids($to_uid);
			  if($data['online_client_array']){ //发送在线
				  $msg['type'] = $message_data['type'];
				  $unread_arr = explode(',', $message_data['unread']);
				  $msg['unread_comment'] = (string)$unread_arr[0];
				  $msg['unread_digg'] = (string)$unread_arr[1];
				  $msg['unread_message'] = (string)$unread_arr[2];
				  // Gateway::sendToCurrentClient(json_encode(array('client_ids'=>implode(',', $data['online_client_array']))));
				  Gateway::sendToAll(json_encode($msg), $data['online_client_array']);
			  }
			  if($data['offline_uids'] || 1){    // 不在线，极光推送离线
				  $from_uname = @htmlspecialchars($message_data['from_uname']);
				  // Gateway::sendToCurrentClient(json_encode(array('uids'=>implode(',', $data['offline_uids']))));
				  $data['type'] = $message_data['type'];
				  if($message_data['type'] == 'comment'){
					$content = $message_data['from_uname'].'评论了您的帖子';
				  }else if($message_data['type'] == 'digg'){
					$content = $message_data['from_uname'].'赞了您的帖子';
				  }else if($message_data['type'] == 'message'){
					$content = $message_data['from_uname'].'申请加您为好友';
				  }
				  // self::jpush($data, $data['offline_uids'], $content, $from_uid, 0);
				  self::jpush($data, array_unique(array_filter(explode(',', $to_uid))), $content, $from_uid, 0);
			  }
			  Gateway::sendToCurrentClient(json_encode(array('status' => '1')));
			  break;
			default:
				Gateway::sendToCurrentClient('不存在的操作');
				break;
		}
   }
   
	/**
	 * 当客户端断开连接时
	 * @param integer $client_id 客户端id
	 */
	public static function onClose($client_id){
	   // debug
	   echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
	   
	   // 从房间的客户端列表中删除
	   if(isset($_SESSION['room_id']))
	   {
		   $room_id = $_SESSION['room_id'];
		   self::delClientFromRoom($room_id, $client_id);
		   // 广播 xxx 退出了
		   if($all_clients = self::getClientListFromRoom($room_id))
		   {
			   $client_list = self::formatClientsData($all_clients);
			   $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'client_list'=>$client_list, 'time'=>date('Y-m-d H:i:s'));
			   $client_id_array = array_keys($all_clients);
			   Gateway::sendToAll(json_encode($new_message), $client_id_array);
		   }
	   }
	}
   
	/**
	* getSociaxObject
	*/
	public static function getSociaxObject(){  
	  // 连接数据库
	  // include dirname(__FILE__) . '/SociaxChat.class.php';
		ini_set('error_log', dirname(__FILE__) . '/log');
		include_once dirname(__FILE__) . '/SociaxChat.class.php';
	  	$config = include dirname(__FILE__) . '/config.inc.php';
	  // dump(getDbObject());
		$sociax = new SociaxChat($config);
		//重新链接数据库
		$sociax->checkConnect();
	  return $sociax;
	}

	/**
	 * getSociaxObject
	 */
	public static function jpush($data, $uids, $content, $mid, $room_id){
	  include_once 'Jpush.class.php';
	  $config = include 'config.inc.php';
	  $jpush = new Jpush($config);
	  if( in_array($data['type'], array('wenzhen_ask','wenzhen_say','wenzhen_assisstant_say','wenzhen_assisstant_ask','say')) ){
		$extras = array (
			'type' => $data['type'],
			'params' => array(
				'list_id' => $data['list_id']?$data['list_id']:0,
				'qid' => $data['qid']?$data['qid']:0,
			  ),
		);
	  }
	  if(is_numeric($uids)){  //一个人
		if($uids!=intval($mid)){
		  $jpush->pushByUID($uids, $content, $extras);
		}
	  }elseif(is_array($uids)){  //多个人
		foreach ($uids as $uid) {
		  if($uid!=$mid)
			$jpush->pushByUID($uid, $content, $extras);
		}
	  }
	}
}