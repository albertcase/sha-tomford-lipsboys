<?php

namespace Core;

use Core\Request;
use Core\Response;
use Core\KernelEvents;

include_once __DIR__ . "/../config/config.php";

class Kernel
{
	private $dispatcher;

	public function __construct()
	{
		$this->dispatcher = new EventDispatcher();
	}

	public function handle(Request $request)
	{

		$this->initialize();

		//request
		$event = new RequestEvent($request);
		$this->dispatcher->dispatch(KernelEvents::REQUEST, $event);

		//resolve router
		$router = new Router($request);

		//resolve controller
		if($router->getCallback()) {
			$event = new ControllerEvent($request);
			$this->dispatcher->dispatch(KernelEvents::CONTROLLER, $event);
			$response = call_user_func_array($router->getCallback(), array($router->getCallbackParam()));
		} else {
			$response = new Response('<center><h1>404 page not found!</h1></center>');
		}

		//response
		$event = new ResponseEvent($request, $response);
		$this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);
		$response->send();

	}

	public function initialize()
	{
		if(WECHAT_CAMPAIGN)
			$this->dispatcher->addListener('kernel.request', array(new \WechatBundle\EventListener(), 'initUser'));
		else
			$this->dispatcher->addListener('kernel.request', array(new \CampaignBundle\EventListener(), 'initUser'));
		$this->dispatcher->addListener('kernel.request', array(new \CampaignBundle\EventListener(), 'extendUser'));
		//$this->dispatcher->addListener('kernel.request', new CampaignBundle\Listerner());
	}
}
