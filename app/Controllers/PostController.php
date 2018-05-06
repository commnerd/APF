<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Post;

class PostController extends BaseController
{
    /**
     * Creation page for a new post
     *
     * @return Response
     */
    public function create()
    {
        return $this->view('posts/create.html');
    }

    /**
     * Store a new Post
     *
     * @param  Request $request The request and its attributes
     * @return Response
     */
    public function store(Request $request)
    {
        Post::create($request->toArray());

        return $this->redirect('back');
    }

    /**
     * Show page for editing a post
     *
     * @param  integer $id The ID of the comment to edit
     * @return Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);

        return $this->view('posts/show.html', array(
            'post' => $post
        ));
    }

    /**
     * Edit a post
     *
     * @param  integer $id The ID of the post to edit
     * @return Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return $this->view('posts/edit.html', array(
            'post' => $post
        ));
    }

    /**
     * Update a post
     *
     * @param  Request $request The request and its attributes
     * @param  integer $id The ID of the post to edit
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $post->update($request->toArray());

        return $this->redirect('back');
    }

    /**
     * Delete a post
     *
     * @param  integer $id The ID of the post to edit
     * @return Response
     */
    public function destroy($id)
    {
        Post::delete($id);

        return $this->redirect('back');
    }
}
