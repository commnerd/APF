<?php

namespace System\Components;

use System\Components\Templating\Driver;

class TemplateSystem extends AppComponent
{
	private $_system;

	private $_driver;

	public function __construct()
	{
		parent::__construct();

		$this->_system = $this->app->config->get('templating.system');

		$this->_driver = new Driver($this->_system);
	}

	public function render($template, $params)
	{
		return $this->_driver->render($template, $params);
	}
}
