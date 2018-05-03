<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Section;

class SectionController extends BaseController
{
    public function edit(Request $request)
    {
        exit(print_r($request->toArray(), true));
        $section = Section::findOrFail($request->ID);
        exit(print_r($section->toArray(), true));
        return $this->view('welcome.html', array('section' => $section));
    }
}
