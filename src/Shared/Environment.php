<?php

namespace Testcenter\Testcenter\Shared;

class Environment
{
	private $endPoint;

	public function __construct()
	{
		$this->endPoint = config('testcenter.api_endpoint');
	}

	public function getEndpoint()
	{
		return $this->endPoint;
	}
}
