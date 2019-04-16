<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class QueryBalanceBtn extends AbstractTool
{
	protected $options = [];

	public function __construct(array $options)
	{
		// [url]
		$this->options = $options;
	}

	protected function script()
	{
		return <<<SCRIPT

$(".query-balance-btn").click(function () {
	var that = $(this),
		text = that.text(),
		vendor_id = that.data("vendor-id");
	if (that.hasClass("disabled")) {
		return;
	}
	$.ajax({
		url: "{$this->options['url']}",
		type: "post",
		data: {
			vendor_id: vendor_id
		},
		dataType: "json",
		headers: {
			"X-CSRF-TOKEN": LA.token
		},
		beforeSend: function () {
			that.addClass("disabled").text("in progress...");
		},
		success: function (res) {
			if (res.error == -1) {
				that.siblings(".account-balance").text(res.data);
			} else if (res.msg) {
				alert(res.msg);
			} else {
				alert("发生未知的错误.");
			}
		},
		error: function (jqXHR) {
			if (jqXHR.status == "419") {
				if (confirm("Session 已失效，请重新整理页面.")) {
					location.reload();
				}
			}
		},
		complete: function () {
			that.removeClass("disabled").text(text);
		}
	});
});

SCRIPT;
	}

	public function render()
	{
		Admin::script($this->script());

		return '';
	}

}
