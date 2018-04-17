<?php

namespace System\Components;

use AltoRouter;

class Router extends AltoRouter
{

	protected $app;

	public function __construct(\System\App $app = null) {
		$this->app = $app;
	}

	public function match($requestUrl = null, $requestMethod = null) {
		$requestUrl = isset($requestUrl) ? $requestUrl : $_SERVER['REQUEST_URI'];
		return parent::match($requestUrl, $requestMethod);
	}

}
