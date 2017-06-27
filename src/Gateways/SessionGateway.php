<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class SessionGateway extends Gateway
{
	const PROVIDER_KEY = __CLASS__ . '::provider';
	const TOKEN_KEY    = __CLASS__ . '::token';
	const ID_KEY       = __CLASS__ . '::id';


	/**
	 *
	 */
	protected function getFromSession($key)
	{
		return isset($_SESSION[$key])
			? $_SESSION[$key]
			: NULL;
	}


	/**
	 *
	 */
	protected function load($request)
	{
		$this->provider = $this->getFromSession(static::PROVIDER_KEY);
		$this->token    = $this->getFromSession(static::TOKEN_KEY);
		$this->id       = $this->getFromSession(static::ID_KEY);
	}


	/**
	 *
	 */
	protected function save($response)
	{
		$_SESSION[static::PROVIDER_KEY] = $this->provider;
		$_SESSION[static::TOKEN_KEY]    = $this->token;
		$_SESSION[static::ID_KEY]       = $this->id;

		return $response;
	}
}
