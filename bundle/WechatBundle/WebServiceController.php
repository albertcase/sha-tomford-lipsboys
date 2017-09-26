<?php
namespace WechatBundle;

use Core\Controller;

class WebServiceController extends Controller
{
	public function oauthAction()
	{
		$request = $this->Request();
		$fields = array(
			'redirect_uri' => array('notnull', '110'),
			'scope' => array('notnull', '111'),
		);
		$request->validation($fields);
		$redirect_uri = $request->query->get('redirect_uri');
		$scope = $request->query->get('scope');
		$wechatUserAPI = new \Lib\UserAPI();
		$param['redirect_uri'] = $redirect_uri;
		$param['scope'] = $scope;
		$callback = BASE_URL . CALLBACK . '?' . http_build_query($param);
		$url = $wechatUserAPI->getAuthorizeUrl(APPID, $callback, $scope);
		$this->redirect($url);	
	}

	public function callbackAction()
	{
		$request = $this->Request();
		$fields = array(
			'redirect_uri' => array('notnull', '120'),
			'code' => array('notnull', '121'),
		);
		$request->validation($fields);
		$redirect_uri = $request->query->get('redirect_uri');
		$code = $request->query->get('code');
		$url = urldecode($redirect_uri);
		//valid
		$this->hostValid($url);
		$wechatUserAPI = new \Lib\UserAPI();
  		$access_token = $wechatUserAPI->getSnsAccessToken($code, APPID, APPSECRET);
		if(isset($access_token->openid)) {
			$param = array();
			if($access_token->scope == 'snsapi_base') {
				$param['openid'] = $access_token->openid;
			} 
			if($access_token->scope == 'snsapi_userinfo') {
				$param['openid'] = $access_token->openid;
				$param['access_token'] = $access_token->access_token;
			}
			$rediect_uri = $this->generateRedirectUrl($url, $param);
			$this->redirect($rediect_uri);
		}
	}

	/**
	 * Generate redirect uri
	 */
	public function generateRedirectUrl($url, $param)
	{
		$query = http_build_query($param);
		$question = "?{$query}&";
		$url = preg_replace('/\?/', $question, $url, 1, $count);
		if($count) {
			return $url;
		} 
		$hashtag = "?{$query}#";
		$url = preg_replace('/\#/', $hashtag, $url, 1, $count);
		if($count) {
			return $url;
		}
		$default = "?{$query}";
		return $url . $default;
	}

	/**
	 * JSSDK Webservice
	 */
	public function jssdkConfigWebServiceAction()
	{
		$request = $this->Request();
		$fields = array(
		    'url' => array('notnull', '120'),
	    );
		$request->validation($fields);
		$url = urldecode($request->query->get('url'));
	  	$this->hostValid($url);
	  	$config = $this->jssdkConfig($url);
	  	$this->dataPrint(array('status' => '1', 'data' => $config));
	}

	/**
	 * JSSDK JS
	 */
	public function jssdkConfigJsAction()
	{
		$request = $this->Request();
		$fields = array(
		    'url' => array('notnull', '120'),
	    );
		$request->validation($fields);
		$url = urldecode($request->query->get('url'));
	  	$this->hostValid($url);
	  	$config = $this->jssdkConfig($url);
	  	$json = json_encode(array('status' => '1', 'data' => $config));
	  	return $this->Response("SignWeiXinJs({$json})");
	}

	public function jssdkConfig($url)
	{
		$RedisAPI = new \Lib\RedisAPI();
		$jsapi_ticket = $RedisAPI->getJSApiTicket();
		$wechatJSSDKAPI = new \Lib\JSSDKAPI();
		return $wechatJSSDKAPI->getJSSDKConfig(APPID, $jsapi_ticket, $url);
	}

	public function hostValid($url, $type = OAUTH_ACCESS)
	{
		$parse_url = parse_url($url);
		if(!isset($parse_url['host'])) {
			$this->statusPrint('101', 'the host is invalid');
		}
		if(!in_array(preg_replace('/^.*?\./', '', $parse_url['host'], 1), (array)json_decode($type))) {
			$this->statusPrint('101', 'the host is invalid');
		}
	}

