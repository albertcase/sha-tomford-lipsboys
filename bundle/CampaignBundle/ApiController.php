<?php

namespace CampaignBundle;

use Core\Controller;
use Lib\Helper;
use Lib\PDO;
use Lib\UserAPI;
use Lib\WechatAPI;
use Lib\Redis;

class ApiController extends Controller
{
    private $_pdo;
    private $helper;
    private $accessToken;

    public function __construct()
    {
   	    global $user;
        parent::__construct();
        if(!$user->uid) {
            $this->statusPrint('100', 'access deny!');
        }
        $this->_pdo = PDO::getInstance();
        $this->helper = new Helper();
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * 预约场次
     */
    public function applyAction()
    {
        global $user;
        $key = "apply:{$user->openid}";

        try {
            //判断是否已经预约过
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

            if(!$this->checkTimeslot($timeslot)) {
                $data = array('status' => 3, 'msg' => 'timeslot is failed');
                $this->dataPrint($data);
            }

            $redis = new Redis();
            $redis->setTimeout($key, 0);
            if(!$redis->get($key)) {
                $redis->set($key, 1);
                $redis->setTimeout($key, 10);
                //场次无名额
                if($redis->hGet('quality', $timeslot) <= 0) {
                    $data = array('status' => 2, 'msg' => '抱歉，您选择的时间已约满，请换个时间再试试吧~');
                    $this->dataPrint($data);
                }
                //库存减成功
                if($this->inCreateCountNum($timeslot)) {
                    //生成核销码
                    $proveCode = $this->create_uuid();
                    $apply = array(
                        'uid' => $user->uid,
                        'name' => $name,
                        'timeslot' => $timeslot,
                        'phone' => $phone,
                        'provecode' => $proveCode,
                        'created' => date('Y-m-d H:i:s'),
                    );
                    $applyId = $this->helper->insertTable('apply', $apply);
                    if($applyId) {
                        $this->sndSMS($phone, $timeslot);
                        $this->sendTmp($user->openid, 'fnuTFmQ5GIJSALzZ6RTTlwmye_e8WC_D6AUt3pagLY8', $name, $timeslot);
                        $redis->setTimeout($key, 0);
                        $data = array('status' => 1, 'msg' => 'apply success');
                        $this->dataPrint($data);
                    } else { //TODO
                        $redis->setTimeout($key, 0);
                        $data = array('status' => 0, 'msg' => '未能完成预约，请再试一次。');
                        $this->dataPrint($data);
                    }
                } else {
                    $data = array('status' => 2, 'msg' => '抱歉，您选择的时间已约满，请换个时间再试试吧~');
                    $this->dataPrint($data);
                }
            } else {
                $data = array('status' => 3, 'msg' => '您已完成过预约，欢迎届时莅临门店。');
                $this->dataPrint($data);
            }
        } catch (Exception $e) {
            $redis->setTimeout($key, 0);
        }
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
        global $user;
        $sql = "SELECT `id`, `provestatus` FROM `apply` WHERE `provecode` = :provecode and `uid` = :uid";
        $query = $this->_pdo->prepare($sql);
        $query->execute(array(':provecode' => $proveCode, ':uid' => $user->uid));
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if($row) {
            return (object)$row;
        }
        return 0;
    }

    private function updateProveStatus($code)
    {
        global $user;
        $condition = array(
            array('uid', $user->uid, '='),
            array('provecode', $code, '='),
        );
        $info = new \stdClass();
        $info->provestatus = 1;
        $info->updated = date('Y-m-d H:i:s');
        return $this->helper->updateTable('apply', $info, $condition);
    }

    /**
     * 写入预约剩余名额
     * 如果已经为0不做处理
     */
    private function inCreateCountNum($key, $num = -1) {
        $redis = new Redis();
        if($redis->hGet('quality', $key) <= 0) {
            return false;
        }
        if($redis->hInCrby('quality', $key, $num)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取预约场次列表
     */
    public function applyListAction()
    {
        $list = array(
            array('name' => '11月3日 18:00-21:00', "num" => 700),
            array('name' => '11月4日 18:00-21:00', "num" => 700),
        );
        $redis = new Redis();

        foreach($list as $k => $v) {
            $list[$k]['num'] = $redis->hGet('quality', $v['name']) ? $redis->hGet('quality', $v['name']) : 0;
        }

        if($list) {
            $data = array('status' => 1, 'msg' => 'get apply list success', 'data' => $list);
            $this->dataPrint($data);
        } else {
            $data = array('status' => 0, 'msg' => 'get apply list failed');
            $this->dataPrint($data);
        }
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
     * 发送模版消息
     */
    private function sendTmp($openid, $tmpid, $name, $timeslot)
    {
        $timeslot = '2017年' . $timeslot;
        $data = array(
          'touser' => $openid,
          'template_id' => $tmpid,
          'topcolor' => "#FF0000",
          'data' => array(
              'first' => array(
                  'value' => '恭喜您，预约成功',
                  'color' => '#173177',
              ),
              'keyword1' => array(
                  'value' => $name,
                  'color' => '#173177',
              ),
              'keyword2' => array(
                  'value' => 'TOM FORD Lips & Boys唇魅之夜',
                  'color' => '#173177',
              ),
              'keyword3' => array(
                  'value' => $timeslot,
                  'color' => '#173177',
              ),
              'remark' => array(
                  'value' => '敬请莅临参与活动，谢谢您的支持。',
                  'color' => '#173177',
              ),
          ),
        );
        $applink = "http://tomfordwechat.samesamechina.com/v2/wx/template/send?access_token=%s";
        $url = sprintf($applink, '184fffde-60fd-4779-a767-c4de49f1cdff');
        $rs = $this->postData($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $rs;
    }

    /**
     * 发送短信提醒
     */
    private function sndSMS($moblie, $timeslot)
    {
        $ch = curl_init();
        $apikey = "b42c77ce5a2296dcc0199552012a4bd9";
        $text = "【汤姆福特】您已预约成功。\n时间：2017年{$timeslot}\n地址：上海世博创意秀场 (上海市黄浦区半淞园路498号)\n活动前2-3日您会收到我们所发的专属二维码供入场使用。敬请莅临参与活动，谢谢您的支持。";
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

        //记录短信发送日志
        $smsLog = new \stdClass();
        $smsLog->type = 'applynotice';
        $smsLog->status = $smsStatus;
        $smsLog->api_data = json_encode($data);
        $smsLog->contents = $text;
        $smsLog->api_return = $json_data;
        $this->insertSMSLogs($smsLog);
        return true;
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
     * 验证场次是否正确
     */
    private function checkTimeslot($timeslot)
    {
        $sql = "SELECT `id` FROM `timeslot_list` WHERE `name` = :timeslot";
        $query = $this->_pdo->prepare($sql);
        $query->execute(array(':timeslot' => $timeslot));
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        if($row) {
            return 1;
        }
        return 0;
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
        $text = "【汤姆福特】您的验证码是{$code}";
        $data = array('text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile);
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $json_data = curl_exec($ch);
        $res = json_decode($json_data,true);
        if($res['code'] == 0) {
            $smsStatus = 'success';
        } else {
            $smsStatus = 'failed';
        }

        //记录短信发送日志
        $smsLog = new \stdClass();
        $smsLog->type = 'phonecode';
        $smsLog->status = $smsStatus;
        $smsLog->api_data = json_encode($data);
        $smsLog->contents = $text;
        $smsLog->api_return = $json_data;
        $this->insertSMSLogs($smsLog);

        $data = array('status' => 1, 'msg' => 'send ok');
        $this->dataPrint($data);
    }

    private function insertSMSLogs($logs)
    {
        global $user;
        $logs->uid = $user->uid;
        $logs->created = date('Y-m-d H:i:s');
        $log = (array) $logs;
        $this->helper->insertTable('sms_logs', $log);
    }

    /**
     *  判断手机验证码是否正确
     */
    private function checkMsgCode($mobile, $msgCode)
    {
        $RedisAPI = new Redis();
        $code = $RedisAPI->get($mobile);
        if($code == $msgCode) {
            return true;
        } else {
            return false;
        }
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

     /**
     * 生成UUID
     */
    private function create_uuid($prefix = "")
    {   
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }
}
