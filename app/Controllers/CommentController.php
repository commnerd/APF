<?php

namespace App\Controllers;

use System\Components\Request;
use App\Models\Comment;

class CommentController extends BaseController
{
    /**
     * Store a new Comment
     *
     * @param  Request $request The request and its attributes
     * @param  integer $postId  The ID associated with the parent post
     * @return Response
     */
    public function store(Request $request, $postId)
    {
        $post = $request->toArray();
        $post['POST_ID'] = $postId;
        Comment::create($post);

        return $this->redirect('home');
    }

    /**
     * Show page for editing a comment
     *
     * @param  integer $id The ID of the comment to edit
     * @return Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $comment->fill($request->toArray());

        return $this->redirect('home');
    }

    /**
     * Delete a comment
     *
     * @param  integer $id The ID of the comment to delete
     * @return Response
     */
    public function destroy($id)
    {
        Comment::delete($id);

        return $this->redirect('home');
    }
}
