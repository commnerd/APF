<?php

namespace System\Components;

use \System\Components\Response;

class Response extends AppComponent
{
	private $_template;

	private $_data;

	public function __construct($template, $data = array())
	{
		$this->_template = $template;
		$this->_data = $data;
	}

	public function __get($name) {
		return $this->{"_".$name};
	}
}

?>
