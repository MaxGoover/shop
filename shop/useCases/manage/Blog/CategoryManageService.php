<?php

namespace shop\useCases\manage\Blog;

use shop\entities\Meta;
use shop\entities\Blog\Category;
use shop\forms\manage\Blog\CategoryForm;
use shop\repositories\Blog\CategoryRepository;
use shop\repositories\Blog\PostRepository;

class CategoryManageService
{
    private $_categories;
    private $_posts;

    public function __construct(CategoryRepository $categories, PostRepository $posts)
    {
        $this->_categories = $categories;
        $this->_posts = $posts;
    }

    public function create(CategoryForm $form): Category
    {
        $category = Category::create(
            $form->name,
            $form->slug,
            $form->title,
            $form->description,
            $form->sort,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $this->_categories->save($category);
        return $category;
    }

    public function edit($id, CategoryForm $form): void
    {
        $category = $this->_categories->get($id);
        $category->edit(
            $form->name,
            $form->slug,
            $form->title,
            $form->description,
            $form->sort,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $this->_categories->save($category);
    }

    public function remove($id): void
    {
        $category = $this->_categories->get($id);
        if ($this->_posts->existsByCategory($category->id)) {
            throw new \DomainException('Unable to remove category with posts.');
        }
        $this->_categories->remove($category);
    }
}