<?php
define('SITE_URL', dirname(dirname(__FILE__)));
require_once SITE_URL . "/vendor/autoload.php";
require_once SITE_URL . "/config/config.php";

use Lib\Helper;
use Lib\PDO;
use \Lib\Redis;

$p = new pushConsume();
$p->sendSMSConsume();

class pushConsume
{
    private $helper;
    private $_pdo;
    private $accessToken;
    private $pushDate;

    public function __construct()
    {
        $this->helper = new Helper();
        $this->_pdo = PDO::getInstance();
        $this->accessToken = 'qckfPU4ijV-KCiU_cN5XQCZGN5sdNHk13RhPnkIBS8-aMurOLF2bqJGZeBA_6wS62AFABUgZgARM1RfU43Rt3DzfPNuzeU1KXJkTfVJ-1BPevm3We0QaG-7sc7owYsfBRVRcABAVZV';
        // $this->accessToken = $this->getAccessToken();
    }

    public function sendSMSConsume()
    {
        $sql = "SELECT `uid`, `phone`, `timeslot`, `provecode` FROM `apply` WHERE `sendstatus` = 0 AND `provestatus` = 0";
        $query = $this->_pdo->prepare($sql);
        $query->execute();
        while($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            // var_dump($row);exit;
            $timeslot = explode(' ', $row['timeslot']);
            $date = $timeslot[0];
            $url = "http://tf-lipsboys.samesamechina.com/qrcode?code={$row['provecode']}";
            // local test url
            // $url = "http://172.20.144.47:9122/qrcode?code={$row['provecode']}"; 
            $short_url = $this->shortUrl($url);
            if($short_url) {
                $this->send($row['phone'], $date, $short_url, $row['uid']);
                echo $row['phone'] . "send ok!\n";
            } else {
                echo $row['phone'] . "send failed!\n";
            }     
        }
        exit;
    }

    private function shortUrl($url)
    {   
        $apiUrl = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$this->accessToken}";
        $data = array(
            'action' => 'long2short',
            'long_url' => $url,
        );
        $rs = $this->postData($apiUrl, json_encode($data, JSON_UNESCAPED_UNICODE));
        if($rs->errcode == 0) {
            return $rs->short_url;
        } else {
            return false;
        }
    }

    private function send($moblie, $date, $url, $uid)
    {
        $ch = curl_init();
        $apikey = "b42c77ce5a2296dcc0199552012a4bd9";
        // $text = "【汤姆福特】\n请阁下于{$date}活动当日18:00-21:00凭此二维码予工作人员验证入场，请勿自行扫描以免失效。（此二维码只限本人使用）{$url}";
        $text = "【汤姆福特】\n请阁下于{$date}活动当日18:00-21:00凭此二维码予工作人员验证入场，请勿自行扫描以免失效。（此二维码只限本人使用）{$url}\n着装要求：鸡尾酒会晚装";
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
            $this->updateSendstatus($uid, 1);
            $smsStatus = 'success';
        } else {
            $smsStatus = 'failed';
        }

        //记录短信发送日志
        $smsLog = new \stdClass();
        $smsLog->type = 'consume';
        $smsLog->status = $smsStatus;
        $smsLog->api_data = json_encode($data);
        $smsLog->contents = $text;
        $smsLog->api_return = $json_data;
        $this->insertSMSLogs($smsLog, $uid);

        return $smsStatus;
    }

    private function updateSendstatus($uid, $status)
    {
        $condition = array(
          array('uid', $uid, '='),
        );
        $info = new \stdClass();
        $info->sendstatus = $status;
        $info->updated = date('Y-m-d H:i:s');
        return $this->helper->updateTable('apply', $info, $condition);
    }

    private function insertSMSLogs($logs, $uid)
    {
        $logs->uid = $uid;
        $logs->created = date('Y-m-d H:i:s');
        $log = (array) $logs;
        $this->helper->insertTable('sms_logs', $log);
    }

    /**
     * 获取WECHAT平台的 ACCESS TOKEN
     */
    public function getAccessToken()
    {
        $key = 'CYN6LEYUSZ2HJE2F';
        $iv = 'URY6L8JA4WN2SEJL';
        $return = file_get_contents('http://tomfordwechat.samesamechina.com/wechat/retrieve/access_token/CYN6LEYUSZ2HJE2F');
        $return = json_decode($return);
        if($return->status == 'success') {
            $string = base64_decode($return->data, TRUE);
            $access_token = $this->aes128_cbc_decrypt($key, $string, $iv);
            return $access_token;
        } else {
            return FALSE;
        }
    }

    /**
     * TOKEN 解密
     * @param $key
     * @param $data
     * @param $iv
     * @return bool|string
     */
    public function aes128_cbc_decrypt($key, $data, $iv)
    {
          if(16 !== strlen($key)) $key = hash('MD5', $key, true);
          if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
          $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
          $padding = ord($data[strlen($data) - 1]);
          return substr($data, 0, -$padding);
    }

    /**
     * POST DATA
     * @param $url
     * @param $post_json
     * @return mixed
     */
    private function postData($url, $post_json)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data);
    }

}