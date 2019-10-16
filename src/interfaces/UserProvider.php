<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface UserProvider
{
	/**
	 * Get the name for the provider
	 *
	 * @return string The name of the provider
	 */
	public function getName(): string;


	/**
	 * Initialize the user provider and return a response.
	 *
	 * The initialization function should look for information in the request
	 * upon which it can set the token `$this->gateway->setToken()`.  Some
	 * providers may return different responses based on the request.  For
	 * example, an oauth style provider may look for a code parameter.  If
	 * a code is not provided, it may return a redirect to the oauth server
	 * for login containing client_id and such.
	 */
	public function initialize(Request $request): Response;


	/**
	 * Resolve a user from a token.
	 *
	 * Note: If there are cases where `initialize()` does not throw an
	 * exception, you will almost invariably want to handle the possibility of
	 * a `NULL` token.  In this case, you can safely return `NULL`.
	 *
	 * @return mixed A user ID or user data to be mapped, NULL if invalid.
	 */
	public function resolve($token);


	/**
	 *
	 */
	public function setGateway(Gateway $gateway): UserProvider;
}
