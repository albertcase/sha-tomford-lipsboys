<?php
namespace Lib;

use Core\Response;

class SameWechatAPI {

		public function wechatAuthorize($scope, $callback)
		{
				$apiUrl = SAME_OAUTH_URL . '?scope=' . SCOPE . '&redirect_uri=' . urlencode($callback);
				$response = new Response();
				$response->redirect($apiUrl);
		}
}
