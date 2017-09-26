<?php
namespace Lib;

class Base {

	public $_access_token;

	public function __construct($access_token = '') {
		$this->_access_token = $access_token;
	}	

	protected function postData($url, $post_json) {
		// post data to wechat
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
		$data = curl_exec($ch);
		curl_close($ch);
		return json_decode($data);
	}	
}