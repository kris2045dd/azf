<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class ActionBtn extends AbstractTool
{
	protected $options = [];

	public function __construct(array $options)
	{
		// [url, btn_id, btn_icon, btn_text, btn_style]
		$this->options = $options;
	}

	protected function script()
	{
		return <<<SCRIPT

$("#{$this->options['btn_id']}").click(function() {
	var that = $(this),
		btn_text = that.find(".btn-text"),
		text = btn_text.text();
	if (that.hasClass("disabled")) {
		return;
	}
	if (! confirm("执行 {$this->options['btn_text']}\\n确认?")) {
		return;
	}
	$.ajax({
		url: "{$this->options['url']}",
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

		return view('admin.tools.action_btn', $this->options);
	}

}
