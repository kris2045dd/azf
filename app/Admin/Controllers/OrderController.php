<?php

namespace App\Admin\Controllers;

use App\Model\DOrder;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
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
		$grid = $this->grid();							// Encore\Admin\Grid
		$filter = $grid->getFilter();					// Encore\Admin\Grid\Filter
		$conditions = $filter->conditions();			// Array
		$model = $grid->model();						// Encore\Admin\Grid\Model
		$model->addConditions($conditions);
		$query_builder = $model->getQueryBuilder();		// Illuminate\Database\Eloquent\Builder
		$query = $query_builder->getQuery();			// Illuminate\Database\Query\Builder
		/*
		$grid = $this->grid();							// Encore\Admin\Grid
		$collection = $grid->processFilter(false);		// Illuminate\Database\Eloquent\Collection
		$collection->sum('amount');						// 本頁金額加總
		*/
        return $content
            ->header('订单')
            ->description('列表 (总计金额: ' . number_format($query->sum('amount')) . ')')
            ->body($grid);
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
            ->header('订单')
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
            ->header('订单')
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
            ->header('订单')
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
        $grid = new Grid(new DOrder);

		// 資料
		$vendor_options = $this->getVendorOptions();
		$payment_type_options = $this->getPaymentTypeOptions();
		$paid_state_table = $this->getPaidStateTable();
		$checked_status_table = $this->getCheckedStateTable();

		// 由新到舊
		$grid->model()->orderBy('order_id', 'DESC');

		// 關閉選擇器
		$grid->disableRowSelector();
		// 自訂搜尋
		$grid->filter(function ($filter) use (
			$vendor_options,
			$payment_type_options,
			$paid_state_table,
			$checked_status_table
		) {
			// Remove the default id filter
			$filter->disableIdFilter();

			// 篩選條件
			$filter->equal('vendor_id', '金流商')->select($vendor_options);
			$filter->equal('payment_type', '支付方式')->select($payment_type_options);
			$filter->like('order_no', '订单号');
			$filter->like('order_no_outer', '外部订单号');
			$filter->like('username', '帐号');
			$filter->where(function ($query) {
				$query->where('amount', '>=', $this->input);
			}, '金额 (>=)')->currency();
			$filter->where(function ($query) {
				$query->where('amount', '<', $this->input);
			}, '金额 (<)')->currency();
			$filter->equal('paid_status', '付款状态')->radio(array_replace(
				['' => '全部'],
				$paid_state_table
			));
			$filter->equal('checked_status', '确认状态')->radio(array_replace(
				['' => '全部'],
				$checked_status_table
			));
			$filter->between('created_at', '建立日期')->datetime();
		});
		// 關閉新建按鈕
		$grid->disableCreateButton();
		// 自訂導出
		$grid->exporter(new \App\Admin\Extensions\DOrderExporter());
		// 關閉操作按鈕
		$grid->actions(function ($actions) {
			/*
			$actions->disableEdit();
			*/
			$actions->disableView();
			$actions->disableDelete();
		});

		$grid->column('order_id', '编号');
		$grid->column('vendor_id', '金流商')->display(function ($vendor_id) use ($vendor_options) {
			if (isset($vendor_options[$vendor_id])) {
				return $vendor_options[$vendor_id];
			}
			return "未知 ({$vendor_id})";
		});
		$grid->column('payment_type', '支付方式')->display(function ($payment_type) use ($payment_type_options) {
			if (isset($payment_type_options[$payment_type])) {
				return $payment_type_options[$payment_type];
			}
			return "未知 ({$payment_type})";
		});
		$grid->column('order_no', '订单号');
		$grid->column('order_no_outer', '外部订单号');
		$grid->column('username', '帐号');
		$grid->column('amount', '金额')->display(function ($amount) {
			return number_format($amount);
		});
		$grid->column('paid_status', '付款状态')->display(function ($paid_status) use ($paid_state_table) {
			return $paid_state_table[$paid_status];
		});
		$grid->column('checked_status', '确认状态')->editable('select', $checked_status_table);
		$grid->column('memo', '备注')->display(function ($memo) {
			return str_limit($memo, 16, ' ...');
		})->editable('textarea');
		$grid->column('created_at', '建立日期');
		//$grid->column('updated_at', '更新日期');

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
        $show = new Show(DOrder::findOrFail($id));

        $show->order_id('Order id');
        $show->vendor_id('Vendor id');
        $show->payment_type('Payment type');
        $show->order_no('Order no');
        $show->order_no_outer('Order no outer');
        $show->username('Username');
        $show->amount('Amount');
        $show->paid_status('Paid status');
        $show->checked_status('Checked status');
        $show->memo('Memo');
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
        $form = new Form(new DOrder);

		// 資料
		$vendor_options = $this->getVendorOptions();
		$payment_type_options = $this->getPaymentTypeOptions();
		$paid_state_table = $this->getPaidStateTable();
		$checked_status_table = $this->getCheckedStateTable();

		// 關閉工具
		$form->tools(function (Form\Tools $tools) {
			$tools->disableView();
			$tools->disableDelete();
			/*
			$tools->disableList();
			$tools->disableBackButton();
			$tools->disableListButton();
			*/
		});

		$form->select('vendor_id', '金流商')->options($vendor_options)->readonly();
		$form->select('payment_type', '支付方式')->options($payment_type_options)->readonly();
        $form->text('order_no', '订单号')->readonly();
        $form->text('order_no_outer', '外部订单号')->readonly();
        $form->text('username', '帐号')->readonly();
        $form->currency('amount', '金额')->symbol('RMB¥')->readonly();
		$form->select('paid_status', '付款状态')->options($paid_state_table)->readonly();
		$form->select('checked_status', '确认状态')->options($checked_status_table);
        $form->textarea('memo', '备注');

        return $form;
    }

	// 取得金流商 options for select
	protected function getVendorOptions($available_only = false)
	{
		$options = [];

		$rows = \App\Model\MVendor::select('vendor_id', 'name', 'disabled')->get();
		foreach ($rows as $row) {
			if ($available_only && $row->disabled) {
				continue;
			}
			$options[$row->vendor_id] = $row->name;
		}

		return $options;
	}

	// 取得支付方式 options for select
	protected function getPaymentTypeOptions()
	{
		return \App\Helpers\Payment::getColumnMapping();
	}

	// 取得支付狀態表
	protected function getPaidStateTable()
	{
		return [
			\App\Model\DOrder::PAID_STATUS_DEFAULT => '未支付',
			\App\Model\DOrder::PAID_STATUS_SUCCESS => '支付成功',
			\App\Model\DOrder::PAID_STATUS_FAILED => '支付失败',
		];
	}

	// 取得確認狀態表
	protected function getCheckedStateTable()
	{
		return [
			\App\Model\DOrder::CHECKED_STATUS_DEFAULT => '待确认',
			\App\Model\DOrder::CHECKED_STATUS_CONFIRMED => '已确认',
			\App\Model\DOrder::CHECKED_STATUS_CANCELED => '已取消',
		];
	}
}
