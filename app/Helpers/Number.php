<?php

namespace App\Helpers;

class Number
{
	public static function stripDecimalsTailZero($num)
	{
		if (strrpos($num, '.') === false) {
			return $num;
		}
		return rtrim(rtrim($num, '0'), '.');
	}
}
