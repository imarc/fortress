<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;

/**
 *
 */
class Director
{
	/**
	 *
	 */
	public function __construct(ResponseFactory $factory, Director ...$directors)
	{
		$this->directors = $directors;
		$this->factory   = $factory;
	}


	/**
	 *
	 */
	public function redirect(Request $request): Response
	{
		foreach ($this->directors as $director) {
			if ($director->match($request)) {
				return $director($request, $this->factory->createResponse());
			}
		}

		return $this->factory
			->createResponse()
			->withStatus(301)
			->withHeader('Location', (string) $request->getUri())
		;
	}
}
