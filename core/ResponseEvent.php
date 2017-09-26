<?php

namespace Core;

class ResponseEvent extends Event
{
	private $response;
	
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    public function getResponse()
    {
    	return $this->response;
    }
}