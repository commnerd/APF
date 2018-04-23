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

	public function view($template, $params = null)
	{
		$params[Response::TYPE_TEMPLATE] = $template;

		return new Response($params);
	}

	public function redirect($name, $params = array())
	{
		$router = $this->_router;

		return new Response(array(
			'type' => Response::TYPE_REDIRECT,
			'route' => $router->generate($name, $params),
			'code' => 302,
		));
	}
}
