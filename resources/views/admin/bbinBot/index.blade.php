<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title"></h3>
					<div class="box-tools"></div>
				</div>

				<div class="box-body">
					<div class="nav-tabs-custom">
						{{-- Tabs --}}
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#tab-form-1" data-toggle="tab">
									机器人状态 <i class="fa fa-exclamation-circle text-red hide"></i>
								</a>
							</li>
							<li>
								<a href="#tab-form-2" data-toggle="tab">
									机器人登录 <i class="fa fa-exclamation-circle text-red hide"></i>
								</a>
							</li>
						</ul>

						{{-- Contents --}}
						<div class="tab-content fields-group">
							<div class="tab-pane active" id="tab-form-1">

								<form class="form-horizontal">

									<div class="fields-group">
										<div class="form-group">
											<label for="login_state" class="col-sm-2 control-label">登录状态</label>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
													<input type="text" id="login_state" name="login_state" class="form-control" placeholder="登录状态" readonly>
												</div>
											</div>
										</div>
									</div>

								</form>

								<div class="box-footer">
									<div class="col-md-2">
									</div>

									<div class="col-md-8">
										<div class="btn-group pull-right">
											<button type="button" id="query-login-state-btn" class="btn btn-primary">查询</button>
										</div>
									</div>
								</div><!-- .box-footer -->

							</div><!-- #tab-form-1 -->

							<div class="tab-pane" id="tab-form-2">

								<form class="form-horizontal">

									<div class="fields-group">
										<div class="form-group">
											<label for="domain_name" class="col-sm-2 control-label">Domain name</label>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
													<input type="text" id="domain_name" name="domain_name" class="form-control" value="https://cj168.01jsdc.com/" placeholder="输入 Domain name">
												</div>
											</div>
										</div>
									</div>

									<div class="fields-group">
										<div class="form-group">
											<label for="username" class="col-sm-2 control-label">帐号</label>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
													<input type="text" id="username" name="username" class="form-control" value="dzcheshi" placeholder="输入 帐号" readonly>
													<!-- dzcheshi -->
												</div>
											</div>
										</div>
									</div>

									<div class="fields-group">
										<div class="form-group">
											<label for="password" class="col-sm-2 control-label">密码</label>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
													<input type="password" id="password" name="password" class="form-control" value="dzcheshi456" placeholder="输入 密码" readonly>
													<!-- dzcheshi456 -->
												</div>
											</div>
										</div>
									</div>

									<div class="fields-group">
										<div class="form-group">
											<label for="otpcode" class="col-sm-2 control-label">OTP code</label>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
													<input type="text" id="otpcode" name="otpcode" class="form-control" placeholder="输入 OTP code">
												</div>
											</div>
										</div>
									</div>

								</form><!-- .form-horizontal -->

								<div class="box-footer">
									<div class="col-md-2">
									</div>

									<div class="col-md-8">
										<div class="btn-group pull-right">
											<button type="button" id="login-btn" class="btn btn-primary">登录</button>
										</div>
									</div>
								</div><!-- .box-footer -->

							</div><!-- #tab-form-2 -->
						</div>
					</div>

				</div><!-- .box-body -->

			</div>
		</div>
	</div>
</section>


<script>
$(function () {

	{{-- 查詢登入狀態按鈕 --}}
	$("#query-login-state-btn").click(function () {
		var $that = $(this), f = document.forms[0];

		if ($that.hasClass("disabled")) {
			return;
		}

		$.ajax({
			url: "{{ admin_base_path('bbinBot/loginState') }}",
			type: "post",
			dataType: "json",
			headers: {
				"X-CSRF-TOKEN": LA.token
			},
			beforeSend: function () {
				$that.addClass("disabled");
			},
			success: function (res) {
				if (res.error == -1) {
					toastr.success("查询成功");
					f.login_state.value = res.msg;
				} else if (res.msg) {
					alert(res.msg);
				} else {
					alert("发生未知的错误.");
				}
			},
			error: function (jqXHR, textStatus) {
				if (jqXHR.status == "419") {
					if (confirm("Session 已失效，请重新整理页面.")) {
						top.location.reload();
					}
				} else {
					alert("发送失败. (" + textStatus + ")");
				}
			},
			complete: function () {
				$that.removeClass("disabled");
			}
		});
	}).click();

	{{-- 登入按鈕 --}}
	$("#login-btn").click(function () {
		var $that = $(this),
			f = document.forms[1],
			error_msg = "";

		if ($that.hasClass("disabled")) {
			return;
		}

		f.domain_name.value == "" && (error_msg += "\n    Domain name");
		f.username.value == "" && (error_msg += "\n    帐号");
		f.password.value == "" && (error_msg += "\n    密码");
		f.otpcode.value == "" && (error_msg += "\n    OTP code");
		if (error_msg) {
			alert("请输入" + error_msg);
			return;
		}

		if (! confirm("确认?")) {
			return;
		}

		$.ajax({
			url: "{{ admin_base_path('bbinBot/logIn') }}",
			data: $(f).serialize(),
			type: "post",
			headers: {
				"X-CSRF-TOKEN": LA.token
			},
			beforeSend: function () {
				$that.addClass("disabled");
			},
			success: function (res) {
				if (res.error == -1) {
					toastr.success("登录成功");
				} else if (res.msg) {
					alert(res.msg);
				} else {
					alert("发生未知的错误.");
				}
			},
			error: function (jqXHR, textStatus) {
				if (jqXHR.status == "419") {
					if (confirm("Session 已失效，请重新整理页面.")) {
						top.location.reload();
					}
				} else {
					alert("发送失败. (" + textStatus + ")");
				}
			},
			complete: function () {
				$that.removeClass("disabled");
			}
		});
	});

});
</script>
