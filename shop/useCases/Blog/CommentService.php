<?php

namespace shop\useCases\Blog;

use shop\entities\Blog\Post\Comment;
use shop\forms\Blog\CommentForm;
use shop\repositories\Blog\PostRepository;
use shop\repositories\UserRepository;

class CommentService
{
    private $_posts;
    private $_users;

    public function __construct(PostRepository $posts, UserRepository $users)
    {
        $this->_posts = $posts;
        $this->_users = $users;
    }

    public function create($postId, $userId, CommentForm $form): Comment
    {
        $post = $this->_posts->get($postId);
        $user = $this->_users->get($userId);

        $comment = $post->addComment($user->id, $form->parentId, $form->text);

        $this->_posts->save($post);

        return $comment;
    }
}