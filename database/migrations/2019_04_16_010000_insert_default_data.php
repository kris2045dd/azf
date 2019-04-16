<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDefaultData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// 新增 層級 資料
		$sql = <<<SQL
TRUNCATE TABLE `m_level`;
SQL;
		DB::statement($sql);

		$sql = <<<SQL
INSERT INTO `m_level` (`level_id`, `name`, `created_at`, `updated_at`) VALUES
(1, '预设层级', NOW(), NULL);
SQL;
		DB::statement($sql);

		// 新增 設定 資料
		$sql = <<<SQL
TRUNCATE TABLE `m_settings`;
SQL;
		DB::statement($sql);

		$sql = <<<SQL
INSERT INTO `m_settings` (`setting_id`, `created_at`, `updated_at`) VALUES
(1, NOW(), NULL);
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
