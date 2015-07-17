<?php
class SociaxChat extends SociaxDB {

    //检查重连
    public function checkConnect(){
        return $this->ping();
    }

    //验证用户权限
    public function checkLogin($uid, $token, $token_secret){
        // $this->ping();
        $uid = intval($uid);
        $token = addslashes($token);
        $token_secret = addslashes($token_secret);
        $sql = "select login_id,uid,type,oauth_token from ts_login where type='location' and uid={$uid} and oauth_token='{$token}' and oauth_token_secret='{$token_secret}' limit 1";
        // $sql = "SELECT login_id,uid,type,oauth_token FROM ts_login WHERE type='location' AND uid={$uid} LIMIT 1";
        // var_dump($sql);
        $res = $this->query($sql);
        // var_dump($res);
        // dump($db->getLastSql());
        if(!$res){
            return false;
        }else{
            //删除其他客户端登录的 client_id
            $res = $this->execute("DELETE FROM ts_message_client WHERE uid={$uid};");
            return true;
        }
    }

    //增加清理机制
    public function pingpong($client_id,$uid,$token){

        if($token){ //只手机端判断
            //如果已经异地登录，强制下线
            $res = $this->query("SELECT * FROM ts_login WHERE uid=".$uid." and oauth_token='".$token."'");
            if(!$res){
                return false;
            }
        }
        

        $client_id = intval($client_id);
        $ctime = time();

        //更新客户端在线时间,不在的话，新增(暂时方案)
        if($res = $this->query("SELECT client_id FROM ts_message_client WHERE client_id={$client_id}")){
            $this->execute("UPDATE ts_message_client SET last_active_time={$ctime} WHERE client_id={$client_id}");
        }else{
            $this->execute("DELETE FROM ts_message_client WHERE uid={$uid};");
            $this->execute("INSERT INTO ts_message_client (uid,client_id,last_active_time) VALUES ($uid,$client_id,$ctime)");
        }
        
        //删除最近30秒不活跃的客户端
        $utime = time()-30;
        $res = $this->execute("DELETE FROM ts_message_client WHERE last_active_time<{$utime}");
        
        return true;
    }

    //添加Client
    public function addClient($client_id,$uid){
        $client_id = intval($client_id);
        $uid = intval($uid);
        if(!$client_id || !$uid){
            return false;
        }
        //不存在时写入数据
        $ctime = time();
        $res = $this->execute("INSERT INTO ts_message_client (uid,client_id,last_active_time) VALUES ($uid,$client_id,$ctime)");
        if(!$res){
            return false;
        }else{
            return true;;
        }
    }

    //添加Client
    public function addClient_test($client_id,$uid){
        $client_id = intval($client_id);
        $uid = intval($uid);
        if(!$client_id || !$uid){
            return false;
        }
        // $sql = "select * from ts_message_client where client_id={$client_id} and uid={$uid} limit 1";
        // if($this->query($sql)){ //已存在
        //     return true;
        // }
        // //删除其他客户端登录的 client_id
        // $this->execute("DELETE FROM ts_message_client WHERE  uid={$uid};");
        //不存在时写入数据
        $ctime = time();
        $res = $this->execute("INSERT INTO ts_message_client (uid,client_id,last_active_time) VALUES ($uid,$client_id,$ctime)");
        if(!$res){
            return false;
        }else{
            return true;
        }
    }

    //删除Client - 关闭或断开连接时
    public function delClient($client_id){
        $client_id = intval($client_id);
        if(!$client_id){
            return false;
        }
        //不存在时写入数据
        // $res = $this->execute("DELETE FROM ts_message_client WHERE  uid = (SELECT uid FROM ts_message_client WHERE client_id={$client_id});");
        $res = $this->execute("DELETE FROM ts_message_client WHERE client_id={$client_id};");
        if(!$res){
            return false;
        }else{
            return true;;
        }
    }

    //获取用户的Client列表
    public function getClientListFromUID($uid){
        $uid = intval($uid);
        $sql = "SELECT client_id FROM ts_message_client WHERE uid={$uid}";
        $res = $this->query($sql);
        if(!$res){
            return array();
        }else{
            $client_list_array = array();
            foreach ($res as $k => $v) {
                $client_list_array[] = $v['client_id'];
            }
            $client_list_array = array_unique(array_filter($client_list_array));
            return $client_list_array;
        }
    }