	public function retrieveAccessTokenAction($key)
	{
		if($key != ENCRYPT_KEY) {
			return $this->Response('key is wrong');
		}
		$RedisAPI = new \Lib\RedisAPI();
		$access_token = $RedisAPI->getAccessToken();
		$data = base64_encode($this->aes128_cbc_encrypt(ENCRYPT_KEY, $access_token, ENCRYPT_IV));
		return $this->Response($data);
	}

	public function retrieveAccessTokenCicAction()
	{
		$iv = 'sfsdfefsdfddsdss';
		if(!isset($_GET['key'])) {
			$json = json_encode(array('result' => 'fail', 'jsonResponse' => 'parameter error'));
	  		return $this->Response($json);
		}
		$RedisAPI = new \Lib\RedisAPI();
		$access_token = $RedisAPI->getAccessToken();
		$data = base64_encode($this->aes128_cbc_encrypt($_GET['key'], $access_token, $iv));
		$json = json_encode(array('result' => 'success', 'jsonResponse' => $data));
	  	return $this->Response($json);
	}

	public function aes128_cbc_encrypt($key, $data, $iv)
	{
		if(16 !== strlen($key)) $key = hash('MD5', $key, true);
		if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
		$padding = 16 - (strlen($data) % 16);
		$data .= str_repeat(chr($padding), $padding);
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
	}

	public function aes128_cbc_decrypt($key, $data, $iv)
	{
	  if(16 !== strlen($key)) $key = hash('MD5', $key, true);
	  if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
	  $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
	  $padding = ord($data[strlen($data) - 1]);
	  return substr($data, 0, -$padding);
	}

	public function retrieveUserinfoAction()
	{
		if(!isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
			$this->dataPrint(array('status' => '0', 'msg' => ''));
		}
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postData = json_decode($postStr);
		if(!isset($postData->access->user) || $postData->access->user != 'KIEHLS1') {
			$this->dataPrint(array('status' => '0', 'msg' => 'user is not exist'));
		}
		if(!isset($postData->access->pass) || $postData->access->pass != '2E72B44A131D255A') {
			$this->dataPrint(array('status' => '0', 'msg' => 'pass is wrong'));
		}
		if(!isset($postData->data->openid)) {
			$this->dataPrint(array('status' => '0', 'msg' => 'openid is empty!'));
		}
		$DatabaseAPI = new \Lib\DatabaseAPI();
		$DatabaseAPI->findCellphoneByOpenid($postData->data->openid);
		$bind = 0;
		$subscribe = 0;
 		if($data = $DatabaseAPI->findCellphoneByOpenid($postData->data->openid)) {
 			$bind = 1;
 		}
 		$wechatUserAPI = new \Lib\UserAPI($this->getAccessToken());
 		if($wechatUserAPI->isUserSubscribed($postData->data->openid)) {
 			$subscribe = 1;
 		}
 		$this->dataPrint(array('status' => '1', 'data' => array('bind' => $bind, 'subscribe' => $subscribe)));
	}

	public function sendTemplateAction()
	{
		if(!isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
			$this->dataPrint(array('status' => '0', 'msg' => ''));
		}
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postData = json_decode($postStr);
		if(!isset($postData->access->user) || $postData->access->user != 'KIEHLS1') {
			$this->dataPrint(array('status' => '0', 'msg' => 'user is not exist'));
		}
		if(!isset($postData->access->pass) || $postData->access->pass != '2E72B44A131D255A') {
			$this->dataPrint(array('status' => '0', 'msg' => 'pass is wrong'));
		}
		if(!isset($postData->data)) {
			$this->dataPrint(array('status' => '0', 'msg' => 'data is empty!'));
		}
		$templateMsgAPI = new \Lib\TemplateMsgAPI($this->getAccessToken());
		$re = $templateMsgAPI->postTemplate($postData->data);
		if($re->errcode == 0) {
			$this->dataPrint(array('status' => '1'));
		} else {
			$this->dataPrint(array('status' => $re->errcode, 'msg' => $re->errmsg));
		}
		
	}
}
