<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BbinBotController extends Controller
{

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('BBIN 机器人')
            //->description('')
            ->body(view('admin/bbinBot/index'));
    }

	// Bot 登入
	public function logIn(\Illuminate\Http\Request $request)
	{
		$res = ['error' => '', 'msg' => ''];

		try {
			// 接收參數
			$input = $request->only(['domain_name', 'username', 'password', 'otpcode']);

			// 取得設定檔
			$m_settings = \App\Model\MSettings::findOrFail(1);
			if (empty($m_settings->bbin_api)) {
				throw new \Exception('尚未设置 BBIN API.');
			}

			// 呼叫 API
			$api = rtrim($m_settings->bbin_api, '/') . '/login_bbin';

			$client = new \GuzzleHttp\Client();
			$response = $client->request('GET', $api, [
				'query' => $input
			]);

			if ($response->getStatusCode() != 200) {
				throw new \Exception('GuzzleHttp request failed. (' . $response->getStatusCode() . ')');
			}

			$result = json_decode($response->getBody(), true);
			if (json_last_error() !== \JSON_ERROR_NONE) {
				throw new \Exception('JSON decode failed.');
			}

			// 檢查
			if (!isset($result['state']) || $result['state']!=0) {
				throw new \Exception('登录失败.' . (empty($result['message']) ? '' : " ({$result['message']})"));
			}

			// Response
			$res['error'] = -1;
		} catch (\Exception $e) {
			$res['error'] = $e->getCode();
			$res['msg'] = $e->getMessage();
		}

		return response()->json($res);
	}

	// Bot 登入狀態查詢
	public function loginState(\Illuminate\Http\Request $request)
	{
		$res = ['error' => '', 'msg' => ''];

		try {
			// 取得設定檔
			$m_settings = \App\Model\MSettings::findOrFail(1);
			if (empty($m_settings->bbin_api)) {
				throw new \Exception('尚未设置 BBIN API.');
			}

			// 呼叫 API
			$api = rtrim($m_settings->bbin_api, '/') . '/login_state';

			$client = new \GuzzleHttp\Client();
			$response = $client->request('GET', $api);

			if ($response->getStatusCode() != 200) {
				throw new \Exception('GuzzleHttp request failed. (' . $response->getStatusCode() . ')');
			}

			$result = json_decode($response->getBody(), true);
			if (json_last_error() !== \JSON_ERROR_NONE) {
				throw new \Exception('JSON decode failed.');
			}

			// 檢查
			if (! isset($result['state'])) {
				throw new \Exception('查询失败.' . (empty($result['message']) ? '' : " ({$result['message']})"));
			}

			// Response
			$res['error'] = -1;
			$res['msg'] = $result['state'] == 0 ? '已登录.' : '未登录.';
		} catch (\Exception $e) {
			$res['error'] = $e->getCode();
			$res['msg'] = $e->getMessage();
		}

		return response()->json($res);
	}

}
