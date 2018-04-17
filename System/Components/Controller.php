<?php

namespace System\Components;

class Controller extends AppComponent
{
    public function view($view, $params = null)
    {
    	return $this->app->template->view;
    }

    public function json($contents, $params)
    {
        return $this->app->template->view;
    }
}
