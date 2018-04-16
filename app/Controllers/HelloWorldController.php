<?php

namespace App\Controllers;

class HelloWorldController extends BaseController
{
    public function index(Request $request)
    {
        $target = "world";
        if(is_string($request->target)) {
            $target = $request->target;
        }
        return $this->view('welcome');
    }
}
