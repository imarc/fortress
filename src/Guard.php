<?php

namespace Fortress;

use InvalidArgumentException;

/**
 *
 */
class Guard
{
	/**
	 *
	 */
	protected $acceptRules = array();


	/**
	 *
	 */
	protected $auth = NULL;


	/**
	 *
	 */
	protected $granted = FALSE;


	/**
	 *
	 */
	protected $rejectRules = array();


	/**
	 *
	 */
	public function check($request_path, $user_roles)
	{
		if ($this->defaultRule == 'accept') {
			$this->granted = TRUE;

			$this->processRejectRules($request_path, $user_roles);
			$this->processAcceptRules($request_path, $user_roles);

		} else {
			$this->granted = FALSE;

			$this->processAcceptRules($request_path, $user_roles);
			$this->processRejectRules($request_path, $user_roles);
		}

		return $this->granted;
	}


	/**
	 *
	 */
	public function setDefaultRule($rule)
	{
		if (!in_array($rule, ['accept', 'reject'])) {
			throw new InvalidArgumentException(sprintf(
				'Default rule must be one of "accept" or "reject"'
			));
		}

		$this->defaultRule = $rule;

		return $this;
	}


	/**
	 *
	 */
	public function setAcceptRules(array $rules)
	{
		$this->acceptRules = $rules;

		return $this;
	}


	/**
	 *
	 */
	public function setRejectRules(array $rules)
	{
		$this->rejectRules = $rules;

		return $this;
	}


	/**
	 *
	 */
	protected function processAcceptRules($request_path, $user_roles)
	{
		foreach ($this->acceptRules as $path => $roles) {
			if (!preg_match('#^' . $path. '$#i', $request_path, $matches)) {
				continue;
			}

			if (in_array('*', $roles) || array_intersect($roles, $user_roles)) {
				$this->granted = TRUE;
				return;
			}
		}
	}


	/**
	 *
	 */
	protected function processRejectRules($request_path, $user_roles)
	{
		foreach ($this->rejectRules as $path => $roles) {
			if (!preg_match('#^' . $path. '$#i', $request_path, $matches)) {
				continue;
			}

			if (in_array('*', $roles) || array_intersect($roles, $user_roles)) {
				$this->granted = FALSE;
				return;
			}
		}
	}
}
