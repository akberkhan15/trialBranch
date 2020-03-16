<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function response($data,$status,$is_login_route = 0)
	{
		if(empty($data['message']))
		{
			$data['message'] = 'ok';
		}
		$data = array_merge(
				[
					"program" => "Lumen Project",
					"release" => "v1",
					"code" => $status,
					"message" => $data['message']
				],
			$data
		);
		array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
		return response()
                ->json($data, 400, [], JSON_PRETTY_PRINT);
	}
}
