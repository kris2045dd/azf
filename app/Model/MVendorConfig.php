<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MVendorConfig extends Model
{
	protected $table = 'm_vendor_config';
	protected $primaryKey = 'vendor_config_id';
	/* TODO: Laravel-Admin 不支援多 PK
	protected $primaryKey = ['vendor_id', 'k'];
	*/

	protected $fillable = ['k', 'v', 'desc'];

	/* Indicates if the IDs are auto-incrementing.
	public $incrementing = false;
	*/

	// one to many (inverse)
	public function m_vendor()
	{
		return $this->belongsTo(\App\Model\MVendor::class, 'vendor_id', 'vendor_id');
	}

	/*
		for multiple primary keys

		參考: https://stackoverflow.com/questions/36332005/laravel-model-with-two-primary-keys-update
	protected function setKeysForSaveQuery(\Illuminate\Database\Eloquent\Builder $query)
	{
		$keys = $this->getKeyName();
		if (! is_array($keys)) {
			return parent::setKeysForSaveQuery($query);
		}

		foreach ($keys as $key) {
			$query->where($key, '=', $this->getKeyForSaveQuery($key));
		}

		return $query;
	}

	protected function getKeyForSaveQuery($key = null)
	{
		if (is_null($key)) {
			$key = $this->getKeyName();
		}

		if (isset($this->original[$key])) {
			return $this->original[$key];
		}

		return $this->getAttribute($key);
	}
	*/
}
