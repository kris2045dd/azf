<?php

namespace App\Admin\Controllers;

use App\Model\DMember;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MemberController extends Controller
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
            ->header('会员管理')
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
            ->header('会员管理')
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
            ->header('会员管理')
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
            ->header('会员管理')
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
        $grid = new Grid(new DMember);

		// 資料
		$level_options = $this->getLevelOptions();

		// 自訂搜尋
		$grid->filter(function ($filter) use ($level_options) {
			// Remove the default id filter
			$filter->disableIdFilter();

			// 過濾
			$filter->like('username', '帐号');
			$filter->equal('level_id', '层级')->select($level_options);
			$filter->between('created_at', '建立日期')->datetime();
			$filter->between('updated_at', '更新日期')->datetime();
		});
		// 關閉新建按鈕
		$grid->disableCreateButton();
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
			$tools->append(new \App\Admin\Extensions\Tools\ImportCsv(admin_base_path('member/import')));
			//$tools->prepend(new \App\Admin\Extensions\Tools\xxx());
		});
		/* 自訂導出
		$grid->exporter(new \App\Admin\Extensions\DMemberExporter());
		*/
		// 關閉操作按鈕
		$grid->actions(function ($actions) {
			/*
			$actions->disableEdit();
			$actions->disableDelete();
			*/
			$actions->disableView();
		});

		$grid->column('username', '帐号');
		$grid->column('level_id', '层级')->editable('select', $level_options);
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
        $show = new Show(DMember::findOrFail($id));

        $show->username('Username');
        $show->level_id('Level id');
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
        $form = new Form(new DMember);

		// 關閉操作按鈕
		$form->tools(function (Form\Tools $tools) {
			/*
			$tools->disableList();
			$tools->disableDelete();
			$tools->disableBackButton();
			$tools->disableListButton();
			*/
			$tools->disableView();
		});

		$form->text('username', '帐号')->readonly();
		$form->select('level_id', '层级')->options($this->getLevelOptions());

        return $form;
    }

	// 取得層級 options for select
	protected function getLevelOptions()
	{
		$rows = \App\Model\MLevel::select('level_id', 'name')->get();
		return array_column($rows->toArray(), 'name', 'level_id');
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

			// Bulk insert
			while ($rows = $this->getCsvContents($handle)) {
				$sql = "INSERT INTO d_member (username, level_id, created_at) VALUES ";
				$values = array_fill(0, count($rows), "(?, ?, NOW())");
				$binds = [];
				foreach ($rows as $row) {
					$username = $row[0];
					$level_id = empty($row[1]) ? 1 : $row[1];
					$binds[] = $username;
					$binds[] = $level_id;
				}
				$sql .= implode(', ', $values);
				$sql .= " ON DUPLICATE KEY UPDATE level_id =VALUES(level_id), updated_at =NOW()";
				\Illuminate\Support\Facades\DB::insert($sql, $binds);
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
}
