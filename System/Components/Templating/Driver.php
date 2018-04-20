<?php

namespace System\Components\Templating;

class Driver
{
	private $_system;

	public function __construct($system)
	{
		switch(strtolower($system)) {
			case 'twig':
			default:
				$this->_system = new \Twig_Environment();
		}
	}

	public function render($template, $params = array())
	{
		return $this->_system->render($template, $params);
	}
}
