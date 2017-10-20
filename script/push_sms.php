<?php
define('SITE_URL', dirname(dirname(__FILE__)));
require_once SITE_URL . "/vendor/autoload.php";
require_once SITE_URL . "/config/config.php";

use Lib\Helper;
use Lib\PDO;
use \Lib\Redis;

$p = new pushMsg();
$p->sendSMS();

class pushMsg
{
    private $helper;
    private $_pdo;
    private $accessToken;
    private $pushDate;

    public function __construct()
    {
        $this->helper = new Helper();
        $this->_pdo = PDO::getInstance();
    }

    public function sendSMS()
    {
        $sql = "SELECT a.`phone`, a.`uid`, a.`timeslot`, s.`status`, s.`type` FROM `apply` a, `sms_logs` s WHERE a.`uid` = s.`uid` AND s.`type` = 'applynotice' AND s.`status` = 'failed'";
        $query = $this->_pdo->prepare($sql);
        $query->execute();
        $i = 1;
        while($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $smsStatus = $this->send($row['phone'], $row['timeslot']);
            $this->updateSMSstatus($row['uid'], $smsStatus);
            echo "{$i}个人，手机号：{$row['phone']}, 补发状态：{$smsStatus}\n";
        }
    }

    private function send($moblie, $timeslot)
    {
        $ch = curl_init();
        $apikey = "b42c77ce5a2296dcc0199552012a4bd9";
        // $text = "【汤姆福特】您已预约成功。\n时间：2017年{$timeslot}\n地址：上海世博创意秀 (上海市黄浦区半淞园路498号) \n敬请莅临参与活动，谢谢您的支持。";
        $text = "【汤姆福特】您已预约成功。\n时间：2017年{$timeslot} \n地址：上海世博创意秀场 (上海市黄浦区半淞园路498号) \n活动前2-3日您会收到我们所发的专属二维码供入场使用。\n敬请莅临参与活动，谢谢您的支持。";
        $data = array(
          'text' => $text,
          'apikey' => $apikey,
          'mobile' => $moblie,
        );
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $json_data = curl_exec($ch);
        $res= json_decode($json_data,true);
        if($res['code'] == 0) {
            $smsStatus = 'success';
        } else {
            $smsStatus = 'failed';
        }
        return $smsStatus;
    }

    private function updateSMSstatus($uid, $status)
    {
        $condition = array(
          array('uid', $uid, '='),
          array('type', 'applynotice', '='),
        );
        $info = new \stdClass();
        $info->status = $status;
        $info->created = date('Y-m-d H:i:s');
        return $this->helper->updateTable('sms_logs', $info, $condition);
    }
}