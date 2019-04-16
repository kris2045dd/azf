<?php

namespace App\Helpers;

class Payment
{
	public static function getColumnMapping()
	{
		return [
			'alipay' => '支付宝',
			'alipaywap' => '支付宝 WAP',
			'wechat' => '微信支付',
			'wap' => '微信 WAP',
			'qq' => 'QQ',
			'qqwap' => 'QQ WAP',
			'jd' => '京东支付',
			'jdwap' => '京东支付 WAP',
			'unionfast' => '银联快捷',
			'unionfastwap' => '银联快捷 WAP',
			'bank' => '网银',
		];
	}

	public static function getColumnMappingByVendorId($vendor_id)
	{
		$column_mampping = [];

		$vendor_payment = \App\Model\MVendorPayment::find($vendor_id);
		if ($vendor_payment) {
			$column_mapping = self::getColumnMapping();

			foreach ($vendor_payment->toArray() as $k => $v) {
				if (isset($column_mapping[$k]) && $v) {
					$column_mampping[$k] = $column_mapping[$k];
				}
			}
		}

		return $column_mampping;
	}

}
