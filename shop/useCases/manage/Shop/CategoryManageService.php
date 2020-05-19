<?php

namespace shop\useCases\manage\Shop;

use shop\entities\Meta;
use shop\entities\Shop\Category;
use shop\forms\manage\Shop\CategoryForm;
use shop\repositories\Shop\CategoryRepository;
use shop\repositories\Shop\ProductRepository;

class CategoryManageService
{
    private $_categories;
    private $_products;

    public function __construct(CategoryRepository $categories, ProductRepository $products)
    {
        $this->_categories = $categories;
        $this->_products = $products;
    }

    public function create(CategoryForm $form): Category
    {
        $parent = $this->_categories->get($form->parentId);
        $category = Category::create(
            $form->name,
            $form->slug,
            $form->title,
            $form->description,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $category->appendTo($parent);
        $this->_categories->save($category);
        return $category;
    }

    public function edit($id, CategoryForm $form): void
    {
        $category = $this->_categories->get($id);
        $this->_assertIsNotRoot($category);
        $category->edit(
            $form->name,
            $form->slug,
            $form->title,
            $form->description,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        if ($form->parentId !== $category->parent->id) {
            $parent = $this->_categories->get($form->parentId);
            $category->appendTo($parent);
        }
        $this->_categories->save($category);
    }

    public function moveUp($id): void
    {
        $category = $this->_categories->get($id);
        $this->_assertIsNotRoot($category);
        if ($prev = $category->prev) {
            $category->insertBefore($prev);
        }
        $this->_categories->save($category);
    }

    public function moveDown($id): void
    {
        $category = $this->_categories->get($id);
        $this->_assertIsNotRoot($category);
        if ($next = $category->next) {
            $category->insertAfter($next);
        }
        $this->_categories->save($category);
    }

    public function remove($id): void
    {
        $category = $this->_categories->get($id);
        $this->_assertIsNotRoot($category);
        if ($this->_products->existsByMainCategory($category->id)) {
            throw new \DomainException('Unable to remove category with products.');
        }
        $this->_categories->remove($category);
    }

    private function _assertIsNotRoot(Category $category): void
    {
        if ($category->isRoot()) {
            throw new \DomainException('Unable to manage the root category.');
        }
    }
}