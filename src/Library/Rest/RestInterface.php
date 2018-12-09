<?php

namespace Library\Rest;

use Library\Util\Logger;

interface RestInterface
{
	public function get(array $parameters) : RestResponse;
//	public function post(array $parameters);
//	public function delete(array $parameters);
}
