<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

		/*
			Haima: Laravel-Admin

			\Illuminate\Support\Facades\Storage
				Storage::disk('admin')->exists('path/my_file');
				Storage::disk('admin')->get('path/my_file');		// 取得檔案內容
				Storage::disk('admin')->url('path/my_file');		// filesystems.php 需設定 url
		*/
		'admin' => [
			'driver' => 'local',
			'root' => storage_path('app/public/admin/uploads'),		// storage/app/public/admin/uploads
			'url' => '/storage/admin/uploads',						// [env('APP_URL') .] /storage/admin/uploads ([APP_URL]/storage/admin/uploads)
			'visibility' => 'public',
		],

		/*
			Haima: 檢視 Vendor Log 專用 (Media manager)

			public/storage: ln -s ../../storage/logs/vendor/ vendorLog
		*/
		'vendorLog' => [
			'driver' => 'local',
			'root' => storage_path('logs/vendor'),
			'url' => env('APP_URL') . '/storage/vendorLog',
			'visibility' => 'public',
		],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],

];
