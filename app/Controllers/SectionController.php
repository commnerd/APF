<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Section;

class SectionController extends BaseController
{
    /**
     * Manage
     * @param  integer  $id  The ID of the resource
     * @return Response      The response
     */
    public function edit($id)
    {
        $section = Section::findOrFail($id);

        return $this->view('section.html', array('section' => $section));
    }
}
