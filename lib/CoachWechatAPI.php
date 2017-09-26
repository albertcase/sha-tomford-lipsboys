<?php
namespace Lib;

use Core\Response;

class CoachWechatAPI {
	
	public function wechatAuthorize($callback) {
    	$response = new Response();
    	$response->redirect(COACH_AUTH_URL.'?callback='.$callback);  
  	}

  	public function getUserInfo($openid) {
	  	$api_url = "http://coach.samesamechina.com/v2/wx/users/no_cache/" . $openid . "?access_token=" . CURIO_TOKEN;
	    $ch = curl_init();
	    // print_r($ch);
	    curl_setopt ($ch, CURLOPT_URL, $api_url);
	    //curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt ($ch, CURLOPT_HEADER, 0);
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	    $info = curl_exec($ch);
	    curl_close($ch);
	    $rs = json_decode($info, true);
	    return $rs;
	}

	public function isSubscribed($openid) {
	    $info = $this->getUserInfo($openid);
	    if(isset($info['subscribe']) && $info['subscribe'] == 1)
	      return TRUE;
	    else
	      return FALSE;
	}
}