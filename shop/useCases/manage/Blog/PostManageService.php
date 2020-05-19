<?php

namespace shop\useCases\manage\Blog;

use shop\entities\Blog\Post\Post;
use shop\entities\Blog\Tag;
use shop\entities\Meta;
use shop\forms\manage\Blog\Post\PostForm;
use shop\repositories\Blog\CategoryRepository;
use shop\repositories\Blog\PostRepository;
use shop\repositories\Blog\TagRepository;
use shop\services\TransactionManager;

class PostManageService
{
    private $_posts;
    private $_categories;
    private $_tags;
    private $_transaction;

    public function __construct(
        PostRepository $posts,
        CategoryRepository $categories,
        TagRepository $tags,
        TransactionManager $transaction
    )
    {
        $this->_posts = $posts;
        $this->_categories = $categories;
        $this->_tags = $tags;
        $this->_transaction = $transaction;
    }

    public function activate($id): void
    {
        $post = $this->_posts->get($id);
        $post->activate();
        $this->_posts->save($post);
    }

    public function create(PostForm $form): Post
    {
        $category = $this->_categories->get($form->categoryId);

        $post = Post::create(
            $category->id,
            $form->title,
            $form->description,
            $form->content,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        if ($form->photo) {
            $post->setPhoto($form->photo);
        }

        foreach ($form->tags->existing as $tagId) {
            $tag = $this->_tags->get($tagId);
            $post->assignTag($tag->id);
        }

        $this->_transaction->wrap(function () use ($post, $form) {
            foreach ($form->tags->newNames as $tagName) {
                if (!$tag = $this->_tags->findByName($tagName)) {
                    $tag = Tag::create($tagName, $tagName);
                    $this->_tags->save($tag);
                }
                $post->assignTag($tag->id);
            }
            $this->_posts->save($post);
        });

        return $post;
    }

    public function draft($id): void
    {
        $post = $this->_posts->get($id);
        $post->draft();
        $this->_posts->save($post);
    }

    public function edit($id, PostForm $form): void
    {
        $post = $this->_posts->get($id);
        $category = $this->_categories->get($form->categoryId);

        $post->edit(
            $category->id,
            $form->title,
            $form->description,
            $form->content,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        if ($form->photo) {
            $post->setPhoto($form->photo);
        }

        $this->_transaction->wrap(function () use ($post, $form) {

            $post->revokeTags();
            $this->_posts->save($post);

            foreach ($form->tags->existing as $tagId) {
                $tag = $this->_tags->get($tagId);
                $post->assignTag($tag->id);
            }
            foreach ($form->tags->newNames as $tagName) {
                if (!$tag = $this->_tags->findByName($tagName)) {
                    $tag = Tag::create($tagName, $tagName);
                    $this->_tags->save($tag);
                }
                $post->assignTag($tag->id);
            }
            $this->_posts->save($post);
        });
    }

    public function remove($id): void
    {
        $post = $this->_posts->get($id);
        $this->_posts->remove($post);
    }
}