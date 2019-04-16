<?php

namespace App\Vendor;

use Illuminate\Http\Request;

class VendorBase
{

	private static $instances = [];

	// 基本資料
	protected $vendor_id;
	protected $name;
	protected $class_name;

	protected $config = [];

	// 代付資料
	protected $daifu_order;
	protected $bank_account;

	public function __construct()
	{
		$class = get_called_class();
		$path = explode('\\', $class);
		$short_class_name = array_pop($path);

		$m_vendor = \App\Model\MVendor::where('class_name', '=', $short_class_name)->first();
		if (! $m_vendor) {
			throw new \Exception("MVendor 查无资料. ({$short_class_name})");
		}

		foreach ($m_vendor->m_vendor_config as $config) {
			$this->config[$config->k] = $config->v;
		}

		$this->vendor_id = $m_vendor->vendor_id;
		$this->name = $m_vendor->name;
		$this->class_name = $short_class_name;
	}

	/**
		取得實例
	*/
	public static function getInstance($shared = true)
	{
		if (! $shared) {
			return new static();
		}

		$class = get_called_class();
		if (! isset(self::$instances[$class])) {
			self::$instances[$class] = new static();
		}

		return self::$instances[$class];
	}

	/**
		代付
	*/
	public function daifu() {
		throw new \Exception(get_called_class() . ' 尚未实作代付功能.');
	}

	/**
		代付 回調
	*/
	public function daifuCallback(Request $request) {
		throw new \Exception(get_called_class() . ' 尚未实作代付回调功能.');
	}

	/**
		設置 代付訂單
	*/
	public function setDaifuOrder(\App\Model\DDfOrder $df_order)
	{
		$this->daifu_order = $df_order;
		return $this;
	}

	/**
		取得 代付訂單
	*/
	public function getDaifuOrder()
	{
		if (empty($this->daifu_order)) {
			throw new \Exception('尚未设置代付订单.');
		}
		return $this->daifu_order;
	}

	/**
		設置 代付銀行帳號
	*/
	public function setBankAccount(\App\Model\MBankAccount $bank_account)
	{
		$this->bank_account = $bank_account;
		return $this;
	}

	/**
		取得 代付銀行帳號
	*/
	public function getBankAccount()
	{
		if (empty($this->bank_account)) {
			throw new \Exception('尚未设置代付银行帐号.');
		}
		return $this->bank_account;
	}

	/**
		取得 代付回調網址
	*/
	public function getDaifuCallbackUrl()
	{
		$daifu_order = $this->getDaifuOrder();
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}";
		$url .= '/callback/daifu/' . $this->class_name . '/' . $daifu_order->df_order_id;
		return $url;
	}

	/**
		付款
	*/
	public function pay()
	{
		throw new \Exception(get_called_class() . ' 尚未实作付款功能.');
	}

	/**
		付款成功 (導頁)
	*/
	public function success()
	{
	}

	/**
		付款回調
	*/
	public function callback(Request $request)
	{
		throw new \Exception(get_called_class() . ' 尚未实作付款回调功能.');
	}

	/**
		查詢餘額

		@return string
	*/
	public function queryBalance()
	{
		throw new \Exception(get_called_class() . ' 尚未实作查询余额功能.');
	}

	/**
		寫 Log
	*/
	protected function writeLog($data, $file_name)
	{
		$file = storage_path('logs/vendor/' . $this->class_name . '/' . $file_name);
		\App\Helpers\Logger::write($data, $file);
	}

	/**
		檢查 config
	*/
	protected function checkConfig(array $keys)
	{
		$lack = [];
		foreach ($keys as $key) {
			if (!isset($this->config[$key]) || $this->config[$key]==='') {
				$lack[] = $key;
			}
		}
		if ($lack) {
			throw new \Exception('缺少参数 [' . implode(', ', $lack) . ']');
		}
	}

}