    /**
     * 获取在线医生client和离线医生uid
     * @return array
     */
    public function getDoctors(){
        $sql = "SELECT uid FROM ts_user WHERE is_doctor=1";
        $res = $this->query($sql);
        foreach ($res as $k => $v) {
            $total_uids[] = $v['uid'];
        }
        $sql1 = "SELECT uid,client_id FROM ts_message_client WHERE uid in (".implode(',', $total_uids).")";
        $res1 = $this->query($sql1);
        foreach ($res1 as $k1 => $v1) {
            $online_client_array[] = $v1['client_id'];
            $online_uids[] = $v1['uid'];
        }
        $offline_uids = array_diff($total_uids,array_unique($online_uids));
        $data['online_client_array'] = $online_client_array;
        $data['offline_uids'] = $offline_uids;
        $data['total_uids'] = array_unique(array_filter($total_uids));
        return $data;
    }

    /**
     * 根据uid获取在线client和离线uid
     * @return [type] [description]
     */
    public function get_clients_and_uids($to_uid){
        $uids = array_unique(array_filter(explode(',', $to_uid)));
        $sql = "SELECT uid,client_id FROM ts_message_client WHERE uid in (".implode(',', $uids).")";
        $res = $this->query($sql);
        $online_client_array = array();
        $online_uids = array();
        foreach ($res as $k => $v) {
            $online_client_array[] = $v['client_id'];
            $online_uids[] = $v['uid'];
        }
        $offline_uids = array_diff($uids,array_unique($online_uids));
        $data['online_client_array'] = $online_client_array;
        $data['offline_uids'] = $offline_uids;
        return $data;
    }

    //获取ROOM的Client列表
    public function getClientListFromRoom($room_id){
        $room_id = intval($room_id);
        $sql = "SELECT client_id FROM ts_message_client WHERE uid IN (SELECT member_uid FROM ts_message_member WHERE list_id={$room_id})";
        $res = $this->query($sql);
        if(!$res){
            return array();
        }else{
            $client_list_array = array();
            foreach ($res as $k => $v) {
                $client_list_array[] = $v['client_id'];
            }
            $client_list_array = array_unique(array_filter($client_list_array));
            return $client_list_array;
        }
    }

    //判断是否能发群聊
    public function checkGroupUser($room_id, $uid){
        $room_id = intval($room_id);
        $uid = intval($uid);
        $sql = "SELECT count(1) as `count` FROM ts_message_member WHERE list_id={$room_id} AND member_uid={$uid}";
        $res = $this->query($sql);
        if($res[0]['count']==0){
            return false;
        }else{
            return true;
        }
    }

    //判断是否能发对话 -- 待完善
    public function checkChatUser($from_uid, $to_uid){
        $res = true;
        if(!$res){
            return false;
        }else{
            return true;
        }
    }

    //获取ROOM的onlineUID列表
    public function getOnlineUIDFromRoom($room_id, $from_uid){
        $room_id = intval($room_id);
        $sql = "SELECT member_uid FROM ts_message_member WHERE list_id={$room_id}";
        $res = $this->query($sql);

        // $log['from_uid'] = $from_uid;
        // $log['room_id'] = $room_id;
        // $log['sql'] = $sql;
        // $log['res'] = $res;
        // $log['=='] = '===========';

        if(!$res){
            return array();
        }else{
            $all_uid_array = array();
            foreach ($res as $k => $v) {
                $all_uid_array[] = $v['member_uid'];
            }
            $all_uid_array = array_unique(array_filter($all_uid_array));
        }

        $sql = "SELECT distinct uid FROM ts_message_client WHERE uid IN (SELECT member_uid FROM ts_message_member WHERE list_id={$room_id})";
        $res = $this->query($sql);
        if(!$res){
            return array();
        }else{
            $online_uid_array = array();
            foreach ($res as $k => $v) {
                $online_uid_array[] = $v['uid'];
            }
            $online_uid_array = array_unique(array_filter($online_uid_array));
        }
        $offline_uid_array = array_diff($all_uid_array, $online_uid_array);

        $return['all']  = (array) $all_uid_array;
        $return['online']   = (array) $online_uid_array;
        $return['offline']  = (array) $offline_uid_array;
        
        // $log['sql'] = $sql;
        // $log['res'] = $res;
        // $log['=='] = '===========';
        // $log['return'] = $return;

        // $log_message = "============================ \n "
        //        ." \n ".var_export($log,true)." \n ";
        // $log_file = "/home/wwwroot/workerman-chat/applications/Chat/log.txt";
        // error_log($log_message, 3, $log_file);

        return $return;
    }

