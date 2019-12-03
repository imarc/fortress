<?php

namespace Fortress;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface Destination
{
	public function __invoke(Request $request, Response $response): Response;

	public function match(Request $request): bool;
}
