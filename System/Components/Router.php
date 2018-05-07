<?php

namespace System\Components;

use System\Services\TextTransforms;
use AltoRouter;

class Router extends AltoRouter
{
	/**
	 * Resource label for splitting into routes
	 */
	const METHOD_RESOURCE = "RESOURCE";

	/**
	 * The app context for local or related class usage
	 *
	 * @var \System\App
	 */
	protected $app;

	/**
	 * The methods to use with "RESOURCE" routes
	 * @var array
	 */
	private $_controllerMethods = array(
		"index" => "GET",
		"create" => "GET",
		"store" => "POST",
		"edit" => "GET",
		"update" => "PUT",
		"destroy" => "DELETE",
	);

	/**
	 * Construct the router
	 *
	 * @param \System\App|null $app The app reference
	 */
	public function __construct(\System\App $app = null)
	{
		$this->app = $app;
	}

	/**
	 * Match a route
	 * @param  string $requestUrl    The URL being requested
	 * @param  string $requestMethod The method being requested
	 * @return array                 Route declaration
	 */
	public function match($requestUrl = null, $requestMethod = null)
	{
		$requestUrl = isset($requestUrl) ? $requestUrl : $_SERVER['REQUEST_URI'];
		return parent::match($requestUrl, $requestMethod);
	}

	/**
	 * Add routes to the system
	 *
	 * @param array $routes Array of routes to register
	 */
	public function addRoutes($routes)
	{
		$resourceRoutes = array();
		foreach($routes as $index => $route) {
			$methods = get_class_methods($route[2]);
			if($route[0] === self::METHOD_RESOURCE) {
				$resourceRoutes[] = $index;
				$breakout = array();
				if(!empty($methods)) {
					foreach($methods as $method) {
						if(isset($this->_controllerMethods[$method])) {
							$singlizedName = TextTransforms::pluralToSingle($route[1]);
							$path = ($route[1][0] === "/") ? $route[1] : "/".$route[1];
							if($method === "create") {
								$path .= "/create";
							}
							if(in_array($method, array("edit", "update", "destroy"))) {
								$path .= "/[i:ID]";
							}
							if($method === "edit") {
								$path .= "/edit";
							}
							parent::addRoutes(array(array(
								$this->_controllerMethods[$method],
								$path,
								$route[2]."#".$method,
								$route[1].".".$method
							)));
						}
					}
				}
			}
		}
		if(!empty($resourceRoutes)) {
			for($i = sizeof($resourceRoutes) - 1; $i >= 0; $i--) {
				array_splice($routes, $resourceRoutes[$i], 1);
			}
		}
		parent::addRoutes($routes);
	}

	public function generate($routeName, array $params = array())
	{
		$route = parent::generate($routeName, $params);

		$routeDef = $this->namedRoutes[$routeName];
		if(preg_match('/\[.*?:(.*?)\]/', $routeDef, $matches)) {
			array_shift($matches);
			foreach($matches as $key) {
				unset($params[$key]);
			}
		}
		if(!empty($params)) {
			$pairs = array();
			foreach($params as $key => $val) {
				$pairs[] = htmlentities("$key=$val");
			}
			$route .= "?".implode("&", $pairs);
		}
		return $route;
	}
}