    //获取ROOMID
    public function getRoomID($uid, $fid){
        if($uid>$fid){
            $min_max = $fid.'_'.$uid;
        }else{
            $min_max = $uid.'_'.$fid;
        }
        $res = $this->query("SELECT list_id,type,title,mtime from ts_message_list where type=1 and min_max='$min_max'");
        if(!$res){
            //不存在时写入数据
            $res = $this->execute("INSERT into ts_message_list (type,title,member_num,min_max) values (1,'',2,'$min_max')");
            if(!$res){
                return false;
            }else{
                $res = $this->query("SELECT list_id,type,title,mtime from ts_message_list where type=1 and min_max='$min_max'");
                if(!$res){
                    return false;
                }else{
                    $list_id = (int) $res[0]['list_id'];
                    //增加list_member
                    $ctime=time();
                    $res = $this->execute("INSERT into ts_message_member (list_id,member_uid,ctime,list_ctime) values ($list_id,$uid,$ctime,$ctime),($list_id,$fid,$ctime,$ctime)");
                    return $list_id;
                }
            }
        }else{
            return (int) $res[0]['list_id'];
        }
    }

    //获取ROOMTITLE
    public function getRoomTitle($room_id){
        $room_id = intval($room_id);
        $res = $this->query("SELECT list_id,type,title,mtime from ts_message_list where list_id=$room_id");
        if(!$res){
            return '群聊';
        }else{
            return (string) $res[0]['title'];
        }
    }

    //发布对话 - 需要list_id（room_id）
    public function sendMessage($content){
        //如果list_id不存在
        $type = addslashes($content['type']);
        $msgtype = addslashes($content['msgtype']);
        $list_id = intval($content['room_id']);
        $from_uid = intval($content['from_uid']);
        $to_uid = intval($content['to_uid']);
        if(!$type || !$from_uid ||!$to_uid){
            return false;
        }
        //获取房间LIST_ID
        if(!$list_id){
            $list_id = getRoomID($from_uid, $to_uid);
            if(!$list_id){
                return false;
            }
        }
        //拼装内容
        $content = addslashes($content['content']);
        if($msgtype=='image'){
            $attach_ids = serialize(array($content['attach_id']));        
        }else{
            $attach_ids = "";
        }
        
        //写入消息
        $res = $this->execute("INSERT INTO ts_message_content (list_id,from_uid,type,content,attach_ids) values ($list_id,$from_uid,'$msgtype','$content','$attach_ids')");
        // dump($this->getLastSql());
        $message_id = $this->getLastIncID();
        if(!$res){
            return false;
        }else{
            //更新消息通知系统
            return (int) $message_id;
        }
    }

    //发送离线消息 
    public function sendOfflineMessage($uids, $message){
        $log['uid'] = $uids;
        $log['message'] = $message;
        //更新ts_message_list表
        if( count($uids)>0 ){
            //写入离线消息系统
            $list_id = $message['room_id'];
            $content = addslashes(json_encode($message));
            foreach ($uids as $uid) {
                $sql = "INSERT INTO ts_message_offline (`uid`, `content`) VALUES ($uid,'{$content}');";
                $log['sql1'] = $sql;
                $this->execute($sql);
            }
            //更新网页版
            $to_uids = implode(',', $uids);
            $sql = "UPDATE ts_message_member SET new=new+1 WHERE list_id={$list_id} AND to_uid in ({$to_uids});";
            $log['sql2'] = $sql;
            $this->execute($sql);

            // $log_message = "============================ \n "
            //        ." \n ".var_export($log,true)." \n ";
            // $log_file = "/home/wwwroot/workerman-chat/applications/Chat/log.txt";
            // error_log($log_message, 3, $log_file);
        }
    }

    //获取离线消息
    public function getOfflineMessage($uid){
        $res = $this->query("SELECT * FROM ts_message_offline WHERE uid={$uid} ORDER BY id ASC");        
        // $log['uid'] = $uid;
        // $log['messages'] = $res;
        // $log_message = "============================ \n "
        //        ." \n ".var_export($log,true)." \n ";
        // $log_file = "/home/wwwroot/workerman-chat/applications/Chat/log.txt";
        // error_log($log_message, 3, $log_file);
        if($res){
            $this->execute("DELETE FROM ts_message_offline WHERE uid={$uid}");
            return $res;
        }else{
            return false;
        }
    }

