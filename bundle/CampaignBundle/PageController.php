<?php

namespace CampaignBundle;

use Core\Controller;
use Lib\Helper;
use Lib\PDO;
use Lib\UserAPI;
use Lib\WechatAPI;
use Lib\Redis;

class PageController extends Controller
{

    public function __construct()
    {
        $this->_pdo = PDO::getInstance();
    }

    public function indexAction()
    {
        $isReservation = 0;
        $applyDate = '';
        $applyRs = $this->checkUserApply();
        if($applyRs){
            $isReservation = 1;
            $applyDate = $applyRs->timeslot;
        }
        $maxNumber = $this->getApplitNum();
        $config = array(
            'isReservation' => $isReservation,
            'applyDate' => $applyDate,
            'maxNumber' => $maxNumber,
        );
        return $this->render('index', array('conf' => $config));
    }

    public function clearCookieAction()
    {
      	$request = $this->Request();
		    setcookie('_user', '', time(), '/', $request->getDomain());
		    $this->statusPrint('success');
    }

    private function getApplitNum()
    {
        $list = array(
            array('name' => '11月3日 18:00-21:00', "num" => 700),
            array('name' => '11月4日 18:00-21:00', "num" => 700),
        );
        $redis = new Redis();

        foreach($list as $k => $v) {
            $list[$k]['num'] = $redis->hGet('quality', $v['name']) ? $redis->hGet('quality', $v['name']) : 0;
        }
        if((int)$list[0]['num'] > 0 && (int)$list[1]['num'] > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    private function checkUserApply()
    {
        global $user;
        $sql = "SELECT `id`, `name`, `timeslot` FROM `apply` WHERE `uid` = :uid";
        $query = $this->_pdo->prepare($sql);
        $query->execute(array(':uid' => $user->uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if($row) {
            return (object)$row;
        }
        return 0;
    }
}
