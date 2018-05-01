<?php

namespace App\Controllers;

class WelcomeController extends BaseController
{
    public function index()
    {
        $this->view('welcome.html');
    }
}
