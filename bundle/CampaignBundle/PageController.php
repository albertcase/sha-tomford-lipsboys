<?php

namespace CampaignBundle;

use Core\Controller;
use Lib\Helper;
use Lib\PDO;
use Lib\UserAPI;
use Lib\WechatAPI;

class PageController extends Controller
{

    public function __construct()
    {
        $this->_pdo = PDO::getInstance();
    }

    public function indexAction()
    {
        $isReservation = $this->checkUserApply();
        $config = array(
            'isReservation' => $isReservation,
        );
        return $this->render('index', array('conf' => $config));
    }

    public function clearCookieAction()
    {
      	$request = $this->Request();
		    setcookie('_user', '', time(), '/', $request->getDomain());
		    $this->statusPrint('success');
    }

    private function checkUserApply()
    {
        global $user;
        $sql = "SELECT `id`, `name` FROM `apply` WHERE `uid` = :uid";
        $query = $this->_pdo->prepare($sql);
        $query->execute(array(':uid' => $user->uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if($row) {
            return 1;
        }
        return 0;
    }
}
