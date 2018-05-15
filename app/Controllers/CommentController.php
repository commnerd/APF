<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Comment;

class CommentController extends BaseController
{
    /**
     * Generate page for creating a new post
     *
     * @return Response
     */
    public function create()
    {
        return $this->view('comments/create.html');
    }

    /**
     * Store a new Comment
     *
     * @param  Request $request The request and its attributes
     * @return Response
     */
    public function store(Request $request)
    {
        Comment::create($request->toArray());

        return $this->redirect('back');
    }

    /**
     * Show page for editing a comment
     *
     * @param  integer $id The ID of the comment to edit
     * @return Response
     */
    public function edit($postId, $id)
    {
        $comment = Comment::findOrFail($id);

        return $this->view('comments/edit.html', array(
            'comment' => $comment
        ));
    }

    /**
     * Update a comment
     *
     * @param  Request $request The request and its attributes
     * @param  integer $id The ID of the comment to edit
     * @return Response
     */
    public function update(Request $request, $postId, $id)
    {
        $comment = Comment::findOrFail($id);

        $comment->fill($request->toArray());

        return $this->redirect('back');
    }

    /**
     * Delete a comment
     *
     * @param  integer $id The ID of the comment to delete
     * @return Response
     */
    public function destroy($postId, $id)
    {
        Comment::delete($id);

        return $this->redirect('back');
    }
}
