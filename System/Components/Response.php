<?php

namespace System\Components;

use \System\Components\Response;

class Response extends AppComponent
{
	private $_template;

	private $_data;

	public function __construct($template, $data)
	{
		$this->_template = $template;
		$this->_data = $data;
	}

	public function __get($name) {
		if(empty($this->{"_".$name})) {
			return null;
		}
		return $this->{"_".$name};
	}
}

?>
