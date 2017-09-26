<?php

namespace Core;

class Request
{
	public $request;

	public $query;

	public $params;

	public $validation;

	private $server;

	public function __construct() {

		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->params = $_GET;
			$this->query = $this;
		} else {
			$this->params = $_POST;
			$this->request = $this;
		}

		$this->server = $_SERVER;
	}

	public function get($param){
		if(isset($this->params[$param])) {
			return $this->params[$param];
		} else {
			return NULL;
		}
	}

	public function getServer(){
		return $this->server;
	}

	public function getDomain() {
		$domain = $_SERVER['HTTP_HOST'];
		$port = strpos($domain, ':');
		if ( $port !== false ) $domain = substr($domain, 0, $port);
		return $domain;
	}
	public function getUrl($absolute = false){
		if($absolute) {
			return $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		return $_SERVER['REQUEST_URI'];
	}

	public function getRouter(){
		return preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
	}

	public function validation($fields) {
		if($this->request) {
			$this->validRules($fields, $_POST);
			$_POST = $this->validation;
		}
		if($this->query) {
			$this->validRules($fields, $_GET);
			$_GET = $this->validation;
		}
	}

	public function validRules($fields, $raw) {
		$data = array();
		foreach($fields as $field => $info) {
			if(!isset($this->params[$field])) {
				$response = new Response;
		        $response->statusPrint('999');
			}
		    $value = trim($raw[$field]);
		    if($info) {
		      if($info[0] == 'notnull' && $value == '') {
		        $code = isset($info[1]) ? $info[1] : '999';
		        $response = new Response;
		        $response->statusPrint($code);
		      }
		      if($info[0] == 'date' && !strtotime($value)){
		        $code = isset($info[1]) ? $info[1] : '999';
		        $response = new Response;
		        $response->statusPrint($code);
		      }
	          if($info[0] == 'cellphone' && !preg_match("/^1\d{10}$/", $value)){
	          	 $code = isset($info[1]) ? $info[1] : '999';
		        $response = new Response;
		        $response->statusPrint($code);
	          }
		    }
		    $data[$field] = $value;
		}
		$this->validation = $data;
	}

	public function setSourceUrl($url, $type = 'cookie') {
		if($type == 'cookie') {
			setcookie("redirect_url", $url, time() + 3600*8, '/');
		}
	}

	public function getSourcetUrl($type = 'cookie') {
		if (ENV == 'dev') {
			return '/';
		}
		
		if($type == 'cookie') {
			if(isset($_COOKIE['redirect_url'])) {
				return $_COOKIE['redirect_url'];
			} 
		}
		return null;
	}
}
