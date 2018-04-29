<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Section;

class EntryController extends BaseController
{
    public function index(Request $request)
    {
        return $this->view('welcome.html', array(
            'sections' => Section::all()
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