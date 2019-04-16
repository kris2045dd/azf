<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

	$router->post('member/import', 'MemberController@import');
	$router->post('vendor/import', 'VendorController@import');

	$router->resource('member', MemberController::class);
	$router->resource('level', LevelController::class);
	$router->resource('vendor', VendorController::class);
	$router->resource('order', OrderController::class);
	$router->resource('setting', SettingController::class);

});
