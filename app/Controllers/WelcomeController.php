<?php

namespace App\Controllers;

use App\Models\Section;

class WelcomeController extends BaseController
{
    public function index()
    {
        $sections = Section::all();

        return $this->view('welcome.html', array('sections' => $sections));
    }
}
