<?php

namespace shop\useCases\manage\Blog;

use shop\forms\manage\Blog\Post\CommentEditForm;
use shop\repositories\Blog\PostRepository;

class CommentManageService
{
    private $_posts;

    public function __construct(PostRepository $posts)
    {
        $this->_posts = $posts;
    }

    public function activate($postId, $id): void
    {
        $post = $this->_posts->get($postId);
        $post->activateComment($id);
        $this->_posts->save($post);
    }

    public function edit($postId, $id, CommentEditForm $form): void
    {
        $post = $this->_posts->get($postId);
        $post->editComment($id, $form->parentId, $form->text);
        $this->_posts->save($post);
    }

    public function remove($postId, $id): void
    {
        $post = $this->_posts->get($postId);
        $post->removeComment($id);
        $this->_posts->save($post);
    }
}