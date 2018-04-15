<?php

namespace System\Components;

abstract class AppComponent
{
    /**
     * Reference to the overarching app
     *
     * @var \System\App
     */
    protected $app;

    /**
	 * Constructor for app components
	 */
	public function __construct() {
        GLOBAL $app;

        $this->app = $app;
    }
}
