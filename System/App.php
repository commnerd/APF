<?php

namespace System;

use System\Interfaces\App as AppInterface;
use System\Services\DirectoryScanner;
use System\Components\AppComponent;
use System\Components\ConfigReader;
use System\Components\Config;
use System\Components\Router;


/**
 * The overarching system object
 */
class App implements AppInterface
{
	/**
	 * Component mapping for the app
	 *
	 * @var array
	 */
	private $_componentMap = array();

	/**
	 * Component alias mappings for userspace use
	 *
	 * @var array
	 */
	private $_componentAliasMap = array();

	/**
	 * Constructor for Application; bootstrap config
	 */
	public function __construct()
	{
		$configClass = "\System\Components\Config";
		$this->_componentAliasMap['config'] = $configClass;
		$this->_componentMap[$configClass] = new $configClass();
	}

	/**
	 * Getter for managed components
	 *
	 * @param  string $name The namespace and class name you want retrieved
	 * @return \System\Components\AppComponent The associated component
	 */
	public function __get($name)
	{
		if(!empty($this->_componentAliasMap[$name])) {
			$name = $this->_componentAliasMap[$name];
		}
		
		if(empty($this->_componentMap[$name])) {
			$this->_componentMap[$name] = new $name();
		}

		return $this->_componentMap[$name];
	}

	/**
	 * Get the application's base directory
	 *
	 * @return string The base directory for the application
	 */
	public function getBaseDir()
	{
		return getcwd().DIRECTORY_SEPARATOR.'..';
	}

	/**
	 * Public accessor to initialize the app
	 *
	 * @return System\Models\App The application context
	 */
	public static function init()
	{
		$app = new App();
		$app->bootstrap();
		return $app;
	}

	/**
	 * Bootstrap system
	 *
	 * @return void
	 */
	public function bootstrap()
	{
		$this->_setupRequest();
		$this->_loadConfigs();
		$this->_loadRoutes();
		$this->_runMiddlewares();
		$route = $this->_getMappedRoute();
		exit("Here: ".print_r($route, true));
		$response = $route->getResponse();
	}

	/**
	 * Returns the contents from a Viewable interface
	 *
	 * @return string
	 */
	public function sendResponse()
	{
		echo "Sending response!";
	}

	/**
	 * Pull in configs
	 *
	 * @return void
	 */
	private function _loadConfigs()
	{
		
		$configReader = new ConfigReader();
		$configReader->readConfigs($this->getBaseDir().DIRECTORY_SEPARATOR."config");
		$this->config->setConfigs($configReader->getConfigs());
	}

	/**
	 * Setup the request for the application
	 * 
	 * @return void
	 */
	private function _setupRequest()
	{
		$this->_mapComponent("request","\System\Components\Request");
	}

	/**
	 * Pull in configs
	 *
	 * @return void
	 */
	private function _loadRoutes()
	{
		$dir = $this->getBaseDir().DIRECTORY_SEPARATOR.$this->config->get('path.routes');
		$files = DirectoryScanner::getFiles($dir);
		$router = new Router($this);
		foreach($files as $file) {
			exec(file_get_contents($file));
		}
		$this->_componentMap['\App\Components\Router'] = $router;
		exit(print_r($this->{'\System\Components\Router'}, true));
	}

	/**
	 * Run middlewares
	 *
	 * @return void
	 */
	private function _runMiddlewares()
	{
		$dir = $this->config->get('path.src.middlewares');
		$files = DirectoryScanner::getFiles($this->getBaseDir().DIRECTORY_SEPARATOR.$dir);
		foreach($files as $file) {
			require_once($file);
		}
	}

	/**
	 * Get the mapped 
	 */
	private function _getMappedRoute()
	{
		return $this->{'\System\Components\Router'}->match(
			$this->request->getUrl(),
			$this->request->getMethod()
		);
	}

	/**
	 * Utility method used to quickly map components
	 * @param  string $name  The intended alias
	 * @param  string $class The fully qualified class name
	 * @return void
	 */
	private function _mapComponent($name, $class)
	{
		$this->_componentAliasMap[$name] = $class;
		$this->_componentMap[$class] = new $class();
	}
}
