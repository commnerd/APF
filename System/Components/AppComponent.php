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

    /**
     * If not located in this component, look to the app
     * @param  string $name The variable to retrieve
     * @return mixed        The referenced variable
     */
    public function __get($name) {
        if(isset($this->{$name}) {
            return $this->{$name};
        }
        return $this->app->{$name};
    }

    /**
     * If not located in this component, look to the app
     * @param  string $name The variable to retrieve
     * @return mixed        The corresponding return value
     */
    /*
    public function __call($name, $args) {
        return 
        return $this->app->{$name};
    }
    */
}
