<?php

namespace Core;

class EventDispatcher
{

	private $listeners = array();

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][] = $listener;
    }

	public function dispatch($eventName, Event $event)
	{
		if (isset($this->listeners[$eventName])) {
	        foreach ($this->listeners[$eventName] as $listener) {
	            call_user_func($listener, $event, $eventName);
	        }
        }
	} 

}