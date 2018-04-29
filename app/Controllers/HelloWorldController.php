<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Section;

class HelloWorldController extends BaseController
{
    public function index(Request $request)
    {
        $sections = Section::with('ENTRIES')->all();
        return $this->view('welcome.html', array(
            'sections' => $sections
        ));
    }

    public function store(Request $request)
    {
        $section = new Section();

        $section->fill($request->toArray());

        $section->save();

        return $this->redirect('home');
    }
}
