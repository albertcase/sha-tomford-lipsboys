<?php
define('SITE_URL', dirname(dirname(__FILE__)));
require_once SITE_URL . "/vendor/autoload.php";
require_once SITE_URL . "/config/config.php";

use Lib\Helper;
use Lib\PDO;
use \Lib\Redis;

$list = array(
  array('name' => '10:00-11:00', "num" => 200),
  array('name' => '11:00-12:00', "num" => 200),
  array('name' => '12:00-13:00', "num" => 200),
  array('name' => '13:00-14:00', "num" => 200),
  array('name' => '14:00-15:00', "num" => 200),
  array('name' => '15:00-16:00', "num" => 200),
  array('name' => '16:00-17:00', "num" => 200),
  array('name' => '17:00-18:00', "num" => 200),
  array('name' => '18:00-19:00', "num" => 200),
  array('name' => '19:00-20:00', "num" => 200),
  array('name' => '20:00-21:00', "num" => 200),
  array('name' => '21:00-22:00', "num" => 200),
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
