<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Traits\CommonTrait as CommonTrait;

class CommonController extends Controller
{
	use CommonTrait;
	public function __construct()
	{
	}

	protected function view($template,$data=array())
	{
		return view($template,$data);
	}

	public function response($data,$status,$is_login_route = 0)
	{
		if(empty($data['message']))
		{
			$data['message'] = 'ok';
		}
		$data = array_merge(
				[
					"program" => APPLICATION_NAME,
					"release" => API_VERSION,
					"code" => $status,
					"message" => $data['message']
				],
			$data
		);
		array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
		return \Response::json($data,200);
	}
}
