<?php

namespace Fortress;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;

/**
 *
 */
class Director
{
	/**
	 *
	 */
	public function __construct(ResponseFactory $factory, Destination ...$destinations)
	{
		$this->destinations = $destinations;
		$this->factory      = $factory;
	}


	/**
	 *
	 */
	public function redirect(Gateway $gateway, Request $request): Response
	{
		foreach ($this->destinations as $destination) {
			if ($destination->match($request)) {
				return $destination($gateway, $request, $this->factory->createResponse());
			}
		}

		return $this->factory
			->createResponse()
			->withStatus(303)
			->withHeader('Location', (string) $request->getUri())
		;
	}
}
