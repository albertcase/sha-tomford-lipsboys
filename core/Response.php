<?php
namespace Core;

class Response
{

	public $response;

	public function __construct($response = '') 
	{
		$this->response = $response;
	}

	public function send() {
		print $this->response;
		exit;
	}

	public function statusPrint($status, $msg = '') 
	{
		$data = array();
		if($status || $status == 0)
			$data['status'] = $status;
		if($msg)
			$data['msg'] = $msg;
		header("Content-type: application/json");
		print json_encode($data);
		exit;
	}

	public function dataPrint($data) 
	{
		header("Content-type: application/json");
		print json_encode($data);
		exit;
	}

	public function redirect($uri) 
	{
		Header('Location:' . $uri);
		exit;
	}
}