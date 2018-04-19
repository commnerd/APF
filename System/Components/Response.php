<?php

namespace System\Components;

class Response extends AppComponent
{
	private $_templateSystem;

    public function render()
    {
        return $this->app->_templateSystem->render()
    }
}

?>
