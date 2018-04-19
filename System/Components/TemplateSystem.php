<?php

namespace System\Components;

use System\Components\Templating\Driver;

class TemplateSystem extends AppComponent
{
	private $_system;

	protected $driver;

	public function __construct()
	{
		$this->_system = $this->app->config->get('templating.system');

		$this->driver = new Driver($this->_system);
	}

	public function render($args)
	{
		$this->_system->render()
	}
}