<?php

namespace App\Controllers;

use App\Models\Post;

class WelcomeController extends BaseController
{
    public function index()
    {
        $posts = Post::with('COMMENTS')->all();
        
        return $this->view('welcome.html', array(
            'posts' => $posts
        ));
    }
}
