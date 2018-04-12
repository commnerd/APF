<?php

namespace System\Interfaces;

/**
 * Required by config reader to read in configs
 */
interface ConfigConsumer
{
	/**
	 * Set the configurations
	 * 
	 * @param array $configs The configs read in from the ConfigReader 
	 */
	public function setConfigs($configs);

	/**
	 * Required by reader to get the associated config path
	 * 
	 * @return string the path to the associated config directory
	 */
	public function getConfigPath();
}