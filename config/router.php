<?php

$routers = array();
//System
$routers['/wechat/callback'] = array('WechatBundle\Wechat', 'callback');
$routers['/wechat/curio/callback'] = array('WechatBundle\Coach', 'callback');
$routers['/wechat/same/callback'] = array('WechatBundle\Same', 'callback');
$routers['/wechat/curio/receive'] = array('WechatBundle\Coach', 'receiveUserInfo');
$routers['/wechat/jssdk/config/js'] = array('WechatBundle\Wechat', 'jssdkConfigJs');
$routers['/simulation/login'] = array('WechatBundle\Wechat', 'simulationLogin');
$routers['/clear'] = array('CampaignBundle\Page', 'clearCookie');
//System end

//Campaign
$routers['/'] = array('CampaignBundle\Page', 'index');
$routers['/consume'] = array('CampaignBundle\Page', 'prove');
$routers['/qrcode'] = array('CampaignBundle\Page', 'qrcode');
$routers['/api/consume'] = array('CampaignBundle\Page', 'consume');

//API
$routers['/api/phonecode'] = array('CampaignBundle\Api', 'phoneCode');
$routers['/api/checkphonecode'] = array('CampaignBundle\Api', 'checkPhoneCode');
$routers['/api/apply'] = array('CampaignBundle\Api', 'apply');
$routers['/api/applylist'] = array('CampaignBundle\Api', 'applyList');
