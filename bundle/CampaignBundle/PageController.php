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
        parent::__construct();
        $this->_pdo = PDO::getInstance();
        $this->helper = new Helper();
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

    public function qrcodeAction()
    {
        $config = array();
        return $this->render('qrcode', array('conf' => $config));   
    }

    public function proveAction()
    {
        $proveCode = $_GET['code'];
        $isProve = 0;
        $prove = $this->checkProveStatus($proveCode);
        if($prove) {
            $isProve = $prove->provestatus;
        }
        $config = array(
            'isProve' =>  $isProve,
        );
        return $this->render('consume', array('conf' => $config));   
    }

    public function clearCookieAction()
    {
      	$request = $this->Request();
		    setcookie('_user', '', time(), '/', $request->getDomain());
		    $this->statusPrint('success');
    }


    /**
     * 核销API
     */
    public function consumeAction()
    {
        $request = $this->request;
        $fields = array(
            'code' => array('notnull', '120'),
        );
        $request->validation($fields);
        $code = $request->request->get('code');

        $prove = $this->checkProveStatus($code);
        if($prove) {
            if((int)$prove->provestatus == 1) {
                $data = array('status' => 3, 'msg' => '该核销码已经核销过');
                $this->dataPrint($data);
            }
        } else {
            $data = array('status' => 2, 'msg' => '核销码错误');
            $this->dataPrint($data);
        }

        if(!$this->updateProveStatus($code)) {
            $data = array('status' => 0, 'msg' => '核销失败');
            $this->dataPrint($data);
        }
        $data = array('status' => 1, 'msg' => '核销成功');
        $this->dataPrint($data);
    }

    private function checkProveStatus($proveCode)
    {
        $sql = "SELECT `id`, `provestatus` FROM `apply` WHERE `provecode` = :provecode";
        $query = $this->_pdo->prepare($sql);
        $query->execute(array(':provecode' => $proveCode));
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if($row) {
            return (object)$row;
        }
        return 0;
    }

    private function updateProveStatus($code)
    {
        $condition = array(
            array('provecode', $code, '='),
        );
        $info = new \stdClass();
        $info->provestatus = 1;
        $info->updated = date('Y-m-d H:i:s');
        return $this->helper->updateTable('apply', $info, $condition);
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
        if((int)$list[0]['num'] > 0 || (int)$list[1]['num'] > 0) {
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
