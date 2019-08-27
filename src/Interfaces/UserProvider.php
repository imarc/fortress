<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface UserProvider
{
	public function getData($token);
	public function getName();
	public function initialize(Request $request, Response $response = NULL);
	public function setGateway(Gateway $gateway);
}
