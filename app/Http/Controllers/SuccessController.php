<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuccessController extends Controller
{

	/**
		支付成功頁面 (導頁)
	*/
	public function index(Request $request, $vendor = '')
	{
		try {

			/*
			$route = \Route::current();

			$vendor = $route->parameter('vendor');
			*/

			if ($vendor) {
				$m_vendor = \App\Model\MVendor::where('class_name', $vendor)->first();
				if ($m_vendor) {
					$vendor_class = '\App\Vendor\\' . $m_vendor->class_name;
					if (class_exists($vendor_class)) {
						// 指定頁面
						return $vendor_class::getInstance($m_vendor)->success();
					}
				}
			}

			// 預設頁面
			return '支付成功頁面.';

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}
