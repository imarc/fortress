<?php

namespace Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
abstract class Gateway
{
	/**
	 *
	 */
	protected $id = NULL;


	/**
	 *
	 */
	protected $mappers = array();


	/**
	 *
	 */
	protected $provider = NULL;


	/**
	 *
	 */
	protected $providers = array();


	/**
	 *
	 */
	protected $token = NULL;


	/**
	 *
	 */
	protected $resolver = NULL;


	/**
	 *
	 */
	public function __construct(UserResolver $resolver)
	{
		$this->resolver = $resolver;
	}


	/**
	 *
	 */
	public function getUser()
	{
		return $this->resolver->fetch($this->id);
	}


	/**
	 *
	 */
	public function login(Request $request, Response $response = NULL)
	{
		if (!$this->provider) {
			$this->load($request);

			if (!$this->provider) {
				return $response;
			}
		}

		if (!isset($this->providers[$this->provider])) {
			throw new InvalidProviderException(sprintf(
				'The specified provider "%s" is not available',
				$this->provider
			));
		}

		$provider = $this->providers[$this->provider];

		if (!$this->token) {
			$response = $provider->initialize($request, $response);
		}

		if (!$this->id) {
			$data = $provider->getData($this->token);

			if (isset($this->mappers[$this->provider])) {
				$id = $this->mappers[$this->provider]->map($data);
			} else {
				$id = $data;
			}

			$this->setId($id);
		}

		return $this->save($response);
	}


	/**
	 *
	 */
	public function logout(Request $request, Response $response)
	{
		$this->setId(NULL);
		$this->setToken(NULL);
		$this->setProvider(NULL);

		return $this->save($response);
	}


	/**
	 *
	 */
	public function register(UserProvider $provider, UserMapper $mapper = NULL)
	{
		$provider_name = $provider->getName();

		if (isset($this->providers[$provider_name])) {
			throw new InvalidProviderException(sprintf(
				'Provider with alias %s already registered',
				$alias
			));
		}

		$provider->setGateway($this);

		$this->providers[$provider_name] = $provider;

		if ($mapper) {
			$this->mappers[$provider_name] = $mapper;
		}

		return $this;
	}


	/**
	 *
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}


	/**
	 *
	 */
	public function setProvider($provider)
	{
		$this->provider = $provider;

		return $this;
	}


	/**
	 *
	 */
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	 *
	 */
	abstract protected function load($request);

	/**
	 *
	 */
	abstract protected function save($response);
}
