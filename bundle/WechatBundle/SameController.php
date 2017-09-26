<?php

namespace WechatBundle;

use Core\Controller;
use Lib\UserAPI;

class SameController extends Controller
{
	public function callbackAction()
	{
		$request = $this->request;
		$fields = array(
			'openid' => array('notnull', '120'),
			'redirect_uri' => array('notnull', '120')
		);
		$request->validation($fields);
		$openid = $request->query->get('openid');
		$url = urldecode($request->query->get('redirect_uri'));
		$userAPI = new UserAPI();
		$user = $userAPI->userLogin($openid);
		if(!$user) {
			$user = new \stdClass();
			$user->openid = $openid;
			$userAPI->userRegister($user);
		}
		$this->redirect($url);
	}

}
