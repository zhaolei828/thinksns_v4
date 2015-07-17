<?php
/**
 * 短信模型
 * @author liuxiaoqing@zhishisoft.com 
 * @version TS3.0
 * Type   level
 * 动态密码=0    0
 * gsm数据=1     15
 * 淘宝通知=2    5
 * 找回密码=3    0
 * 手机注册码=4  0
 * 其他活动=5    5
 * 手机认证码=6  0
 */
class SmsModel extends Model {
	var $trueTableName = 'ts_sms';
	var $error = '';
	
	//获取错误信息
	public function getError(){
		return $this->error;
	}

	/**
	 * 发送短信
	 *
	 * @param int $tel 需要发送到的电话号码
	 * @param string $message 需要发送的消息
	 * @return bool
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function newSendSMS($tel, $message)
	{
		$config = model('Xdata')->get('admin_Config:sms');
		$url    = $config['sms_server'];
		$param  = $config['sms_param'];
		$code   = $config['success_code'];
		$type   = $config['send_type'];
		$service= $config['service'];

		$param  = str_replace('{tel}', $tel, $param);
		$param  = str_replace('{message}', rawurlencode($message), $param);

		if ($type == 'get') {
			$url .= strpos($url, '?') ? '&' : '?';
			$url .= $param;
		}

		$result = $this->httppost($param, $url);

		switch (strtolower($service)) {
			// # 互亿互联
			case 'ihuyi':
				$return = $this->_ihuyi($result);
				break;
			
			case 'false':
			default:
				$return = false;
				break;
		}

		$return or $return = $this->_code($result, $code);

		return $return;
	}

	/**
	 * 互亿互联 短信平台返回成功标识判断
	 *
	 * @param string $result 发信服务器返回的数据
	 * @return bool
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	private function _ihuyi($result)
	{
		$xml = simplexml_load_string($result);
		if (intval($xml->code) == 2) {
			return true;
		}
		return false;
	}

	/**
	 * 根据成功返回标识判断是否发信成功
	 *
	 * @param string $result 发信服务器返回的数据
	 * @param string $code 用来判断的code代码标识
	 * @return bool
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	private function _code($result, $code)
	{
		return (bool)strpos($result, $code);
	}

	public function send_sms($tel,$content){

		// # 兼容一些未知位置
		return $this->newSendSMS($tel, $content);

        //获取发送短信账号和密码
        // mb_detect_encoding($content);
        $sms_config = model('Xdata')->get('admin_Config:sms');
        $target = $sms_config['sms_server'];
        // $name = $sms_config['sms_account'];
        $pwd = $sms_config['sms_password'];

        //替换成自己的测试账号,参数顺序和wenservice对应
        $post_data = "account=".$name."&password=".$pwd."&mobile=".$tel."&content=".rawurlencode($content);
        //$binarydata = pack("A", $post_data);
        $gets = $this->httppost($post_data, $target);
		/*        $gets = '<?xml version="1.0" encoding="utf-8"?>
		<SubmitResult xmlns="http://121.199.16.178/">
		<code>2</code>
		<msg>提交成功</msg>
		<smsid>12437361</smsid>
		</SubmitResult>';
		*/
       	$res = simplexml_load_string($gets);
       	// dump($res);
        $return['code'] = intval($res->code);
        $return['msg'] = strval($res->msg);
        $return['smsid'] = intval($res->smsid);
        return $return;
    }

    private function httppost($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }

	//发送短信
	public function send($data){
		if(!$data['Mobile']) return false;
		$res = $this->add($data);
		if($res){
			// //测试用begin
			// $map['ID'] = $res;
			// $this->where($map)->setField('Status',1);
			// return 1;
			// //测试用end
			$tel = $data['Mobile'];
			$content = $data['smsContent'];
			// $smsres = $this->send_sms($tel,$content);
			// if($smsres['code']==2){
			if ($this->newSendSMS($tel, $content)) {
				$map['ID'] = $res;
				$this->where($map)->setField('Status',1);
				$this->error = '验证码发送成功';
				return 1;
			}else{
				$this->error = '验证码发送失败';
				return -1;
			}
		}else{
			$this->error = '删除写入失败';
			return 0;
		}
	}

	//发送注册验证码
	public function sendRegisterCode($tel, $from='web'){
		// return false;
		if (model('User')->where("phone='".$tel."'")->find() ){
			$this->error = '该手机号码已经注册过了，请更换号码再试！';
			return false;
		}

		//短信锁定
		if(!$this->_smsLock($tel))
			return false;

		//发送注册短信
		$tel = t($tel);
		if(!$tel) return false;
		$data['Mobile'] = $tel;
		$data['Rand'] = rand(1111,9999);
		$data['Type'] = 4;
		$data['Status'] = 0;
		$data['level'] = 0;
		$data['AddDate'] = date('Y-m-d H:i:s');
		$data['smsContent'] = '亲爱的用户，您的注册验证码为：'.$data['Rand'];
		$data['from'] = $from;

		$res = $this->send($data);
		return $res;
	}

	//验证注册验证码
	public function checkRegisterCode($tel,$code){
		$map['Type'] = 4;
		$map['Mobile'] = $tel;
		if( !$code || !$tel){
			return false;
		}
		$dbcode = $this->where($map)->order('ID desc')->getField('Rand');
		return $dbcode == $code ? true : false;
	}

	//发送找回密码短信
	public function sendPasswordCode($tel){
		//短信锁定
		if(!$this->_smsLock($tel))
			return false;

		if ( !model('User')->where("login='".$tel."'")->find() ){
			$this->error = '该手机号关联不到相关用户信息';
			return false;
		}
		//发送注册短信
		$tel = t($tel);
		if(!$tel) return false;
		$data['Mobile'] = $tel;
		$data['Rand'] = rand(1111,9999);
		$data['Type'] = 3;
		$data['Status'] = 0;
		$data['level'] = 0;
		$data['AddDate'] = date('Y-m-d H:i:s');
		$data['smsContent'] = '亲爱的用户，您的找回密码验证码为：'.$data['Rand'];
		$res = $this->send($data);
		return $res;
	}
	
	//重置密码
	public function sendPassword($tel,$pwd){
		$tel = t($tel);
		if(!$tel) return false;
		$map['login'] = $tel;
		$setuser['login_salt'] = rand(10000,99999);
		$setuser['password']   = md5(md5($pwd).$setuser['login_salt']);
		$res = model( 'User' )->where( $map )->save($setuser);
		return $res;
	}

	//发送重置的密码短信
	public function sendPasswordApi($tel){
		//发送注册短信
		$tel = t($tel);
		if(!$tel) return false;
		$data['Mobile'] = $tel;
		$data['Rand'] = rand(11111,99999);
		$data['Type'] = 3;
		$data['Status'] = 0;
		$data['level'] = 0;
		$data['AddDate'] = date('Y-m-d H:i:s');
		tsload(ADDON_PATH.'brary/String.class.php');
        $rndstr = String::rand_string( 5 , 3 );
        $pwd = $rndstr.$data['Rand'];
		$data['smsContent'] = '亲爱的用户，您的当前密码为：'.$pwd;
		$res = $this->add($data);
		$map['login'] = $tel;
		$setuser['login_salt'] = rand(10000,99999);
		$setuser['password']   = md5(md5($pwd).$setuser['login_salt']);
		$res = model( 'User' )->where( $map )->save($setuser);
		return $res;
	}

	//验证找回密码短信
	public function checkPasswordCode($tel,$code){
		$map['Type'] = 3;
		$map['Mobile'] = $tel;
		if( !$code || !$tel){
			$this->error = '请输入验证码！';
			return false;
		}
		$dbcode = $this->where($map)->order('ID desc')->getField('Rand');
		if( $dbcode == $code ){
			return true;
		} else {
			$this->error = '验证码错误，请检查您的验证码！';
			return false;
		}
	}

	//发送绑定手机验证码
	public function sendLoginCode($tel){
		//短信锁定
		if(!$this->_smsLock($tel))
			return false;

		//发送注册短信
		$tel = t($tel);
		if(!$tel) return false;
		$data['Mobile'] = $tel;
		$data['Rand'] = rand(1111,9999);
		$data['Type'] = 6;
		$data['Status'] = 0;
		$data['level'] = 0;
		$data['AddDate'] = date('Y-m-d H:i:s');
		$data['smsContent'] = '亲爱的用户，您的手机绑定验证码为：'.$data['Rand'];
		$res = $this->send($data);
		return $res;
	}

	//验证注册验证码
	public function checkLoginCode($tel,$code){
		$map['Type'] = 6;
		$map['Mobile'] = $tel;
		if( !$code || !$tel){
			$this->error = '请输入验证码！';
			return false;
		}
		$dbcode = $this->where($map)->order('ID desc')->getField('Rand');
		if( $dbcode == $code ){
			return true;
		} else {
			$this->error = '验证码错误，请检查您的验证码！';
			return false;
		}
	}

	//短信锁
	private function _smsLock($tel){

		//锁定时间
		$locktime = 60;
		//创建目录
		if(!is_dir(SITE_PATH.'/data/smslock/'))
			mkdir(SITE_PATH.'/data/smslock/',0777,true);
		//锁文件
		$lockfile = SITE_PATH.'/data/smslock/'.$tel.'.txt';
		//文件验证时间间隔
		if(file_exists($lockfile)){
			if( ( time() - filemtime ($lockfile) ) < $locktime ){
				$this->error = '请不要频繁发送，稍后再试。';
				return false;
			}else{
				$k = fopen($lockfile,"a+");
				fwrite($k,','.time());
				fclose($k);
			}
		}else{
			touch($lockfile);
		}
		return true;
	}
}