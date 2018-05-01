<?php

namespace System\Components;

use System\Components\Templating\Driver;

class TemplateSystem extends AppComponent
{
	/**
	 * The templating system to use in rendering response
	 * 
	 * @var string
	 */
	private $_system;

	/**
	 * The driver to use in rendering responses
	 * 
	 * @var \System\Components\Templating\Driver
	 */
	private $_driver;

	/**
	 * Build the templating system
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_system = $this->app->config->get('templating.system');

		$this->_driver = new Driver($this->_system);
	}

	/**
	 * Render the template
	 * @param  string $template The relative path to the route to render
	 * @param  array  $params   The params to pass to the template
	 * @return string           The HTML/JSON to pass to the client
	 */
	public function render($template, $params)
	{
		return $this->_driver->render($template, $params);
	}
}
