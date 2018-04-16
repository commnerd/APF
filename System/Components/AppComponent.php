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
     *
     * @param \System\App $app The context to bind  
	 */
	public function __construct($passedApp = null) {
        GLOBAL $app;

        $this->app = $app;

        if(isset($passedApp)) {
            $this->app = $passedApp;
        }
    }
}
