<?php

namespace CampaignBundle;

use Core\Event;
use Lib\PDO;
use Lib\UserAPI;
use Lib\Helper;

class EventListener
{
	public function initUser(Event $event)
	{
		global $user;
		$this->request = $event->getRequest();
	}

	public function extendUser(Event $event)
	{

	}

	public function getUserInfo($uid)
	{

	}
}