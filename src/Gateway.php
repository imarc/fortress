<?php

namespace Fortress;

use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
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
	protected $response_factory = NULL;


	/**
	 *
	 */
	abstract protected function load(Request $request): Gateway;

	/**
	 *
	 */
	abstract protected function save(Response $response): Response;


	/**
	 *
	 */
	public function __construct(ResponseFactory $response_factory, UserResolver $resolver)
	{
		$this->resolver        = $resolver;
		$this->responseFactory = $response_factory;
	}


	/**
	 *
	 */
	public function getUser(Request $request)
	{
		$this->load($request);

		return $this->resolver->fetch($this->id);
	}


	/**
	 *
	 */
	public function login(string $provider, Request $request): Response
	{
		if (!isset($this->providers[$provider])) {
			throw new InvalidProviderException(sprintf(
				'The specified provider "%s" is not available',
				$this->provider
			));
		}

		$provider = $this->providers[$provider];
		$response = $this->responseFactory->createResponse();

		if (!$this->token) {
			$response = $provider->initialize($request, $response);
		}

		if (!$this->id) {
			$data = $provider->resolve($this->token);

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
	public function logout(Request $request): Response
	{
		$this->setId(NULL);
		$this->setToken(NULL);
		$this->setProvider(NULL);

		return $this->save($this->responseFactory->createResponse());
	}


	/**
	 *
	 */
	public function register(UserProvider $provider, UserMapper $mapper = NULL): Gateway
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
	public function setId($id): Gateway
	{
		$this->id = $id;

		return $this;
	}


	/**
	 *
	 */
	public function setProvider($provider): Gateway
	{
		$this->provider = $provider;

		return $this;
	}


	/**
	 *
	 */
	public function setToken($token): Gateway
	{
		$this->token = $token;

		return $this;
	}
}
