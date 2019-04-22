<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PayController extends Controller
{

	/**
		支付
	*/
	public function index(Request $request, $payment_type, $username, $amount)
	{
		try {

			/*
			// 取得 m_settings
			$m_settings = \App\Model\MSettings::findOrFail(1);

			// 限 BBIN 會員充值 ?
			if ($m_settings->bbin_member_only) {
			}

			// 從 BBIN 查詢層級 ?
			if ($m_settings->bbin_query_level) {
			}
			*/

			// 取得層級
			$d_member = \App\Model\DMember::where('username', $username)->first();
			$level_id = $d_member ? $d_member->level_id : 1;

			$m_level = \App\Model\MLevel::find($level_id);
			if (! $m_level) {
				return "查无层级资料. (level_id: {$level_id})";
			}

			// 取得相對應金流商
			$vendor_id = $m_level->$payment_type;
			if (empty($vendor_id)) {
				return "未设定金流商. ({$m_level->name}:{$payment_type})";
			}

			$m_vendor = \App\Model\MVendor::findOrFail($vendor_id);
			$vendor_class = '\App\Vendor\\' . $m_vendor->class_name;
			if (! class_exists($vendor_class)) {
				throw new \Exception('Class 不存在. (' . $vendor_class . ')');
			}
			if ($m_vendor->disabled) {
				throw new \Exception($m_vendor->name . ' 已禁用.');
			}

			// 設定並執行付款
			$vendor = $vendor_class::getInstance($m_vendor)
				->setUsername($username)
				->setAmount($amount)
				->setPaymentType($payment_type)
				->setDatetime(date('YmdHis'));

			return $vendor->$payment_type();

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}
