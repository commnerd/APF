<?php

namespace System\Components;

use System\Interfaces\ConfigConsumer;
use System\Services\DirectoryScanner;

/**
 * Configuration reader utility
 */
class ConfigReader
{
	/**
	 * Error to throw when path not set.
	 */
	const EXCEPTION_NULL_PATH = "Path not set, cannot read configs.";

	/**
	 * Configs read in
	 *
	 * @var array
	 */
	private $_configs = array();

	/**
	 * Target to read configs to
	 *
	 * @var System\Interfaces\ConfigConsumer
	 */
	private $_target;

	/**
	 * Static function to read in configs
	 *
	 * @param System\Interfaces\ConfigConsumer $target The system to read the configs to
	 * @return array                                   Associative array of variables and values
	 */
	public static function read(ConfigConsumer $target)
	{
		$reader = new ConfigReader($target);
		$reader->readConfigs();
		$configs = $reader->getConfigs();
		$target->setConfigs($configs);
	}

	/**
	 * Constructor for the config reader
	 *
	 * @param System\Interfaces\ConfigConsumer $target The system to read the configs to
	 */
	public function __construct(ConfigConsumer $target = null) {
		if(!empty($target)) {
			$this->_target = $target;
		}
	}

	/**
	 * Read in the configs
	 *
	 * @param  string $dir The path to read configs from
	 * @return void
	 */
	public function readConfigs($dir = null)
	{
		if(!empty($this->_target)) {
			$dir = $this->_target->getConfigPath();
		}
		// exit("<pre>".print_r($dir, true)."</pre>");
		if(empty($dir)) {
			throw ErrorException(self::EXCEPTION_NULL_PATH);
		}
		$files = DirectoryScanner::getFiles($dir);
		foreach($files as $file) {
			$extension = end(explode(".", $file));
			if($extension === 'ini') {
				array_merge($this->_configs, parse_ini_file($file));
			}
		}
	}

	/**
	 * Get the read-in configs
	 *
	 * @return array  The read-in configs
	 */
	public function getConfigs()
	{
		return $_configs;
	}

}
