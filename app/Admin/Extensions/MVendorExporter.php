<?php

namespace App\Admin\Extensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MVendorExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{
    public function export()
    {
        $filename = $this->getTable().'.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

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
			'name', 'class_name',
			'alipay', 'alipaywap', 'wechat', 'wap', 'qq',
			'qqwap', 'jd', 'jdwap', 'unionfast', 'unionfastwap',
			'bank',
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
			$record->name,
			$record->class_name,
			$record->m_vendor_payment->alipay,
			$record->m_vendor_payment->alipaywap,
			$record->m_vendor_payment->wechat,
			$record->m_vendor_payment->wap,
			$record->m_vendor_payment->qq,
			$record->m_vendor_payment->qqwap,
			$record->m_vendor_payment->jd,
			$record->m_vendor_payment->jdwap,
			$record->m_vendor_payment->unionfast,
			$record->m_vendor_payment->unionfastwap,
			$record->m_vendor_payment->bank,
		];
    }
}
