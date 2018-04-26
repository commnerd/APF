<?php

namespace System\Components;

use AltoRouter;

class Router extends AltoRouter
{
	const METHOD_RESOURCE = "RESOURCE";

	protected $app;

	private $_controllerMethods = array(
		"index" => "GET",
		"create" => "GET",
		"store" => "POST",
		"edit" => "GET",
		"update" => "PUT",
		"delete" => "DELETE",
	);

	public function __construct(\System\App $app = null)
	{
		$this->app = $app;
	}

	public function match($requestUrl = null, $requestMethod = null)
	{
		$requestUrl = isset($requestUrl) ? $requestUrl : $_SERVER['REQUEST_URI'];
		return parent::match($requestUrl, $requestMethod);
	}

	public function addRoutes(array $routes)
	{
		$resourceRoutes = array();
		foreach($routes as $index => $route) {
			$methods = get_class_methods($route[2]);
			if($route[0] === self::METHOD_RESOURCE) {
				$resourceRoutes[] = $index;
				$breakout = array();
				foreach($methods as $method) {
					if(isset($this->$_controllerMethods[$method])) {
						$path = ($route[1][0] === "/") ? $route[1] : "/".$route[1];
						if($method === "create") {
							$path .= "/create";
						}
						if(in_array($method, array("edit", "create", "update"))) {
							$path .= "/{".$route[1]."}";
						}
						if($method === "edit") {
							$path .= "/edit";
						}
						parent::addRoutes(array(
							$this->$_controllerMethods[$method],
							$path,
							$class."#".$method,
							$route[1].".".$method
						));
					}
				}
			}
		}
		for($i = sizeof($resourceRoutes); $i >= 0; $i--) {
			array_splice($routes, $resourceRoutes[$i], 1);
		}
		parent::addRoutes($routes);
	}

}
