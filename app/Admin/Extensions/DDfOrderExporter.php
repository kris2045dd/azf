<?php

namespace App\Admin\Extensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DDfOrderExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{
    public function export()
    {
        //$filename = $this->getTable() . '-' . date('Ymd') . '.csv';
        $filename = '代付订单-' . date('Ymd') . '.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

		// 設定相關資料
		$this->setBankAccountData();
		$this->setVendorData();
		$this->setBankData();
		$this->setPaidStateData();
		$this->setAuditStateData();
		$this->setAdminUserData();

        response()->stream(function () {
            $handle = fopen('php://output', 'w');

            $titles = [];

            $this->chunk(function ($records) use ($handle, &$titles) {
                if (empty($titles)) {
                    $titles = $this->getHeaderRowFromRecords($records);

                    // Add CSV headers
                    fputcsv($handle, $titles);
                }

                foreach ($records as $record) {
                    fputcsv($handle, $this->getFormattedRecord($record));
                }
            });

            // Close the output stream
            fclose($handle);
        }, 200, $headers)->send();

        exit;
    }

    /**
     * @param Collection $records
     *
     * @return array
     */
    public function getHeaderRowFromRecords(Collection $records): array
    {
        return [
			'编号',
			'银行帐户',
			'金流商',
			'外部订单号',
			'收款银行',
			'收款卡(帐)号',
			'收款支(分)行',
			'收款户名',
			'金额',
			'收款人联系电话',
			'付款状态',
			'建单者',
			'审核状态',
			'审核备注',
			'备注',
			'建立日期',
		];
    }

    /**
     * @param Model $record
     *
     * @return array
     */
    public function getFormattedRecord(Model $record)
    {
        return [
			$record->df_order_id,
			$this->getBank($record->bank_account_id),
			$this->getVendor($record->vendor_id),
			$record->order_no_outer,
			$this->getBank($record->bank_id),
			$record->payee_account_no,
			$record->payee_branch,
			$record->payee_name,
			$record->amount,
			$record->payee_phone_no,
			$this->getPaidState($record->paid_status),
			$this->getAdminUser($record->creator),
			$this->getAuditState($record->audit_status),
			$record->audit_memo,
			$record->memo,
			$record->created_at,
		];
    }

	/* 設定資料 */

	protected function setBankAccountData()
	{
		$data = [];

		$rows = \App\Model\MBankAccount::select('bank_account_id', 'title')->get();
		foreach ($rows as $row) {
			$data[$row->bank_account_id] = $row->title;
		}

		$this->bank_accounts = $data;
	}

	protected function setVendorData()
	{
		$data = [];

		$rows = \App\Model\MVendor::select('vendor_id', 'name')->get();
		foreach ($rows as $row) {
			$data[$row->vendor_id] = $row->name;
		}

		$this->vendors = $data;
	}

	protected function setBankData()
	{
		$data = [];

		$rows = \App\Model\MBank::select('bank_id', 'name')->get();
		foreach ($rows as $row) {
			$data[$row->bank_id] = $row->name;
		}

		$this->banks = $data;
	}

	protected function setPaidStateData()
	{
		$this->paid_states = [
			\App\Model\DDfOrder::PAID_STATUS_DEFAULT => '未付款',
			\App\Model\DDfOrder::PAID_STATUS_PENDING => '处理中',
			\App\Model\DDfOrder::PAID_STATUS_SUCCESS => '已付款',
			\App\Model\DDfOrder::PAID_STATUS_FAILED => '付款失败',
		];
	}

	protected function setAuditStateData()
	{
		$this->audit_states = [
			\App\Model\DDfOrder::AUDIT_STATUS_DEFAULT => '未审核',
			\App\Model\DDfOrder::AUDIT_STATUS_ACCEPTED => '已核准',
			\App\Model\DDfOrder::AUDIT_STATUS_REJECTED => '拒绝',
		];
	}

	protected function setAdminUserData()
	{
		$user_model = config('admin.database.users_model');
		$rows = $user_model::select('id', 'name')->get();
		$this->admin_users = array_column($rows->toArray(), 'name', 'id');
	}

	/* 取得資料 */

	protected function getBankAccount($bank_account_id)
	{
		if (empty($bank_account_id)) {
			return '';
		}
		if (isset($this->bank_accounts[$bank_account_id])) {
			return $this->bank_accounts[$bank_account_id];
		}
		return '未知';
	}

	protected function getVendor($vendor_id)
	{
		if (empty($vendor_id)) {
			return '';
		}
		if (isset($this->vendors[$vendor_id])) {
			return $this->vendors[$vendor_id];
		}
		return '未知';
	}

	protected function getBank($bank_id)
	{
		if (empty($bank_id)) {
			return '';
		}
		if (isset($this->banks[$bank_id])) {
			return $this->banks[$bank_id];
		}
		return '未知';
	}

	protected function getPaidState($paid_status)
	{
		if (isset($this->paid_states[$paid_status])) {
			return $this->paid_states[$paid_status];
		}
		return '未知';
	}

	protected function getAuditState($audit_status)
	{
		if (isset($this->audit_states[$audit_status])) {
			return $this->audit_states[$audit_status];
		}
		return '未知';
	}

	protected function getAdminUser($admin_user_id)
	{
		if (empty($admin_user_id)) {
			return '';
		}
		if (isset($this->admin_users[$admin_user_id])) {
			return $this->admin_users[$admin_user_id];
		}
		return '未知';
	}

}
