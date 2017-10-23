<?php
define('SITE_URL', dirname(dirname(__FILE__)));
require_once SITE_URL . "/vendor/autoload.php";
require_once SITE_URL . "/config/config.php";

use Lib\Helper;
use Lib\PDO;
use Lib\Redis;

$list = array(
    array('name' => '11月3日 18:00-21:00', "num" => 700),
    array('name' => '11月4日 18:00-21:00', "num" => 700),
);

$applylist = new ApplyList($list);
$applylist->createList();

class ApplyList
{
    private $helper;
    private $_pdo;
    private $ApplyList;

    public function __construct($list)
    {
        $this->helper = new Helper();
        $this->ApplyList = $list;
    }

    public function createList()
    {
      foreach ($this->ApplyList as $k => $v) {
          $redis = new Redis();
          $redis->hSet('quality', $v['name'], $v['num']);
          $v['created'] = date('Y-m-d H:i:s');
          $v['status'] = 0;
          $id = $this->helper->insertTable('timeslot_list', $v);
      }
      echo "create apply list ok!\n";
    }
}