    // public function hasOfflineMessage($uid){
    //     $res = $this->query("SELECT * FROM ts_message_member WHERE member_uid={$uid} and new>0 LIMIT 1");
    //     if($res){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }

    //创建群聊
    // public function createList($uid, $members, $title){
    //     $type = 2;
    //     $from_uid = intval($uid);
    //     $members .= ','.$uid;
    //     $members = explode(',', $members);
    //     $members = array_map('intval', $members);
    //     $members = array_unique(array_filter($members));
    //     $member_num = count($members);
    //     if(!$from_uid || !$member_num){
    //         return false;
    //     }
    //     $res = $this->execute("INSERT into ts_message_list (type,from_uid,title,member_num,min_max) values (2,$from_uid,'{$title}',{$member_num},'')");
    //     if(!$res){
    //         return false;
    //     }else{
    //         $list_id = $this->getLastIncID();
    //         //更新ts_message_member表
    //         $ctime = time();
    //         $sql = "INSERT into ts_message_member (list_id,member_uid,ctime,list_ctime) values ";
    //         foreach($members as $v){
    //             $sql .= "($list_id,$v,$ctime,$ctime),";
    //         }
    //         $sql = rtrim($sql,',');
    //         $res = $this->execute($sql);
    //         return $list_id;
    //     }
    // }

    //多人对话加人
    public function addUserToList($list_id, $uid){
        $list_id = intval($list_id);
        $uid = intval($uid);
        if(!$list_id || !$uid){
            return false;
        }
        $ctime = time();
        $sql = "INSERT INTO ts_message_member (list_id,member_uid,ctime,list_ctime) VALUES ($list_id,$uid,$ctime,$ctime)";
        // var_dump($sql);
        $res = $this->execute($sql);
        if(!$res){
            return false;
        }else{
            $sql = "UPDATE ts_message_list SET member_num=(SELECT count(member_uid) FROM ts_message_member WHERE list_id=$list_id) WHERE list_id=$list_id LIMIT 1;";
            // var_dump($sql);
            $this->execute($sql);
            return true;
        }
    }

    //多人对话减人
    public function moveUserFromList($list_id, $uid){
        $list_id = intval($list_id);
        $uid = intval($uid);
        if(!$list_id || !$uid){
            return false;
        }
        $sql = "DELETE FROM ts_message_member WHERE list_id=$list_id AND member_uid=$uid;";
        // var_dump($sql);
        $res = $this->execute($sql);
        if(!$res){
            return false;
        }else{
            $this->execute("UPDATE ts_message_list SET member_num=(SELECT count(member_uid) FROM ts_message_member WHERE list_id=$list_id) WHERE list_id=$list_id LIMIT 1;");
            // var_dump($sql);
            return true;
        }
    }

    public function get_users_avatar($list_id){
        $res = $this->query("SELECT member_uid from ts_message_member where list_id=$list_id");
        foreach ($res as $k => $v) {
            if($k<=8){
                $avatar[] = $this->getUserAvatar($v['member_uid']);
            }
        }
        return $avatar;
    }

    //修改群名称
    public function changeListTitle($list_id, $title){
        $list_id = intval($list_id);
        $title = addslashes($title);
        if(!$list_id || !$title){
            return false;
        }
        $sql = "UPDATE ts_message_list SET title='{$title}' WHERE list_id=$list_id LIMIT 1;";
        $res = $this->execute($sql);
        if(!$res){
            return false;
        }else{
            return true;
        }
    }

