<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertBackendDefaultMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// 新增後台預設選單
		$sql = <<<SQL
TRUNCATE TABLE `admin_menu`;
SQL;
		DB::statement($sql);

		$sql = <<<SQL
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `created_at`, `updated_at`) VALUES
(1, 0, 1, '首页', 'fa-bar-chart', '/', NULL, NULL),
(2, 0, 2, '后台管理', 'fa-tasks', '', NULL, NULL),
(3, 2, 3, '管理员', 'fa-users', 'auth/users', NULL, NULL),
(4, 2, 4, '角色', 'fa-user', 'auth/roles', NULL, NULL),
(5, 2, 5, '权限', 'fa-ban', 'auth/permissions', NULL, NULL),
(6, 2, 6, '菜单', 'fa-bars', 'auth/menu', NULL, NULL),
(7, 2, 7, '操作日志', 'fa-history', 'auth/logs', NULL, NULL),

(8, 0, 0, '金流商', 'fa-viacoin', 'vendor', NOW(), NULL),
(9, 0, 0, '层级管理', 'fa-user-secret', 'level', NOW(), NULL),
(10, 0, 0, '会员管理', 'fa-user', 'member', NOW(), NULL),
(11, 0, 0, '订单', 'fa-files-o', 'order', NOW(), NULL),
(12, 0, 0, '设置', 'fa-edit', 'setting', NOW(), NULL),
(13, 0, 0, 'BBIN 机器人', 'fa-bitcoin', 'bbinBot', NOW(), NULL),

# 開發者工具
(100, 0, 0, '开发者工具', 'fa-android', '', NOW(), NULL),
(101, 100, 0, 'Media manager', 'fa-file', 'media', NOW(), NULL),
(102, 100, 0, 'Scheduling', 'fa-clock-o', 'scheduling', NOW(), NULL),
(103, 100, 0, 'EnvManager', 'fa-gears', 'env-manager', NOW(), NULL),
(104, 100, 0, 'Helpers', 'fa-gears', '', NOW(), NULL),
(105, 104, 0, 'Scaffold', 'fa-keyboard-o', 'helpers/scaffold', NOW(), NULL),
(106, 104, 0, 'Database terminal', 'fa-database', 'helpers/terminal/database', NOW(), NULL),
(107, 104, 0, 'Laravel artisan', 'fa-terminal', 'helpers/terminal/artisan', NOW(), NULL),
(108, 104, 0, 'Routes', 'fa-list-alt', 'helpers/routes', NOW(), NULL);
SQL;
		DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
