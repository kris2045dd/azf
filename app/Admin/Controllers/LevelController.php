<?php

namespace App\Admin\Controllers;

use App\Model\MLevel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class LevelController extends Controller
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
            ->header('层级管理')
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
            ->header('层级管理')
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
            ->header('层级管理')
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
            ->header('层级管理')
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
        $grid = new Grid(new MLevel);

		// 資料
		$vendors = \App\Model\MVendor::select('vendor_id', 'name')->get()->toArray();
		$vendors = array_column($vendors, 'name', 'vendor_id');

		// 關閉選擇器
		$grid->disableRowSelector();
		// 關閉篩選
		$grid->disableFilter();
		// 關閉匯出
		$grid->disableExport();
		// 關閉操作按鈕
		$grid->actions(function ($actions) {
			/*
			$actions->disableEdit();
			$actions->disableDelete();
			*/
			$actions->disableView();
			if ($actions->getKey() == 1) {
				$actions->disableDelete();
			}
		});

		$grid->column('level_id', '编号');
		$grid->column('name', '名称');
		$grid->column('alipay', '支付宝')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('alipaywap', '支付宝 WAP')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('wechat', '微信支付')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('wap', '微信 WAP')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('qq', 'QQ')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('qqwap', 'QQ WAP')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('jd', '京东支付')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('jdwap', '京东支付 WAP')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('unionfast', '银联快捷')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('unionfastwap', '银联快捷 WAP')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
		$grid->column('bank', '网银')->display(function ($vendor_id) use ($vendors) {
			if (isset($vendors[$vendor_id])) {
				return $vendors[$vendor_id];
			}
			return $vendor_id ? '未知' : '';
		});
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
        $show = new Show(MLevel::findOrFail($id));

        $show->level_id('Level id');
        $show->name('Name');
        $show->alipay('Alipay');
        $show->alipaywap('Alipaywap');
        $show->wechat('Wechat');
        $show->wap('Wap');
        $show->qq('Qq');
        $show->qqwap('Qqwap');
        $show->jd('Jd');
        $show->jdwap('Jdwap');
        $show->unionfast('Unionfast');
        $show->unionfastwap('Unionfastwap');
        $show->bank('Bank');
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
        $form = new Form(new MLevel);

		// 資料
		$sql =
			"SELECT
				v.vendor_id, v.name,
				vp.alipay, vp.alipaywap,
				vp.wechat, vp.wap,
				vp.qq, vp.qqwap,
				vp.jd, vp.jdwap,
				vp.unionfast, vp.unionfastwap,
				vp.bank
			FROM m_vendor AS v
				LEFT JOIN m_vendor_payment AS vp USING(vendor_id)
			ORDER BY v.vendor_id DESC";
		$vendors = \Illuminate\Support\Facades\DB::select($sql);

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

        $form->text('name', '名称');
        $form->select('alipay', '支付宝')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->alipay) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('alipaywap', '支付宝 WAP')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->alipaywap) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('wechat', '微信支付')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->wechat) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('wap', '微信 WAP')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->wap) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('qq', 'QQ')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->qq) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('qqwap', 'QQ WAP')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->qqwap) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('jd', '京东支付')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->jd) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('jdwap', '京东支付 WAP')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->jdwap) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('unionfast', '银联快捷')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->unionfast) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('unionfastwap', '银联快捷 WAP')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->unionfastwap) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});
        $form->select('bank', '网银')->options(function ($vendor_id) use ($vendors) {
			$options = [];
			foreach ($vendors as $vendor) {
				if ($vendor->bank) {
					$options[$vendor->vendor_id] = $vendor->name;
				}
			}
			return $options;
		});

        return $form;
    }
}
