<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class MultiPkModelDeletion
{
	protected $pks;
	protected $model;
	protected $api_url;

	public function __construct(array $pks, $model, $api_url)
	{
		$this->pks = $pks;
		$this->model = $model;
		$this->api_url = $api_url;
	}

	protected function script()
	{
		$delete_confirm = trans('admin.delete_confirm');
		$confirm = trans('admin.confirm');
		$cancel = trans('admin.cancel');

		return <<<SCRIPT

$(".multi-pk-model-del-btn").on("click", function () {

	var pks = $(this).data("pks");

	swal({
		title: "{$delete_confirm}",
		type: "warning",
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
						model: "{$this->model}",
						pks: pks,
						_token:LA.token,
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
			if (data.error === "000") {
				swal(data.msg, "", "success");
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

		return '<a href="javascript:void(0);" class="multi-pk-model-del-btn" data-pks=\'' . json_encode($this->pks). '\'><i class="fa fa-trash"></i></a>';
	}

	public function __toString()
	{
		return $this->render();
	}

}
