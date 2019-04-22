<?php

namespace App\Admin\Controllers;

use App\Model\MSettings;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SettingController extends Controller
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
            ->header('设置')
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
            ->header('设置')
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
            ->header('设置')
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
            ->header('设置')
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
        $grid = new Grid(new MSettings);

		// 關閉行選擇器
		$grid->disableRowSelector();
		// 關閉篩選器
		$grid->disableFilter();
		// 關閉新建按鈕
		$grid->disableCreateButton();
		// 關閉匯出按鈕
		$grid->disableExport();
		// 關閉操作按鈕
		$grid->actions(function ($actions) {
			/*
			$actions->disableEdit();
			*/
			$actions->disableView();
			$actions->disableDelete();
		});

		//$grid->column('setting_id', '编号');
		$grid->column('bbin_api', 'BBIN API');
		$grid->column('bbin_member_only', '限 BBIN 会员充值')->switch([
			'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
			'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
		]);
		$grid->column('bbin_query_level', '从 BBIN 查询会员层级')->switch([
			'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
			'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
		]);
		$grid->column('bbin_auto_recharge', 'BBIN 自动上分')->switch([
			'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
			'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
		]);
		//$grid->column('created_at', '建立日期');
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
        $show = new Show(MSettings::findOrFail($id));

        $show->setting_id('Setting id');
        $show->bbin_member_only('Bbin member only');
        $show->bbin_query_level('Bbin query level');
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
        $form = new Form(new MSettings);

		// 關閉操作按鈕
		$form->tools(function (Form\Tools $tools) {
			/*
			$tools->disableList();
			$tools->disableBackButton();
			$tools->disableListButton();
			*/
			$tools->disableView();
			$tools->disableDelete();
		});

		$form->tab('设定', function ($form) {
			$form->text('bbin_api', 'BBIN API');
			$form->switch('bbin_member_only', '限 BBIN 会员充值')->states([
				'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
			])->help('透过 BBIN API 查询会员是否存在.');
			$form->switch('bbin_query_level', '从 BBIN 查询会员层级')->states([
				'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
			])->help('透过 BBIN API 查询会员层级. (会花费较多时间)');
			$form->switch('bbin_auto_recharge', 'BBIN 自动上分')->states([
				'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
			])->help('会员支付成功后, 自动上分至 BBIN.');
		})->tab('支付方式', function ($form) {
			$form->switch('alipay', '支付宝')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('alipaywap', '支付宝 WAP')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('wechat', '微信支付')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('wap', '微信 WAP')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('qq', 'QQ')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('qqwap', 'QQ WAP')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('jd', '京东支付')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('jdwap', '京东支付 WAP')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('unionfast', '银联快捷')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('unionfastwap', '银联快捷 WAP')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
			$form->switch('bank', '网银')->states([
				'on'  => ['value' => 1, 'text' => '开', 'color' => 'primary'],
				'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
			]);
		});

        return $form;
    }
}
