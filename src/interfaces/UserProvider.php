<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface UserProvider
{
	public function getName();
	public function initialize(Request $request): Response;
	public function resolve($token);
	public function setGateway(Gateway $gateway): UserProvider;
}
