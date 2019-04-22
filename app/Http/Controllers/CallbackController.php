<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CallbackController extends Controller
{

	/**
		回調
	*/
	public function index(Request $request, $vendor, $payment_type, $username, $datetime)
	{
		try {

			// 取得金流商 Model
			$m_vendor = \App\Model\MVendor::where('class_name', $vendor)->first();
			if (! $m_vendor) {
				throw new \Exception("查无金流商资料. ({$vendor})");
			}

			$vendor_class = '\App\Vendor\\' . $m_vendor->class_name;
			if (! class_exists($vendor_class)) {
				throw new \Exception('Class 不存在. (' . $vendor_class . ')');
			}

			// 設定相關資料
			$vendor = $vendor_class::getInstance($m_vendor)
				->setUsername($username)
				->setPaymentType($payment_type)
				->setDatetime($datetime);

			// 執行回調
			return $vendor->callback($request);

			// 取得 m_settings
			// BBIN 自動上分 ?
			// 自動上分後更新訂單狀態

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}
