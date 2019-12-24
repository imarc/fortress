<?php

namespace Fortress;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 */
class SessionGateway extends Gateway
{
	const TOKEN_KEY    = __CLASS__ . '::token';
	const ID_KEY       = __CLASS__ . '::id';


	/**
	 *
	 */
	public function load(Request $request): Gateway
	{
		$this->request = $request;
		$this->token   = $this->getFromSession(static::TOKEN_KEY);
		$this->id      = $this->getFromSession(static::ID_KEY);

		return $this;
	}


	/**
	 *
	 */
	public function save(Response $response = NULL): ?Response
	{
		$_SESSION[static::TOKEN_KEY] = $this->token;
		$_SESSION[static::ID_KEY]    = $this->id;

		if ($response && $response->getStatusCode() == 200) {
			return $this->director->redirect($this, $this->request);
		}

		return $response;
	}


	/**
	 *
	 */
	protected function getFromSession($key)
	{
		return isset($_SESSION[$key])
			? $_SESSION[$key]
			: NULL;
	}
}
