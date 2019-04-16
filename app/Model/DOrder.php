<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DOrder extends Model
{

	const PAID_STATUS_DEFAULT = 0;
	const PAID_STATUS_SUCCESS = 1;
	const PAID_STATUS_FAILED = 2;

	const CHECKED_STATUS_DEFAULT = 0;
	const CHECKED_STATUS_CONFIRMED = 1;
	const CHECKED_STATUS_CANCELED = 2;

	protected $table = 'd_order';
	protected $primaryKey = 'order_id';

}
