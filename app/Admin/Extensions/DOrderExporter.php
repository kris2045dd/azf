<?php

namespace App\Admin\Extensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DOrderExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{
    public function export()
    {
        //$filename = $this->getTable() . '-' . date('Ymd') . '.csv';
        $filename = '订单-' . date('Ymd') . '.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

		// 設定相關資料
		$this->setVendorData();
		$this->setPaymentTypeData();
		$this->setPaidStateData();
		$this->setCheckedStatusData();

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
			'金流商',
			'支付方式',
			'订单号',
			'外部订单号',
			'帐号',
			'金额',
			'付款状态',
			'确认状态',
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
			$record->order_id,
			$this->getVendor($record->vendor_id),
			$this->getPaymentType($record->payment_type),
			$record->order_no,
			$record->order_no_outer,
			$record->username,
			$record->amount,
			$this->getPaidState($record->paid_status),
			$this->getCheckedState($record->checked_status),
			$record->memo,
			$record->created_at,
		];
    }

	/* 設定資料 */

	protected function setVendorData()
	{
		$data = [];

		$rows = \App\Model\MVendor::select('vendor_id', 'name')->get();
		foreach ($rows as $row) {
			$data[$row->vendor_id] = $row->name;
		}

		$this->vendors = $data;
	}

	protected function setPaymentTypeData()
	{
		$this->payment_types = \App\Helpers\Payment::getColumnMapping();
	}

	protected function setPaidStateData()
	{
		$this->paid_states = [
			\App\Model\DOrder::PAID_STATUS_DEFAULT => '未支付',
			\App\Model\DOrder::PAID_STATUS_SUCCESS => '支付成功',
			\App\Model\DOrder::PAID_STATUS_FAILED => '支付失败',
		];
	}

	protected function setCheckedStatusData()
	{
		$this->checked_states = [
			\App\Model\DOrder::CHECKED_STATUS_DEFAULT => '待确认',
			\App\Model\DOrder::CHECKED_STATUS_CONFIRMED => '已确认',
			\App\Model\DOrder::CHECKED_STATUS_CANCELED => '已取消',
		];
	}

	/* 取得資料 */

	protected function getVendor($vendor_id)
	{
		if (isset($this->vendors[$vendor_id])) {
			return $this->vendors[$vendor_id];
		}
		return '未知';
	}

	protected function getPaymentType($payment_type)
	{
		if (isset($this->payment_types[$payment_type])) {
			return $this->payment_types[$payment_type];
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

	protected function getCheckedState($checked_status)
	{
		if (isset($this->checked_states[$checked_status])) {
			return $this->checked_states[$checked_status];
		}
		return '未知';
	}

}
