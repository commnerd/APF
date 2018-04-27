<?php

namespace System\Components\Templating;

use System\Components\AppComponent;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;
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
				// 'cache' => $config->get('templating.cache.path'),
			));
			$this->registerTwigFunctions();
		}
	}

	public function render(string $template, array $params)
	{
		if($params == null) {
			$params = array();
		}
		return $this->_system->render($template, $params);
	}

	private function registerTwigFunctions()
	{
		$app = $this->app;

		$functions = array(
			new Twig_SimpleFunction('route', function(string $name, array $params) use ($app) {
				$route = $app->router->generate($name, $params);
				return $route;
			})
		);

		foreach($functions as $function) {
			$this->_system->addFunction($function);
		}
	}
}
