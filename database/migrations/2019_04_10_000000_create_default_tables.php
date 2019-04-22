<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// 建立 會員 table
		$sql = <<<SQL
CREATE TABLE `d_member` (
	`username` VARCHAR(32) COLLATE utf8mb4_bin NOT NULL COMMENT '帳號',
	`level_id` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT '等級',
	`created_at` DATETIME NOT NULL COMMENT '建立日期',
	`updated_at` DATETIME COMMENT '更新日期',
	PRIMARY KEY (`username`)
)
COMMENT='會員'
DEFAULT CHARSET=utf8mb4
ENGINE=InnoDB
;
SQL;
		DB::statement($sql);


		// 建立 層級 table
		$sql = <<<SQL
CREATE TABLE `m_level` (
	`level_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`name` VARCHAR(16) NOT NULL COMMENT '名稱',
	`alipay` INT UNSIGNED DEFAULT NULL COMMENT '支付寶',
	`alipaywap` INT UNSIGNED DEFAULT NULL COMMENT '支付寶 WAP',
	`wechat` INT UNSIGNED DEFAULT NULL COMMENT '微信支付',
	`wap` INT UNSIGNED DEFAULT NULL COMMENT '微信 WAP',
	`qq` INT UNSIGNED DEFAULT NULL COMMENT 'QQ',
	`qqwap` INT UNSIGNED DEFAULT NULL COMMENT 'QQ WAP',
	`jd` INT UNSIGNED DEFAULT NULL COMMENT '京東支付',
	`jdwap` INT UNSIGNED DEFAULT NULL COMMENT '京東支付 WAP',
	`unionfast` INT UNSIGNED DEFAULT NULL COMMENT '銀聯快捷',
	`unionfastwap` INT UNSIGNED DEFAULT NULL COMMENT '銀聯快捷 WAP',
	`bank` INT UNSIGNED DEFAULT NULL COMMENT '網銀',
	`created_at` datetime NOT NULL COMMENT '建立日期',
	`updated_at` datetime COMMENT '更新日期',
	PRIMARY KEY (`level_id`)
)
COMMENT='層級'
DEFAULT CHARSET=utf8mb4
ENGINE=InnoDB
;
SQL;
		DB::statement($sql);


		// 建立 訂單 table
		$sql = <<<SQL
CREATE TABLE `d_order` (
	`order_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`vendor_id` INT UNSIGNED NOT NULL COMMENT '金流商 PK',
	`payment_type` VARCHAR(16) NOT NULL COMMENT '支付方式',
	`order_no` VARCHAR(64) NOT NULL COMMENT '訂單號',
	`order_no_outer` VARCHAR(64) NOT NULL COMMENT '外部訂單號',
	`username` VARCHAR(32) COLLATE utf8mb4_bin NOT NULL COMMENT '帳號',
	`amount` DECIMAL(10, 2) NOT NULL COMMENT '金額',
	`paid_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '付款狀態',
	`checked_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '確認狀態',
	`memo` VARCHAR(64) NULL COMMENT '備註',
	`created_at` DATETIME NOT NULL COMMENT '建立日期',
	`updated_at` DATETIME COMMENT '更新日期',
	PRIMARY KEY (`order_id`),
	KEY `idx_d_order_order_no` (`order_no`)
)
COMMENT='訂單'
DEFAULT CHARSET=utf8mb4
ENGINE=InnoDB
;
SQL;
		DB::statement($sql);


		// 建立 設定 table
		$sql = <<<SQL
CREATE TABLE `m_settings` (
	`setting_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`bbin_api` VARCHAR(128) DEFAULT NULL COMMENT 'BBIN API',
	`bbin_member_only` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '限 BBIN 會員充值',
	`bbin_query_level` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '從 BBIN 查詢層級',
	`bbin_auto_recharge` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'BBIN 自動上分',
	`alipay` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付寶',
	`alipaywap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付寶 WAP',
	`wechat` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '微信支付',
	`wap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '微信 WAP',
	`qq` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'QQ',
	`qqwap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'QQ WAP',
	`jd` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '京東支付',
	`jdwap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '京東支付 WAP',
	`unionfast` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '銀聯快捷',
	`unionfastwap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '銀聯快捷 WAP',
	`bank` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '網銀',
	`created_at` DATETIME NOT NULL COMMENT '建立日期',
	`updated_at` DATETIME COMMENT '更新日期',
	PRIMARY KEY (`setting_id`)
)
COMMENT='設定'
DEFAULT CHARSET=utf8mb4
ENGINE=InnoDB
;
SQL;
		DB::statement($sql);


		// 建立 金流商 table
		$sql = <<<SQL
CREATE TABLE `m_vendor` (
	`vendor_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`name` VARCHAR(32) NOT NULL COMMENT '金流商名稱',
	`class_name` VARCHAR(32) NOT NULL COMMENT 'Class 名稱',
	`disabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '禁用',
	`created_at` DATETIME NOT NULL COMMENT '建立日期',
	`updated_at` DATETIME COMMENT '更新日期',
	PRIMARY KEY (`vendor_id`),
	UNIQUE KEY `uk_m_vendor_class_name` (`class_name`)
)
COMMENT ='金流商'
DEFAULT CHARSET =utf8mb4
ENGINE =InnoDB
;
SQL;
		DB::statement($sql);


		// 建立 金流商付款方式 table
		$sql = <<<SQL
CREATE TABLE `m_vendor_payment` (
	`vendor_id` INT UNSIGNED NOT NULL COMMENT 'PK',
	`alipay` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付寶',
	`alipaywap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付寶 WAP',
	`wechat` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '微信支付',
	`wap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '微信 WAP',
	`qq` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'QQ',
	`qqwap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'QQ WAP',
	`jd` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '京東支付',
	`jdwap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '京東支付 WAP',
	`unionfast` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '銀聯快捷',
	`unionfastwap` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '銀聯快捷 WAP',
	`bank` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '網銀',
	`created_at` DATETIME NOT NULL COMMENT '建立日期',
	`updated_at` DATETIME COMMENT '更新日期',
	PRIMARY KEY (`vendor_id`),
	CONSTRAINT `fk_m_vendor_payment_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `m_vendor` (`vendor_id`)
)
COMMENT ='金流商支援的付款方式'
DEFAULT CHARSET =utf8mb4
ENGINE =InnoDB
;
SQL;
		DB::statement($sql);


		// 建立 金流商設定值 table
		$sql = <<<SQL
CREATE TABLE `m_vendor_config` (
	`vendor_config_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
	`vendor_id` INT UNSIGNED NOT NULL COMMENT 'Vendor PK',
	`k` VARCHAR(32) NOT NULL COMMENT 'Key',
	`v` VARCHAR(128) NOT NULL COMMENT 'Value',
	`desc` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '描述',
	`created_at` DATETIME NOT NULL COMMENT '建立日期',
	`updated_at` DATETIME COMMENT '更新日期',
	PRIMARY KEY (`vendor_config_id`),
	UNIQUE KEY `uk_m_vendor_config_vendor_id_k` (`vendor_id`, `k`),
	KEY `idx_m_vendor_config_vendor_id` (`vendor_id`),
	CONSTRAINT `fk_m_vendor_config_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `m_vendor` (`vendor_id`)
)
COMMENT ='金流商設定值'
DEFAULT CHARSET =utf8mb4
ENGINE =InnoDB
;
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
