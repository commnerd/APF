<?php

namespace System\Components;

use AltoRouter;

class Router extends AltoRouter
{

	protected $app;

	public function __construct(\System\App $app = null) {
		$this->app = $app;
	}

}
