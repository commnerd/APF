<?php

namespace System;

use System\Interfaces\App as AppInterface;
use System\Interfaces\ConfigConsumer;
use System\Components\ConfigReader;

/**
 * The overarching system object
 */
class App implements AppInterface, ConfigConsumer
{
	/**
	 * Application's various paths
	 * 
	 * @var string
	 */
	private $_paths;

	/**
	 * Component mapping for the app
	 * 
	 * @var array
	 */
	private $_componentMap = array();

	/**
	 * Configuration mapping for the app
	 * 
	 * @var array
	 */
	private $_configMap = array();

	/**
	 * Getter for managed components
	 * 
	 * @param  string $name The namespace and class name you want retrieved
	 * @return mixed        The associated component
	 */
	public function __get($name)
	{
		$this->_paths = array(
			'base' => $this->getBaseDir(),
			'app' => $this->getAppPath(),
			'config' => $this->getConfigPath(),
		);

		if(!isset($this->componentMap[$name])) {
			$this->componentMap[get_class($name)] = new $name();
		}

		return $this->componentMap[get_class($name)];
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
	 * Get path by title lookup
	 * 
	 * @param  string $title The title/context of the directory
	 * @return string        The corresponding path
	 */
	public function getDirPath($title)
	{
		return $this->_directories[$title];
	}

	/**
	 * Set path by title
	 * 
	 * @param  string $title The title/context of the directory
	 * @param  string $path  The path for the associated title
	 * @return void
	 */
	public function setDirPath($title, $path)
	{
		$this->_directories[$title] = $path;
	}

	/**
	 * Get the application's base directory
	 * 
	 * @return string The base directory for the application
	 */
	public function getAppPath()
	{
		return $this->getBaseDir().DIRECTORY_SEPARATOR."app";
	}

	/**
	 * Required by Config to get the associated config path
	 * --Implemented to conform to the ConfigConsumer interface
	 * 
	 * @return string the path to the associated config directory
	 */
	public function getConfigPath() {
		return $this->getBaseDir().DIRECTORY_SEPARATOR."config";
	}

	/**
	 * Required by Config to get the associated config path
	 * --Set the configurations
	 * 
	 * @param array $configs The configs read in from the ConfigReader 
	 */
	public function setConfigs($configs) {
		$this->_configMap = $configs;
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
		$this->_loadConfig();
		$this->_loadRoutes();
		$this->_runMiddlewares();
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
	private function _loadConfig()
	{
		ConfigReader::read($this);
	}

	/**
	 * Pull in configs
	 * 
	 * @return void
	 */
	private function _loadRoutes()
	{
		ConfigReader::read($this);
	}

	/**
	 * Run middlewares
	 * 
	 * @return void
	 */
	private function _runMiddlewares()
	{

	}

}