    //获取用户的头像
    public function getUserAvatar($uid){
        $is_cloud   = false;
        $site_path  = dirname(__FILE__).'/../../../'; //'/home/wwwroot/demo.thinksns.com/ts4';
        $site_url   = $this->config['site_url'];
        //本地头像
        if(!$is_cloud){

           //默认头像
            $avatar_url = $site_url.'/addons/theme/stv1/_static/image/noavatar/middle.jpg';

            //头像规则
            $md5 = md5($uid);
            $uid_avatar_path = '/'.substr($md5, 0, 2).'/'.substr($md5, 2, 2).'/'.substr($md5, 4, 2);
            $avatar_file = '/avatar'.$uid_avatar_path.'/original_100_100.jpg';

            if(file_exists($site_path.'/data/upload/'.$avatar_file)){
                $avatar_url = $site_url.'/data/upload/'.$avatar_file;
            }

        //云端头像
        }else{

            //默认头像
            $avatar_url = $site_url.'/addons/theme/stv1/_static/image/noavatar/middle.jpg';

            //头像规则
            $md5 = md5($uid);
            $uid_avatar_path = '/'.substr($md5, 0, 2).'/'.substr($md5, 2, 2).'/'.substr($md5, 4, 2);
            $res = file_get_contents($site_url.'/avatar'.$uid_avatar_path.'/original.jpg!exif');
            if($res){
                $avatar_url = $site_url.'/avatar'.$uid_avatar_path.'/original.jpg!big.avatar.jpg';
            }

        }

        return $avatar_url;
    }
}

class SociaxDB {
    static private $_instance	= null;
    // 是否显示调试信息 如果启用会在日志文件记录sql语句
    public $debug				= false;
    // 是否使用永久连接
    protected $pconnect         = true;
    // 当前SQL指令
    protected $queryStr			= '';
    // 最后插入ID
    protected $lastInsID		= null;
    // 返回或者影响记录数
    protected $numRows			= 0;
    // 返回字段数
    protected $numCols			= 0;
    // 事务指令数
    protected $transTimes		= 0;
    // 错误信息
    protected $error			= '';
    // 当前连接ID
    protected $linkID			=   null;
    // 当前查询ID
    protected $queryID			= null;
    // 是否已经连接数据库
    protected $connected		= false;
    // 数据库连接参数配置
    protected $config			= '';
    // SQL 执行时间记录
    protected $beginTime;
    /**
     * 架构函数
     * @access public
     * @param array $config 数据库配置数组
     */
    public function __construct($config=''){
        if ( !extension_loaded('mysql') ) {
            echo('not support mysql');
        }
        $this->config   =   $this->parseConfig($config);
    }

    /**
     * 连接数据库方法
     * @access public
     * @throws ThinkExecption
     */
    public function connect() {
        if(!$this->connected) {
            $config =   $this->config;
            // 处理不带端口号的socket连接情况
            $host = $config['hostname'].($config['hostport']?":{$config['hostport']}":'');
            if($this->pconnect) {
                $this->linkID = mysql_pconnect( $host, $config['username'], $config['password']);
            }else{
                $this->linkID = mysql_connect( $host, $config['username'], $config['password'],true);
            }
            if ( !$this->linkID || (!empty($config['database']) && !mysql_select_db($config['database'], $this->linkID)) ) {
                echo(mysql_error());
            }
            $dbVersion = mysql_get_server_info($this->linkID);
            if ($dbVersion >= "4.1") {
                //使用UTF8存取数据库 需要mysql 4.1.0以上支持
                mysql_query("SET NAMES 'UTF8'", $this->linkID);
            }
            //设置 sql_model
            if($dbVersion >'5.0.1'){
                mysql_query("SET sql_mode=''",$this->linkID);
            }
            // 标记连接成功
            $this->connected    =   true;
            // 注销数据库连接配置信息
            unset($this->config);
        }
    }

