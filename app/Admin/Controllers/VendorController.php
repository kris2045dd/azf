<?php

namespace App\Admin\Controllers;

use App\Model\MVendor;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VendorController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('金流商')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('金流商')
            ->description('检视')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('金流商')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('金流商')
            ->description('新建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MVendor);

		// 由新到舊
		$grid->model()->orderBy('vendor_id', 'DESC');
		// 關閉選擇器
		$grid->disableRowSelector();
		// 自訂搜尋
		$grid->filter(function ($filter) {
			// Remove the default id filter
			$filter->disableIdFilter();

			$select_options = ['all' => '全部'];
			$select_options = array_merge($select_options, \App\Helpers\Payment::getColumnMapping());

			// 過濾獎項
			$filter->like('name', '名称');
			$filter->like('class_name', 'Class 名称');
			$filter->equal('disabled', '禁用')->radio([
				'' => '全部',
				0 => '否',
				1 => '是',
			]);
			$filter->where(function ($query) {
				$input = $this->input;
				if ($input == 'all') {
					return;
				}
				$query->whereHas('m_vendor_payment', function ($query) use ($input) {
					$query->where($input, '=', 1);
				});
			}, '付款方式')->select($select_options);
		});
		// 自訂工具
		$grid->tools(function ($tools) {
			/* 關閉批次操作
			$tools->disableBatchActions();
			*/
			/* 關閉批次刪除
			$tools->batch(function ($batch) {
				$batch->disableDelete();
			});
			*/
			$tools->append(new \App\Admin\Extensions\Tools\ImportCsv(admin_base_path('vendor/import')));
			//$tools->prepend(new \App\Admin\Extensions\Tools\xxx());
		});
		// 關閉操作按鈕
		$grid->actions(function ($actions) {
			/*
			$actions->disableEdit();
			$actions->disableDelete();
			*/
			$actions->disableView();
		});
		// 自訂導出
		$grid->exporter(new \App\Admin\Extensions\MVendorExporter());

		$grid->column('vendor_id', '编号');
		$grid->column('name', '名称');
		$grid->column('class_name', 'Class 名称');
		$grid->column('disabled', '禁用')->switch([
			'on'  => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
			'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
		]);
		$grid->column('created_at', '建立日期');
		$grid->column('updated_at', '更新日期');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(MVendor::findOrFail($id));

        $show->vendor_id('Vendor id');
        $show->name('Name');
        $show->class_name('Class name');
        $show->disabled('Disabled');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MVendor);

		// 關閉工具
		$form->tools(function (Form\Tools $tools) {
			$tools->disableView();
			/*
			$tools->disableList();
			$tools->disableDelete();
			$tools->disableBackButton();
			$tools->disableListButton();
			*/
		});

		$form->tab('基本设定', function ($form) {
			//$form->display('vendor_id', '金流商 ID');
			$form->text('name', '名称');
			$form->text('class_name', 'Class 名称');
			$form->switch('disabled', '禁用');
			//$form->display('created_at', '建立日期');
			//$form->display('updated_at', '更新日期');
		})->tab('付款方式', function ($form) {
			$column_mapping = \App\Helpers\Payment::getColumnMapping();
			foreach ($column_mapping as $k => $v) {
				$form->switch("m_vendor_payment.{$k}", $v);
			}
		})->tab('参数设定', function ($form) {
			$html = <<<HTML
請新增下列設定<br />
Key: <strong class="text-primary">store_id</strong>, Value: <strong class="text-danger">{商户号}</strong><br />
Key: <strong class="text-primary">secret</strong>, Value: <strong class="text-danger">{密钥}</strong><br />
Key: <strong class="text-primary">pay_api</strong>, Value: <strong class="text-danger">{接口地址 API}</strong>
HTML;
			$form->html($html);

			$form->hasMany('m_vendor_config', '', function (Form\NestedForm $form) {
				$form->text('k', 'Key');
				$form->text('v', 'Value');
				$form->text('desc', '描述');
			});
		});

        return $form;
    }

	// CSV 資料匯入
	public function import(\Illuminate\Http\Request $request)
	{
		$res = ['error' => '', 'msg' => ''];

		try {
			$input_name = 'csv_file';
			if (! $request->hasFile($input_name)) {
				throw new \Exception('没有档案.');
			}

			$file = $request->file($input_name);
			$file_path = $file->path();

			$handle = fopen($file_path, 'r');
			if ($handle === false) {
				throw new \Exception('档案开启失败.');
			}

			// 修正 fgetcsv 遇到中文字讀取錯誤問題
			setlocale(\LC_ALL, 'en_US.UTF-8');

			$headers = [];
			$header_count = 0;

			$loaded_times = 0;
			$limit = 500;

			while ($rows = $this->getCsvContents($handle, $limit)) {
				foreach ($rows as $k => $row) {
					// 讀取標頭名稱 (第一行)
					if (empty($headers)) {
						$headers = $row;
						$header_count = count($headers);
						continue;
					}

					@list($name, $class_name) = $row;
					if (! isset($name, $class_name)) {
						$line = $loaded_times * $limit + $k + 1;
						throw new \Exception("第 {$line} 笔资料有误.");
					}

					// 新增 or 更新 m_vendor
					$m_vendor = \App\Model\MVendor::firstOrNew(['class_name' => $class_name]);
					$m_vendor->name = $name;
					$m_vendor->save();

					// 新增 or 更新 m_vendor_payment
					$m_vendor_payment = \App\Model\MVendorPayment::firstOrNew(['vendor_id' => $m_vendor->vendor_id]);
					for ($i = 2; $i < $header_count; $i++) {
						$field = $headers[$i];
						$value = isset($row[$i]) ? intval($row[$i]) : 0;
						$m_vendor_payment->$field = $value;
					}
					$m_vendor_payment->save();
				}
				$loaded_times++;
			}

			// Response
			$res['error'] = -1;
		} catch (\Exception $e) {
			$res['error'] = $e->getCode();
			$res['msg'] = $e->getMessage();
		} finally {
			if (isset($handle)) {
				fclose($handle);
			}
		}

		return response()->json($res);
	}

	protected function getCsvContents(&$handle, $limit = 500)
	{
		$contents = [];

		$i = 0;
		while ($data = fgetcsv($handle)) {
			$contents[] = $data;
			if (++$i >= $limit) {
				break;
			}
		}

		return $contents;
	}

	// 取得金流商支持的付款方式 options for select (BBIN: ajax)
	public function getPaymentOptionsByVendorId(\Illuminate\Http\Request $request)
	{
		$options = [];

		$q = $request->get('q');
		$column_mapping = \App\Helpers\Payment::getColumnMappingByVendorId($q);
		if ($column_mapping) {
			$options[] = [
				'id' => '',
				'text' => '-- 请选择 --'
			];
			foreach ($column_mapping as $k => $v) {
				$options[] = [
					'id' => $k,
					'text' => $column_mapping[$k]
				];
			}
		}

		return response()->json($options);
	}
}
