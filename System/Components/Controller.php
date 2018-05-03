<?php

namespace System\Components;

use Traversable;

class Controller extends AppComponent
{
	/**
	 * Reference to app's router for internal use
	 *
	 * @var \System\Components\Router
	 */
	private $_router;

	/**
	 * Construct Controller instance
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_router = $this->app->router;
	}

	/**
	 * Return view for use in System\App
	 *
	 * @param  string                       $template The relative path of the template to be displayed
	 * @param  iterable                     $params   The array of parameters to pass to the template
	 * @return System\Components\Response             The response to be returned to the client
	 */
	public function view($template, iterable $params = array())
	{
		$params[Response::TYPE_TEMPLATE] = $template;

		return new Response($params);
	}

	/**
	 * Return view for use in System\App
	 *
	 * @param  string                       $name     The redirected route name for the client
	 * @param  iterable                     $params   Params for the redirect
	 * @return System\Components\Response             The response to be returned to the client
	 */
	public function redirect($name, iterable $params = array())
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
