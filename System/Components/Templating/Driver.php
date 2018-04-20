<?php

namespace System\Components\Templating;

use System\Components\AppComponent;
use Twig_Loader_Filesystem;
use Twig_Environment;

class Driver extends AppComponent
{
	private $_system;

	public function __construct($system)
	{
		parent::__construct();
		switch(strtolower($system)) {
			case 'twig':
			default:
			$config = $this->app->config;
			$loader = new Twig_Loader_Filesystem($config->get('templating.paths'));
			$this->_system = new Twig_Environment($loader, array(
				'cache' => $config->get('templating.cache.path'),
			));
		}
	}

	public function render($template, $params)
	{
		if($params == null) {
			$params = array();
		}
		return $this->_system->render($template, $params);
	}
}
