<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Section;

class SectionController extends BaseController
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

    public function destroy(string $ID)
    {
        Section::delete($ID);
        return $this->redirect('back');
    }
}
