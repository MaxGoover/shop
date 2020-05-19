<?php

namespace shop\useCases\manage\Blog;

use shop\entities\Blog\Tag;
use shop\forms\manage\Blog\TagForm;
use shop\repositories\Blog\TagRepository;

class TagManageService
{
    private $_tags;

    public function __construct(TagRepository $tags)
    {
        $this->_tags = $tags;
    }

    public function create(TagForm $form): Tag
    {
        $tag = Tag::create(
            $form->name,
            $form->slug
        );
        $this->_tags->save($tag);
        return $tag;
    }

    public function edit($id, TagForm $form): void
    {
        $tag = $this->_tags->get($id);
        $tag->edit(
            $form->name,
            $form->slug
        );
        $this->_tags->save($tag);
    }

    public function remove($id): void
    {
        $tag = $this->_tags->get($id);
        $this->_tags->remove($tag);
    }
}