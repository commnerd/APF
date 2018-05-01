<?php

namespace System\Components;

class Controller extends AppComponent
{
	private $_router;

	public function __construct()
	{
		parent::__construct();

		$this->_router = $this->app->router;
	}

	public function view($template, $params = array())
	{
		$params[Response::TYPE_TEMPLATE] = $template;

		return new Response($params);
	}

	public function redirect($name, $params = array())
	{
		$router = $this->_router;
		$route = "";
		if(preg_match('/^(prev|back$)/', $name)) {
			$route = $_SERVER['HTTP_REFERER'];
		}
		else {
			$route = $router->generate($name, $params);
		}

		return new Response(array(
			'type' => Response::TYPE_REDIRECT,
			'route' => $route,
			'code' => 302,
		));
	}
}
