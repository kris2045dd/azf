<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MVendor extends Model
{
	protected $table = 'm_vendor';
	protected $primaryKey = 'vendor_id';

	protected $fillable = ['class_name'];

	// one to one
	public function m_vendor_payment()
	{
		return $this->hasOne(\App\Model\MVendorPayment::class, 'vendor_id', 'vendor_id');
	}

	// one to many
	public function m_vendor_config()
	{
		return $this->hasMany(\App\Model\MVendorConfig::class, 'vendor_id', 'vendor_id');
	}
}
