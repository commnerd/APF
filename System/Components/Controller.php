<?php

namespace System\Components;

class Controller extends AppComponent
{
	public function __construct()
	{
		parent::__construct();

	}

	public function view($template, $params = null)
	{
		return new Response($template, $params);
	}
}
