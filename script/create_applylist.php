<?php
define('SITE_URL', dirname(dirname(__FILE__)));
require_once SITE_URL . "/vendor/autoload.php";
require_once SITE_URL . "/config/config.php";

use Lib\Helper;
use Lib\PDO;
$list = array(
  array('name' => '2017-10-21 am', "num" => 10),
  array('name' => '2017-10-21 pm', "num" => 20),
  array('name' => '2017-10-22 am', "num" => 20),
  array('name' => '2017-10-22 pm', "num" => 5),
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
          $v['created'] = date('Y-m-d H:i:s');
          $v['status'] = 0;
          $id = $this->helper->insertTable('timeslot_list', $v);
      }
      echo "create apply list ok!\n";
    }
}
