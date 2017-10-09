<?php

namespace CampaignBundle;

use Core\Controller;
use Lib\Helper;
use Lib\PDO;
use Lib\UserAPI;
use Lib\WechatAPI;
use \Lib\Redis;
use \Lib\Captcher;

class ApiController extends Controller
{
    public function __construct() {

   	global $user;

        parent::__construct();

        if(!$user->uid) {
            $this->statusPrint('100', 'access deny!');
        }
        $this->_pdo = PDO::getInstance();
        $this->helper = new Helper();
    }

    public function applyListAction()
    {
        $sql = "SELECT `id`, `name`, `num`  FROM `timeslot_list`";
        $query = $this->_pdo->prepare($sql);
        $query->execute();
        $list = $query->fetchAll(\PDO::FETCH_ASSOC);
        if($list) {
            $data = array('status' => 1, 'msg' => 'get apply list success', 'data' => $list);
            $this->dataPrint($data);
        } else {
            $data = array('status' => 0, 'msg' => 'get apply list failed');
            $this->dataPrint($data);
        }
    }

    /*
     * 预约场次
     */
    public function applyAction()
    {
        global $user;

        if($this->checkUserApply()) {
            $data = array('status' => 3, 'msg' => 'apply again');
            $this->dataPrint($data);
        }

        $request = $this->request;
        $fields = array(
          'name' => array('notnull', '120'),
          'phone' => array('cellphone', '121'),
          'timeslot' => array('notnull', '120'),
        );
        $request->validation($fields);
        $name = $request->request->get('name');
        $phone = $request->request->get('phone');
        $timeslot = $request->request->get('timeslot');
        $apply = array(
            'uid' => $user->uid,
            'name' => $name,
            'timeslot' => $timeslot,
            'phone' => $phone,
            'created' => date('Y-m-d H:i:s'),
        );
        $applyId = $this->helper->insertTable('apply', $apply);
        if($applyId) {
            $data = array('status' => 1, 'msg' => 'apply success');
            $this->dataPrint($data);
        } else {
          $data = array('status' => 0, 'msg' => 'apply failed');
          $this->dataPrint($data);
        }
    }

    /**
     * 验证当前用户是否已经预约过
     */
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
        /**
     * 查找当前用户是否中奖
     */
    private function findLotteryByUid($uid)
    {
        $sql = "SELECT `id` FROM `lottery` WHERE `uid` = :uid AND status=1";
        $query = $this->_pdo->prepare($sql);
        $query->execute(array(':uid' => $uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if($row) {
            return 1;
        }
        return 0;
    }

    /**
     * 查看当前一共抽中多少奖
     */
    private function getApplySum($timeslot)
    {
        $sql = "select count(id) AS sum from apply";
        $query = $this->_pdo->prepare($sql);
        $query->execute();
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['sum'];
    }

    /**
     * 发送短信验证码
     */
    public function phoneCodeAction()
    {
        $request = $this->request;
        $fields = array(
          'mobile' => array('cellphone', '121'),
        );
        $request->validation($fields);

        $ch = curl_init();
        $apikey = "b42c77ce5a2296dcc0199552012a4bd9";
        $mobile = $request->request->get('mobile');
        $code = rand(1000, 9999);
        $RedisAPI = new Redis();
        $RedisAPI->setPhoneCode($mobile, $code, '3600');
        $text = "【Kenzo凯卓】您的验证码是{$code}";
        $data = array('text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile);
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $json_data = curl_exec($ch);
        $array = json_decode($json_data,true);
        $data = array('status' => 1, 'msg' => 'send ok');
        $this->dataPrint($data);
    }

    /**
     * 验证短信验证码
     */
    public function checkPhoneCodeAction()
    {
        $request = $this->request;
        $fields = array(
            'phone' => array('cellphone', '121'),
            'phonecode' => array('notnull', '120'),
        );
        $request->validation($fields);
        $phone = $request->request->get('phone');
        $phoneCode = $request->request->get('phonecode');
        if($this->checkMsgCode($phone, $phoneCode)) {
            $data = array('status' => 1, 'msg' => 'success');
        } else {
            $data = array('status' => 0, 'msg' => 'phone code is failed');
        }
        $this->dataPrint($data);
    }

    /**
     *  判断手机验证码是否正确
     */
    private function checkMsgCode($mobile, $msgCode) {
        $RedisAPI = new Redis();
        $code = $RedisAPI->get($mobile);
        if($code == $msgCode) {
            return true;
        } else {
            return false;
        }
    }

}
