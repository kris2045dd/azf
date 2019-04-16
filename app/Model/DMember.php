<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DMember extends Model
{

	protected $table = 'd_member';
	protected $primaryKey = 'username';

	// Indicates if the IDs are auto-incrementing.
	public $incrementing = false;

}
