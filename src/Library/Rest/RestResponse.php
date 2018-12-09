<?php

namespace Library\Rest;

use Library\Util\Logger;

class RestResponse
{
	const STATUS_OK = 200;
	const STATUS_BAD_REQUEST = 400;

	private $code;
	private $data;

	public function setCode(int $code) {
		$this->code = $code;
	}

	public function setData(?array $data) {
		$this->data = $data;
	}

	public function getCode() : int {
		return $this->code;
	}

	public function getData() : ?array {
		return $this->data;
	}
}	
