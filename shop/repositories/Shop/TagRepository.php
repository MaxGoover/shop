<?php

namespace shop\repositories\Shop;

use shop\entities\Shop\Tag;
use shop\repositories\NotFoundException;

class TagRepository
{
    public function findByName($name): ?Tag
    {
        return Tag::findOne(['name' => $name]);
    }

    public function get($id): Tag
    {
        if (!$tag = Tag::findOne($id)) {
            throw new NotFoundException('Tag is not found.');
        }
        return $tag;
    }

    public function remove(Tag $tag): void
    {
        if (!$tag->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }

    public function save(Tag $tag): void
    {
        if (!$tag->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }
}