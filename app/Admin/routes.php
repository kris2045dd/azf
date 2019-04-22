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

    $router->get('bbinBot', 'BbinBotController@index');
    $router->post('bbinBot/logIn', 'BbinBotController@logIn');
    $router->post('bbinBot/loginState', 'BbinBotController@loginState');

	$router->resource('member', MemberController::class);
	$router->resource('level', LevelController::class);
	$router->resource('vendor', VendorController::class);
	$router->resource('order', OrderController::class);
	$router->resource('setting', SettingController::class);

});
