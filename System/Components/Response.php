<?php

namespace System\Components;

use \System\Components\Response;

class Response extends AppComponent
{
	private $_template;

	private $_params;

	public function __construct($template, $params)
	{
		$this->_template = $template;
		$this->_params = $params;
	}

	public function __get($name) {
		if(empty($this->{"_".$name})) {
			return null;
		}
		return $this->{"_".$name};
	}
}

?>
