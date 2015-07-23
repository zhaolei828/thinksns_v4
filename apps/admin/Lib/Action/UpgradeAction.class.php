<?php
tsload(APPS_PATH . '/admin/Lib/Action/AdministratorAction.class.php');

/**
 * 升级程序
 *
 * @package ThinkSNS.admin.upgrade
 * @author Medz Seven <lovevipdsw@vip.qq.com>
 **/
class UpgradeAction extends AdministratorAction
{

	/**
	 * 执行前
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function _initialize()
	{
		extension_loaded('zlib')      or $this->error('服务器未安装php的zlib拓展，无法使用在线升级功能');
		function_exists('gzcompress') or $this->error('服务器不支持gzcompress函数，无法使用在线升级功能');
		parent::_initialize();
	}

	/**
	 * 后台检测
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function ajxjCheck()
	{
		ob_end_clean();
		ob_start();
		header('Content-Type: application/json; charset=utf-8');

		echo file_get_contents(C('UPURL') . '?v=' . C('VERSION'));

		ob_end_flush();
		exit;
	}


	/**
	 * 检查是否有更新
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function check()
	{

		$url  = C('UPURL') . '?v=' . C('VERSION');
		$data = file_get_contents($url);

		$data or $this->showError('您的服务器无法从升级服务器获取升级数据！');

		$data = json_decode($data, false);

		function_exists('json_decode') or $this->showError('你的服务器不支持json_decode函数');

		switch ($data->status) {
			case 1:
				$this->showSuccess('', '暂时没有更新');
				break;

			case 2:
				$this->showUpgrade($data->message, $data->url);
				break;

			case 0:
			default:
				$this->showError($data->message, '无法获得更新');
				break;
		}
		unset($url, $data);
	}

	/**
	 * 显示消息
	 *
	 * @param string $message 消息
	 * @param string $type [success|error] 消息类型
	 * @param string $url 跳转的url
	 * @param int    $s   等待的时间
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function showMessage($message, $type, $url = false, $s = 3)
	{
		$this->assign('message', $message);
		$this->assign('type'   , $type);
		$this->assign('url'    , $url);
		$this->assign('s'      , intval($s));
		$this->display('message');
		exit;
	}

	/**
	 * 显示正确消息
	 *
	 * @param string $message 消息
	 * @param string $defaultMessage 默认消息
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	private function showSuccess($message = '', $defaultMessage = '正确')
	{
		$message or $message = $defaultMessage;
		$this->showMessage($message, 'success', false);
	}

	/**
	 * 显示错误消息
	 *
	 * @param string $message 消息
	 * @param string $defaultMessage 默认消息
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	private function showError($message = '', $defaultMessage = '错误')
	{
		$message or $message = $defaultMessage;
		$this->showMessage($message, 'error', false);
	}

	/**
	 * 显示升级信息
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	private function showUpgrade($log, $downUrl)
	{
		$this->savePostUrl = U('admin/Upgrade/step1', array('upurl' => urlencode($downUrl)));
		$this->pageTitle['showUpgrade'] = '更新日志';
		$this->submitAlias = '立即升级';
		$this->pageKeyList = array('log', 'tips');
		$this->opt['tips'] = '<pre>
1.升级前请做好网站程序和数据备份。
2.如果您网站经过修改，请勿使用在线升级。
3.升级需要ThinkSNS程序文件和目录拥有可写，可读权限，升级前先确定
4.升级前一定要做好数据备份。
5.因各种因素，您无法使用在线升级，那么，请 <a href="' . $downUrl . '">点击这里</a>手动下载升级包进行手动升级。
<pre/>';
		$this->onsubmit = 'confirm(\'确定要升级吗？\')';
		$this->displayConfig(array(
			'log' => $log,
			'tips'=> $this->opt['tips']
		));
	}

	/**
	 * 升级程序第一步 下载增量包
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function step1()
	{
		$downUrl = $_GET['upurl'];
		$downUrl = urldecode($downUrl);
		$path    = DATA_PATH . '/' . 'upgrade/' . basename($downUrl);

		// # 备份老配置文件
		$oldConf = file_get_contents(CONF_PATH . '/thinksns.conf.php');
		file_put_contents(DATA_PATH . '/old.thinksns.conf.php', $oldConf);

		// # 下载增量包
		is_dir(dirname($path)) or mkdir(dirname($path), '0777', true);
		file_put_contents($path, file_get_contents($downUrl));
		file_exists($path) or $this->showError('下载升级包失败，请检查' . dirname($path) . '目录是否可写，如果可写，请刷新重试！');

		$sqlPath = dirname($path) . '/' . 'upgrade.sql';
		$delFile = dirname($path) . '/' . 'deleteFiles.php';

		file_exists($delFile) and file_put_contents($delFile, '<?php return array(); ?>');
		file_exists($sqlPath) and file_put_contents($sqlPath, '-- 暂无升级 SQL --');
		
		// # 解压增量包
		import(C('UTILITY') . 'MedzZip.php');
		$zip = new MedzZip;
		$zip->init() or $this->showError('初始化解压程序失败！');

		$list = $zip->extract($path);

		foreach ($list as $info) {
			$filename = SITE_PATH . '/' . $info['filename'];
			if (!file_exists($filename)) {
				is_dir(dirname($filename)) or mkdir(dirname($filename), '0777', true);
				is_dir(dirname($filename)) or $this->showError('目录' . dirname($filename) . '创建失败，请赋予0777权限');
				file_put_contents($filename, $info['data']);
				file_exists($filename) or $this->showError($filename . '文件写入失败，请赋予' . dirname($filename) . '目录0777权限');
			} elseif (!is_writable($filename)) {
				$this->showError($filename . '文件写入失败，请确认该文件为可写状态');
			}
		}

		$this->showMessage('权限检查成功，程序自动进入下一步（请勿操作页面）', 'success', U('admin/Upgrade/step2', array(
			'filename' => urlencode(basename($path))
		)), 3);
	}

	/**
	 * 升级程序第二步 - 执行文件替换
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function step2()
	{
		$filename = $_GET['filename'];
		$filename = urldecode($filename);
		$filename = DATA_PATH . '/' . 'upgrade/' . $filename;

		import(C('UTILITY') . 'MedzZip.php');
		$zip = new MedzZip;
		$zip->init();

		foreach ($zip->extract($filename) as $info) {
			$path = $info['filename'];
			$data = $info['data'];

			unset($info);

			$path = SITE_PATH . '/' . $path;
			$dir  = dirname($path);

			is_dir($dir) or mkdir($dir, '0777', true);
			file_put_contents($path, $data);
			unset($data);
		}

		$this->showMessage('文件升级完成，程序自动进入下一步（请勿操作页面）', 'success', U('admin/Upgrade/step3'));
	}

	/**
	 * 升级第三步 - 删除升级标记需要删除的文件 和执行sql文件
	 *
	 * @return void
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	public function step3()
	{
		$sqlFilePath = DATA_PATH . '/upgrade/upgrade.sql';
		$delFile     = dirname($sqlFilePath) . '/deleteFiles.php';

		// # 删除废弃文件
		if (file_exists($delFile)) {
			$delFile = include $delFile;
			foreach ($delFile as $filename) {
				$filename = SITE_PATH . '/' . $filename;
				unlink($filename);
			}
		}

		// # 执行sql
		if (file_exists($sqlFilePath)) {
			$db = D('');
			$result = $db->executeSqlFile($sqlFilePath);
			if (isset($result['error_code'])) {
				// # 回滚配置文件
				$oldConf = file_get_contents(DATA_PATH . '/old.thinksns.conf.php');
				file_put_contents(CONF_PATH . '/thinksns.conf.php', $oldConf);

				$this->showMessage($result['error_code'] . ',请重新执行升级', 'error', U('admin/upgrade/check'));
			}
		}
		A('Tool', 'Admin')->cleancache();
		ob_end_clean();
		$this->showMessage('升级成功', 'success', U('admin/upgrade/check'));
	}

} // END class UpgradeAction extends AdministratorAction