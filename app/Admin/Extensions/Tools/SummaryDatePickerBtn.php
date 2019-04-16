<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class SummaryDatePickerBtn extends AbstractTool
{
	protected $options = [];

	public function __construct($url, array $tooltip = [])
	{
		$this->options['url'] = $url;
		$this->options['tooltip'] = $tooltip;
	}

	protected function script()
	{
		$yesterday = date('Y-m-d', strtotime('-1 day'));

		return <<<SCRIPT

$('#summary-date').datetimepicker({
	"format": "YYYY-MM-DD",
	"locale": "zh-CN",
	"ignoreReadonly": true,
	"maxDate": new Date("{$yesterday}")	//"now"
});

$("#summary-btn").click(function() {
	var that = $(this),
		btn_text = that.find(".btn-text"),
		text = btn_text.text(),
		summary_date = $("#summary-date").val();
	if (that.hasClass("disabled")) {
		return;
	}
	if (! summary_date) {
		alert("请设定结算日期");
		return;
	}
	if (! confirm("手动结算 " + summary_date + "\\n确认?")) {
		return;
	}
	$.ajax({
		url: "{$this->options['url']}",
		data: {"target_date": summary_date},
		type: "post",
		dataType: "json",
		headers: {
			"X-CSRF-TOKEN": LA.token
		},
		beforeSend: function() {
			that.addClass("disabled");
			btn_text.text("in progress...");
		},
		success: function(res) {
			if (res.error === "000") {
				$.pjax.reload("#pjax-container");
				toastr.success("执行完毕 !");
			} else if (res.msg) {
				alert(res.msg);
			} else {
				alert("发生未知的错误.");
			}
		},
		error: function(jqXHR) {
			if (jqXHR.status == "419") {
				if (confirm("Session 已失效，请重新整理页面.")) {
					location.reload();
				}
			}
		},
		complete: function() {
			that.removeClass("disabled");
			btn_text.text(text);
		}
	});
});

SCRIPT;
	}

	public function render()
	{
		Admin::script($this->script());

		return view('admin.tools.summary_date_picker_btn', $this->options);
	}

}
