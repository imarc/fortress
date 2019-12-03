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
	public function getId()
	{
		return $this->id;
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
	public function getToken()
	{
		return $this->token;
	}

	/**
	 *
	 */
	public function login(string $provider, Request $request): Response
	{
		$this->load($request);

		if (!isset($this->providers[$provider])) {
			throw new InvalidProviderException(sprintf(
				'The specified provider "%s" is not available',
				$this->provider
			));
		}

		$provider = $this->providers[$provider];
		$response = $provider->initialize($request);

		if (!$this->id) {
			$data = $provider->resolve($this->token);

			if (isset($this->mappers[$provider->getName()])) {
				$id = $this->mappers[$provider->getName()]->map($data);
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
		$this->load($request);
		$this->setId(NULL);
		$this->setToken(NULL);

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
	public function setToken($token): Gateway
	{
		$this->token = $token;

		return $this;
	}
}
