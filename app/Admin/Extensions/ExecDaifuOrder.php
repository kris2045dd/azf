<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class ExecDaifuOrder
{
	protected $pk;
	protected $api_url;

	public function __construct($pk, $api_url)
	{
		$this->pk = $pk;
		$this->api_url = $api_url;
	}

	protected function script()
	{
		$title = '执行代付?';
		$confirm = trans('admin.confirm');
		$cancel = trans('admin.cancel');

		return <<<SCRIPT

$(".exec-daifu-order-btn").on("click", function () {

	var pk = $(this).data("pk");

	swal({
		title: "{$title}",
		type: "info",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "{$confirm}",
		showLoaderOnConfirm: true,
		cancelButtonText: "{$cancel}",
		preConfirm: function() {
			return new Promise(function(resolve) {
				$.ajax({
					method: "post",
					url: "{$this->api_url}",
					data: {
						pk: pk,
						_token: LA.token,
					},
					success: function (data) {
						$.pjax.reload("#pjax-container");
						resolve(data);
					}
				});
			});
		}
	}).then(function(result) {
		var data = result.value;
		if (typeof data === "object") {
			if (data.error == -1) {
				swal("成功!!", "", "success");
			} else {
				swal(data.msg, "", "error");
			}
		}
	});

});

SCRIPT;
	}

	protected function render()
	{
		Admin::script($this->script());

		return '<a href="javascript:void(0);" class="exec-daifu-order-btn" data-pk="' . $this->pk . '"><i class="fa fa-credit-card"></i></a>';
	}

	public function __toString()
	{
		return $this->render();
	}

}
