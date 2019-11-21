<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface Destination
{
	public function __invoke(Request $request, Response $response): Response;

	public function match(Request $request): bool;
}
