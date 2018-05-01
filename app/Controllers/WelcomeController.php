<?php

namespace App\Controllers;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->view('welcome.html');
    }
}
