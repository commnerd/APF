<?php

namespace System\Components;

use \System\Components\Response;
use ErrorException;

class Response extends PackageComponent
{
	const TYPE_TEMPLATE       = "template";
	const TYPE_REDIRECT       = "redirect";

	const ERROR_TYPE_REDIRECT = "The redirect must have an associated route.";

	protected $_code = 200;
	
	protected $_type = "template";

	protected $_template;

	protected $_params;

	public function __construct($params)
	{
		foreach(get_object_vars($this) as $key => $val) {
			$paramKey = substr($key, 1);
			if(isset($params[$paramKey])) {
				$this->{$key} = $params[$paramKey];
				unset($params[$paramKey]);
			}
		}

		$this->_params = $params;

		if($this->_type === self::TYPE_REDIRECT && !isset($params['route'])) {
			throw new ErrorException(self::ERROR_TYPE_REDIRECT);
		}
	}
}

?>
