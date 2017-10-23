<?php
define('SITE_URL', dirname(dirname(__FILE__)));
require_once SITE_URL . "/vendor/autoload.php";
require_once SITE_URL . "/config/config.php";

use Lib\Helper;
use Lib\PDO;
use Lib\Redis;

$redis = new Redis();

$pkey = 'quality';

$feild = $argv[1] . " 18:00-21:00";
$num = $argv[2];

if($feild == '') {
    echo 'feild null';
}

if($num == '') {
    echo 'num null';
}
$nowNum = $redis->hGet($pkey, $feild);
$num = (int)$nowNum + (int)$num;

$redis->hSet($pkey, $feild, $num);

if($redis->hGet($pkey, $feild)) {
    echo "{$feild} update success\n";
}

exit;