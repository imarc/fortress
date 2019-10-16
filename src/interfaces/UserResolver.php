<?php

namespace Fortress;

interface UserResolver
{
	/**
	 * Fetch the user based on their ID
	 *
	 * If the user is not logged in the `$id` will be `NULL`
	 *
	 * @param mixed $id The ID as provided by the gateway
	 * @return mixed A user object or whatever your system considers a user
	 */
	public function fetch($id);
}
