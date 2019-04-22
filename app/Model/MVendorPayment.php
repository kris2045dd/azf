<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MVendorPayment extends Model
{
	const PAYMENT_TYPES = [
		'alipay', 'alipaywap',
		'wechat', 'wap',
		'qq', 'qqwap',
		'jd', 'jdwap',
		'unionfast', 'unionfastwap',
		'bank',
	];

	protected $table = 'm_vendor_payment';
	protected $primaryKey = 'vendor_id';

	protected $fillable = ['vendor_id'];

	// one to one
	public function m_vendor()
	{
		return $this->belongsTo(\App\Model\MVendor::class, 'vendor_id', 'vendor_id');
	}
}
