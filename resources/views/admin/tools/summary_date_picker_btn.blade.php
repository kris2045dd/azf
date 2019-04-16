<div class="col-lg-3 pull-right">
	<div class="input-group input-group-sm">
		<input type="text" id="summary-date" class="form-control" placeholder="结算日期" readonly>
		<span class="input-group-btn">
			<button type="button" id="summary-btn" class="btn btn-warning"<?php if ($tooltip) {echo ' data-toggle="tooltip" data-placement="' . $tooltip['placement'] . '" title="' . htmlspecialchars($tooltip['tooltip']) . '"';} ?>>
				<i class="fa fa-calculator"></i>&nbsp;&nbsp;<span class="btn-text">手动结算</span>
			</button>
		</span>
	</div>
</div>
