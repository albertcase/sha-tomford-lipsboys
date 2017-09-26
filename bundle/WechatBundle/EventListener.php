<?php

namespace WechatBundle;

use Core\Request;
use Core\Event;
use Lib\UserAPI;
use Lib\CoachWechatAPI;
use Lib\SameWechatAPI;

class EventListener
{
	private $request;

	public function initUser(Event $event)
	{
		global $user;
		$this->request = $event->getRequest();
		$UserAPI = new UserAPI();
		$user = $UserAPI->userLoad();
		$this->authorize();
	}

	private function authorize()
	{
		global $user;
		$authorize_url = json_decode(AUTHORIZE_URL);
        if(!$user->uid) {
        	$request = $this->request;
        	$current_router = $request->getRouter();
        	if(in_array($current_router, $authorize_url)) {
        		$current_url = $request->getUrl(TRUE);
        		$function_name = WECHAT_VENDOR . 'WechatAuthoize';
						call_user_func_array(array($this, $function_name), array($request, $current_url));
        	}
        }
	}

	private function defaultWechatAuthoize($request, $current_url)
	{
	    $UserAPI = new UserAPI();
	    $UserAPI->oauthAction($current_url);
	}

	private function coachWechatAuthoize($request, $current_url)
	{
		$request->setSourceUrl($current_url);
		$WechatAPI = new CoachWechatAPI();
		$WechatAPI->wechatAuthorize(BASE_URL.'wechat/curio/callback');
	}

	// kenzo same oauth
	private function sameWechatAuthoize($request, $current_url)
	{
		// $request->setSourceUrl($current_url);
		$WechatAPI = new SameWechatAPI();
		$WechatAPI->wechatAuthorize(SCOPE, BASE_URL.'wechat/same/callback?redirect_uri='.$current_url);
	}
}
