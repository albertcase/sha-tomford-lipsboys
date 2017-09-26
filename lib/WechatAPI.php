<?php

namespace Lib;

class WechatAPI extends Base {

	private $_token;
	private $_appid;
	private $_appsecret;
	private $_redis;

	public function __construct() {
		$this->_redis = Redis::getInstance();
	}

	public function retrieveAccessToken() {
		$applink = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
		$url = sprintf($applink, APPID, APPSECRET);
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}

	public function getTicket($type = 'jsapi_ticket') {
		$types = array('jsapi_ticket' => 'jsapi', 'api_ticket' => 'wx_card');
		$applink = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=%s";
		$url = sprintf($applink, $this->getAccessToken(), $types[$type]);
		$ticket = file_get_contents($url);
		return json_decode($ticket);
	}

	public function getUserInfo($openid) {
		$applink = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN";
		$url = sprintf($applink, $this->getAccessToken(), $openid);
		$return = file_get_contents($url);
		return json_decode($return);
	}

	public function isUserSubscribed($openid) {
		$applink = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN";
		$url = sprintf($applink, $this->getAccessToken(), $openid);
		$return = file_get_contents($url);
		$rs = json_decode($return);
		if(isset($rs->subscribe) && $rs->subscribe == 1)
		  return TRUE;
		else
		  return FALSE;
	}

	public function getAuthorizeUrl($callback) {
		$redirect_uri = urlencode($callback);
		$applink = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=STATE#wechat_redirect";
		$url = sprintf($applink, APPID, $redirect_uri, SCOPE);
		return $url;
	}

	public function getSnsAccessToken($code) {
		$applink = "https://api.weixin.qq.com/sns/oauth2/access_token?code=%s&grant_type=authorization_code&appid=%s&secret=%s";
		$url = sprintf($applink, $code, APPID, APPSECRET);
		$return = file_get_contents($url);
		return json_decode($return);
	}

	public function getSnsUserInfo($openid, $sns_access_token) {
		$applink = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN";
		$url = sprintf($applink, $sns_access_token, $openid);
		$userinfo = file_get_contents($url);
		$userinfo = json_decode($userinfo);
		return $userinfo;
	}

	public function getAccessToken() {
		return $this->getAccessKey();
	}

	public function getJSApiTicket() {
		return $this->getAccessKey('jsapi_ticket');
	}

	private function getAccessKey($type = 'access_token') {
		$key = WECHAT_TOKEN_PREFIX . $type;
  		if($key_value = $this->_redis->get($key)) {
    		return $key_value;
  		} else {
    		if($type == 'access_token') {
  				$data = $this->retrieveAccessToken();
  				if(isset($data->access_token)) {
  					$key_value = $data->access_token;
	        		$expires_in = $data->expires_in - AHEADTIME;
  				}
		    } else {
		      $data = $this->getTicket($type);
		      if($data->ticket){
		        $key_value = $data->ticket;
		        $expires_in = $data->expires_in - AHEADTIME;
		      }
		    }
		    $this->_redis->set($key, $key_value);
			$this->_redis->setTimeout($key, $expires_in);
			return $key_value;
  		}
	}

}
