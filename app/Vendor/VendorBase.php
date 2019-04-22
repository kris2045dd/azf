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

	// 訂單資料
	protected $order;

	// 支付資料
	protected $username;
	protected $amount;
	protected $payment_type;
	protected $datetime;

	public function __construct(\App\Model\MVendor $m_vendor)
	{
		$this->vendor_id = $m_vendor->vendor_id;
		$this->name = $m_vendor->name;
		$this->class_name = $m_vendor->class_name;

		foreach ($m_vendor->m_vendor_config as $config) {
			$this->config[$config->k] = $config->v;
		}
	}

	/**
		取得實例
	*/
	public static function getInstance(\App\Model\MVendor $m_vendor, $shared = true)
	{
		if (! $shared) {
			return new static($m_vendor);
		}

		$class = get_called_class();
		if (! isset(self::$instances[$class])) {
			self::$instances[$class] = new static($m_vendor);
		}

		return self::$instances[$class];
	}

	/**
		支付
	*/
	public function pay()
	{
		throw new \Exception(get_called_class() . ' 尚未实作支付功能.');
	}

	/**
		支付寶
	*/
	public function alipay()
	{
		return $this->pay();
	}

	/**
		支付寶 WAP
	*/
	public function alipaywap()
	{
		return $this->pay();
	}

	/**
		微信支付
	*/
	public function wechat()
	{
		return $this->pay();
	}

	/**
		微信 WAP
	*/
	public function wap()
	{
		return $this->pay();
	}

	/**
		QQ
	*/
	public function qq()
	{
		return $this->pay();
	}

	/**
		QQ WAP
	*/
	public function qqwap()
	{
		return $this->pay();
	}

	/**
		京東支付
	*/
	public function jd()
	{
		return $this->pay();
	}

	/**
		京東支付 WAP
	*/
	public function jdwap()
	{
		return $this->pay();
	}

	/**
		銀聯快捷
	*/
	public function unionfast()
	{
		return $this->pay();
	}

	/**
		銀聯快捷 WAP
	*/
	public function unionfastwap()
	{
		return $this->pay();
	}

	/**
		網銀
	*/
	public function bank()
	{
		return $this->pay();
	}

	/**
		支付成功頁面 (導頁)
	*/
	public function success()
	{
		return $this->name . ' 支付成功頁面.';
	}

	/**
		回調
	*/
	public function callback(Request $request)
	{
		throw new \Exception(get_called_class() . ' 尚未实作回调功能.');
	}

	/**
		取得 支付成功 網址
	*/
	public function getSuccessUrl()
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}";
		$url .= '/success/' . $this->class_name;
		return $url;
	}

	/**
		取得 回調 網址
	*/
	public function getCallbackUrl()
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}";
		$url .= '/callback/' . $this->class_name . '/' . $this->payment_type . '/' . $this->username . '/' . $this->datetime;
		return $url;
	}

	/**
		取得 訂單號
	*/
	public function getOrderNo($limit = 0, $separator = '_')
	{
		$username = $this->username;
		if ($limit && (mb_strlen($username) > $limit - (14 + mb_strlen($separator)))) {
			$max_username_len = $limit - (14 + mb_strlen($separator));
			if ($max_username_len <= 1) {
				throw new \Exception("订单号限制长度太小 ({$limit}).");
			}
			$username = substr_replace($username, 'x', $max_username_len - 1);
		}
		$order_no = $username . $separator . $this->datetime;
		return $order_no;
	}

	/**
		設置 訂單
	*/
	public function setOrder(\App\Model\DOrder $order)
	{
		$this->order = $order;
		return $this;
	}

	/**
		取得 訂單
	*/
	public function getOrder()
	{
		if (empty($this->order)) {
			throw new \Exception('尚未设置订单.');
		}
		return $this->order;
	}

	/**
		儲存 訂單
	*/
	protected function saveOrder(string $order_no_outer, float $amount)
	{
		$order_no = $this->getOrderNo();
		if ($order_no === '') {
			throw new \Exception('订单号为空.');
		}
		$d_order = \App\Model\DOrder::where('order_no', '=', $order_no)->first();
		// 新增訂單
		if (! $d_order) {
			$d_order = new \App\Model\DOrder();
			$d_order->vendor_id = $this->vendor_id;
			$d_order->payment_type = $this->payment_type;
			$d_order->order_no = $order_no;
			$d_order->order_no_outer = $order_no_outer;
			$d_order->username = $this->username;
			$d_order->amount = $amount;
			$d_order->paid_status = \App\Model\DOrder::PAID_STATUS_SUCCESS;
			$d_order->save();
		}
		$this->setOrder($d_order);
	}

	/**
		設置 會員帳號
	*/
	public function setUsername(string $username)
	{
		if (! preg_match('/^[a-zA-Z0-9]{1,}$/', $username)) {
			throw new \Exception('会员帐号只能是数字或大小写字母组成.');
		}
		$this->username = $username;
		return $this;
	}

	/**
		設置 金額
	*/
	public function setAmount(float $amount)
	{
		$this->amount = $amount;
		return $this;
	}

	/**
		設置 支付方式
	*/
	public function setPaymentType(string $payment_type)
	{
		if (! in_array($payment_type, \App\Model\MVendorPayment::PAYMENT_TYPES)) {
			throw new \Exception("不合法的支付方式 ({$payment_type})");
		}
		$this->payment_type = $payment_type;
		return $this;
	}

	/**
		設置 日期時間 (產生訂單號需要)
	*/
	public function setDatetime(string $datetime)
	{
		if (strlen($datetime) != 14) {
			throw new \Exception('日期时间只能是 yyyymmddhhiiss 格式.');
		}
		$this->datetime = $datetime;
		return $this;
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
			if (! isset($this->config[$key])) {
				$lack[] = $key;
			}
		}
		if ($lack) {
			throw new \Exception('缺少参数 [' . implode(', ', $lack) . ']');
		}
	}

}
