<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KyoController extends Controller
{

	public function test(Request $request)
	{
		/*
			HwFMn7KiIXOtaoi0+jeDPQ==
			Kyo Test

		$des3 = new \App\Helpers\Des3();
		echo $ret = $des3->encrypt("Kyo Test");
		echo '<br />';
		echo $des3->decrypt($ret);
		*/

		/*
		$pk = 1;
		$df_order = \App\Model\DDfOrder::findOrFail($pk);
		\App\Vendor\KaoTungPay::getInstance()
			->setDaifuOrder($df_order)
			->daifu();
		*/

		/*
		$rows = \App\Model\Vendor::select('vendor_id', 'name')->get();
		$vendors = array_column($rows->toArray(), 'name', 'vendor_id');
		print_r($vendors);
		*/

		/*
		$rows = \App\Model\SMemberBetting::where('betting_amount', '=', '161.000')->get();
		$model = $rows[0];
		print_r($model->toArray());
		*/

		/*
		return response('File not found.', 404);
		*/

		/*
		(new \App\Task\CalcMemberBetting())->run();
		(new \App\Task\CalcMemberLevelUp())->run();
		(new \App\Task\CalcWeeklySalary())->run();
		(new \App\Task\CalcMonthlySalary())->run();
		*/

		/*
		$sql = "SHOW TABLES";
		$rows = DB::connection('mysql2')->select($sql);
		print_r($rows);
		*/

		/*
		echo (session('not_exists') === null) ? 'Y' : 'N';
		echo (session('not_exists') === '') ? 'Y' : 'N';
		*/

		/*
		echo storage_path('app/public');							// D:\Websites\ggl\storage\app/public
		echo env('APP_URL') . '/storage';							// url: http://localhost/storage
		echo Storage::disk('admin')->url('images/reward_vip.jpg');	// 對應 config/filesystems.php 中 disks => admin => url . 'images/reward_vip.jpg'
		echo public_path('app/public');								// D:\Websites\ggl\public\app/public
		echo get_class(Storage::disk('admin'));						// Illuminate\Filesystem\FilesystemAdapter
		*/

		/* pull 會取得並刪除資料
		$member = $request->session()->get('member');
		print_r($member);

		$member = $request->session()->pull('member');
		print_r($member);
		*/

		/*
		echo $request->ip();
		*/

		/*
		echo 'A: ' . \Storage::disk('local')->getAdapter()->getPathPrefix();
		echo '<br />';
		echo 'B: ' . \Storage::disk('admin')->getAdapter()->getPathPrefix();
		echo '<br />';
		echo 'C: ' . public_path('admin/upload');
		echo '<br />';
		echo 'D: ' . \Storage::disk('admin')->url('image');
		echo '<br />';
		echo 'E: ' . \Storage::disk('admin')->path('image');
		*/

		/*
		$img = 'images/coin10.jpg';
		echo asset($img);	// for css、js、img
		echo '<br />';
		echo url($img);		// for link
		*/

		/*
		$request->session()->forget('user');

		$request->session()->put('user', 'Iori');
		$request->session()->put('user.name', 'kyo');
		$request->session()->put('user.age', 30);
		$request->session()->put('user.skills', ['HTML', 'PHP', 'MySQL']);

		$user = $request->session()->get('user');
		print_r($user);
		*/

		/*
		$name = $request->input('name');
		$age = $request->query('age');
		$sex = $request->post('sex');

		echo "name:{$name}, age:{$age}, sex:{$sex}";
		*/
	}

}
