<?php

define("BASE_URL", '127.0.0.1:9122/');
define("TEMPLATE_ROOT", dirname(__FILE__) . '/../template');
define("VENDOR_ROOT", dirname(__FILE__) . '/../vendor');

//ENV
define("ENV", 'dev');

//User
define("USER_STORAGE", 'COOKIE');

//
define("WECHAT_CAMPAIGN", true);

//Wechat Vendor
define("WECHAT_VENDOR", 'same'); // default | curio | same

//Wechat config info
define("TOKEN", '');
define("APPID", '');
define("APPSECRET", '');
define("NOWTIME", date('Y-m-d H:i:s'));
define("AHEADTIME", '1000');

define("NONCESTR", '?????');
define("COACH_AUTH_URL", '???');
define("SAME_OAUTH_URL", 'http://tomfordwechat.samesamechina.com/v1/wx/web/oauth2/authorize');

//Redis config info
define("REDIS_HOST", '127.0.0.1');
define("REDIS_DBNAME", 1);
define("REDIS_PORT", '6379');

//Database config info
define("DBHOST", '127.0.0.1');
define("DBUSER", 'root');
define("DBPASS", '');
define("DBNAME", 'tomford_lipsboys');

//Wechat Authorize
define("CALLBACK", '???');
define("SCOPE", 'snsapi_base');

//Wechat Authorize Page
define("AUTHORIZE_URL", '[
    "/"
]');

//Account Access
define("OAUTH_ACCESS", '{
	"xxxx": "samesamechina.com"
}');
define("JSSDK_ACCESS", '{
	"xxxx": "samesamechina.com",
	"dev": "127.0.0.1"
}');

define("ENCRYPT_KEY", '29FB77CB8E94B358');
define("ENCRYPT_IV", '6E4CAB2EAAF32E90');

define("WECHAT_TOKEN_PREFIX", 'wechat:token:');
