<?php

namespace App\Controllers;

use App\Models\Section;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->view('welcome.html');
    }
}