    /**
     * ping服务器重连
     * @access public
     */
    public function ping() {
        if(!@mysql_ping($this->linkID)){    
            $this->close(); //注意：一定要先执行数据库关闭，这是关键   
            $this->connect();    
        }
        return $this->linkID;
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free() {
        mysql_free_result($this->queryID);
        $this->queryID = 0;
    }

    /**
     * 执行查询 主要针对 SELECT, SHOW 等指令
     * 返回数据集
     * @access public
     * @param string $str  sql指令
     * @return mixed
     * @throws ThinkExecption
     */
    public function query($str='') {
        $this->connect();
        if ( !$this->linkID ) return false;
        if ( $str != '' ) $this->queryStr = $str;
        //释放前次的查询结果
        if ( $this->queryID ) {    $this->free();    }
        $this->Q(1);
        $this->queryID = mysql_query($this->queryStr, $this->linkID);
        $this->debug();
        if ( !$this->queryID ) {
            if ( $this->debug )
                echo($this->error());
            else
                return false;
        } else {
            $this->numRows = mysql_num_rows($this->queryID);
            return $this->getAll();
        }
    }

    /**
     * 执行语句 针对 INSERT, UPDATE 以及DELETE
     * @access public
     * @param string $str  sql指令
     * @return integer
     * @throws ThinkExecption
     */
    public function execute($str='') {
        $this->connect();
        if ( !$this->linkID ) return false;
        if ( $str != '' ) $this->queryStr = $str;
        //释放前次的查询结果
        if ( $this->queryID ) {    $this->free();    }
        $this->W(1);
        $result =   mysql_query($this->queryStr, $this->linkID) ;
        $this->debug();
        if ( false === $result) {
            if ( $this->debug )
                echo($this->error());
            else
                return false;
        } else {
            $this->numRows = mysql_affected_rows($this->linkID);
            $this->lastInsID = mysql_insert_id($this->linkID);
            return $this->numRows;
        }
    }

    public function getLastIncID(){
        return $this->lastInsID;
    }

    /**
     * 获得所有的查询数据
     * @access public
     * @return array
     * @throws ThinkExecption
     */
    public function getAll() {
        if ( !$this->queryID ) {
            echo($this->error());
            return false;
        }
        //返回数据集
        $result = array();
        if($this->numRows >0) {
            while($row = mysql_fetch_assoc($this->queryID)){
                $result[]   =   $row;
            }
            mysql_data_seek($this->queryID,0);
        }
        return $result;
    }

    /**
     * 关闭数据库
     * @access public
     * @throws ThinkExecption
     */
    public function close() {
        if (!empty($this->queryID))
            mysql_free_result($this->queryID);
        if ($this->linkID && !mysql_close($this->linkID)){
            echo($this->error());
        }
        $this->linkID = 0;
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
        $this->error = mysql_error($this->linkID);
        if($this->queryStr!=''){
            $this->error .= "\n [ SQL语句 ] : ".$this->queryStr;
        }
        return $this->error;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escape_string($str) {
        $res = @mysql_escape_string($str);
        $res === false && $res = $str;
        return $res;
    }

   /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 关闭连接
        $this->close();
    }

    /**
     * 取得数据库类实例
     * @static
     * @access public
     * @return mixed 返回数据库驱动类
     */
    public static function getInstance($db_config='')
    {
		if ( self::$_instance==null ){
			self::$_instance = new SociaxDB($db_config);
		}
		return self::$_instance;
    }

    /**
     * 分析数据库配置信息，支持数组和DSN
     * @access private
     * @param mixed $db_config 数据库配置信息
     * @return string
     */
    private function parseConfig($_db_config='') {
		// 如果配置为空，读取配置文件设置
		$db_config = array (
			'dbms'		=>   $_db_config['DB_TYPE'],
			'username'	=>   $_db_config['DB_USER'],
			'password'	=>   $_db_config['DB_PWD'],
			'hostname'	=>   $_db_config['DB_HOST'],
			'hostport'	=>   $_db_config['DB_PORT'],
			'database'	=>   $_db_config['DB_NAME'],
			// 'dsn'	=>   $_db_config['DB_DSN'],
			// 'params'	=>   $_db_config['DB_PARAMS'],
            'site_url'  =>   $_db_config['SITE_URL'],
            'site_path' =>   $_db_config['SITE_PATH'],
		);
        return $db_config;
    }

    /**
     * 数据库调试 记录当前SQL
     * @access protected
     */
    protected function debug() {
        // 记录操作结束时间
        if ( $this->debug )    {
            $runtime    =   number_format(microtime(TRUE) - $this->beginTime, 6);
            Log::record(" RunTime:".$runtime."s SQL = ".$this->queryStr,Log::SQL);
        }
    }

    /**
     * 查询次数更新或者查询
     * @access public
     * @param mixed $times
     * @return void
     */
    public function Q($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
            // 记录开始执行时间
            $this->beginTime = microtime(TRUE);
        }
    }

    /**
     * 写入次数更新或者查询
     * @access public
     * @param mixed $times
     * @return void
     */
    public function W($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
            // 记录开始执行时间
            $this->beginTime = microtime(TRUE);
        }
    }

    /**
     * 获取最近一次查询的sql语句
     * @access public
     * @return string
     */
    public function getLastSql() {
        return $this->queryStr;
    }
}