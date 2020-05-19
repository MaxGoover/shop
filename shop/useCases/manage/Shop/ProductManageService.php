<?php

namespace shop\useCases\manage\Shop;

use shop\entities\Meta;
use shop\entities\Shop\Product\Product;
use shop\entities\Shop\Tag;
use shop\forms\manage\Shop\Product\ModificationForm;
use shop\forms\manage\Shop\Product\PhotosForm;
use shop\forms\manage\Shop\Product\PriceForm;
use shop\forms\manage\Shop\Product\ProductCreateForm;
use shop\forms\manage\Shop\Product\ProductEditForm;
use shop\forms\manage\Shop\Product\QuantityForm;
use shop\repositories\Shop\BrandRepository;
use shop\repositories\Shop\CategoryRepository;
use shop\repositories\Shop\ProductRepository;
use shop\repositories\Shop\TagRepository;
use shop\services\TransactionManager;

class ProductManageService
{
    private $_products;
    private $_brands;
    private $_categories;
    private $_tags;
    private $_transaction;

    public function __construct(
        ProductRepository $products,
        BrandRepository $brands,
        CategoryRepository $categories,
        TagRepository $tags,
        TransactionManager $transaction
    )
    {
        $this->_products = $products;
        $this->_brands = $brands;
        $this->_categories = $categories;
        $this->_tags = $tags;
        $this->_transaction = $transaction;
    }

    public function activate($id): void
    {
        $product = $this->_products->get($id);
        $product->activate();
        $this->_products->save($product);
    }

    public function addModification($id, ModificationForm $form): void
    {
        $product = $this->_products->get($id);
        $product->addModification(
            $form->code,
            $form->name,
            $form->price,
            $form->quantity
        );
        $this->_products->save($product);
    }

    public function addPhotos($id, PhotosForm $form): void
    {
        $product = $this->_products->get($id);
        foreach ($form->files as $file) {
            $product->addPhoto($file);
        }
        $this->_products->save($product);
    }

    public function addRelatedProduct($id, $otherId): void
    {
        $product = $this->_products->get($id);
        $other = $this->_products->get($otherId);
        $product->assignRelatedProduct($other->id);
        $this->_products->save($product);
    }

    public function changePrice($id, PriceForm $form): void
    {
        $product = $this->_products->get($id);
        $product->setPrice($form->new, $form->old);
        $this->_products->save($product);
    }

    public function changeQuantity($id, QuantityForm $form): void
    {
        $product = $this->_products->get($id);
        $product->changeQuantity($form->quantity);
        $this->_products->save($product);
    }

    public function create(ProductCreateForm $form): Product
    {
        $brand = $this->_brands->get($form->brandId);
        $category = $this->_categories->get($form->categories->main);

        $product = Product::create(
            $brand->id,
            $category->id,
            $form->code,
            $form->name,
            $form->description,
            $form->weight,
            $form->quantity->quantity,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        $product->setPrice($form->price->new, $form->price->old);

        foreach ($form->categories->others as $otherId) {
            $category = $this->_categories->get($otherId);
            $product->assignCategory($category->id);
        }

        foreach ($form->values as $value) {
            $product->setValue($value->id, $value->value);
        }

        foreach ($form->photos->files as $file) {
            $product->addPhoto($file);
        }

        foreach ($form->tags->existing as $tagId) {
            $tag = $this->_tags->get($tagId);
            $product->assignTag($tag->id);
        }

        $this->_transaction->wrap(function () use ($product, $form) {
            foreach ($form->tags->newNames as $tagName) {
                if (!$tag = $this->_tags->findByName($tagName)) {
                    $tag = Tag::create($tagName, $tagName);
                    $this->_tags->save($tag);
                }
                $product->assignTag($tag->id);
            }
            $this->_products->save($product);
        });

        return $product;
    }

    public function draft($id): void
    {
        $product = $this->_products->get($id);
        $product->draft();
        $this->_products->save($product);
    }

    public function edit($id, ProductEditForm $form): void
    {
        $product = $this->_products->get($id);
        $brand = $this->_brands->get($form->brandId);
        $category = $this->_categories->get($form->categories->main);

        $product->edit(
            $brand->id,
            $form->code,
            $form->name,
            $form->description,
            $form->weight,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        $product->changeMainCategory($category->id);

        $this->_transaction->wrap(function () use ($product, $form) {

            $product->revokeCategories();
            $product->revokeTags();
            $this->_products->save($product);

            foreach ($form->categories->others as $otherId) {
                $category = $this->_categories->get($otherId);
                $product->assignCategory($category->id);
            }

            foreach ($form->values as $value) {
                $product->setValue($value->id, $value->value);
            }

            foreach ($form->tags->existing as $tagId) {
                $tag = $this->_tags->get($tagId);
                $product->assignTag($tag->id);
            }
            foreach ($form->tags->newNames as $tagName) {
                if (!$tag = $this->_tags->findByName($tagName)) {
                    $tag = Tag::create($tagName, $tagName);
                    $this->_tags->save($tag);
                }
                $product->assignTag($tag->id);
            }
            $this->_products->save($product);
        });
    }

    public function editModification($id, $modificationId, ModificationForm $form): void
    {
        $product = $this->_products->get($id);
        $product->editModification(
            $modificationId,
            $form->code,
            $form->name,
            $form->price,
            $form->quantity
        );
        $this->_products->save($product);
    }

    public function movePhotoUp($id, $photoId): void
    {
        $product = $this->_products->get($id);
        $product->movePhotoUp($photoId);
        $this->_products->save($product);
    }

    public function movePhotoDown($id, $photoId): void
    {
        $product = $this->_products->get($id);
        $product->movePhotoDown($photoId);
        $this->_products->save($product);
    }

    public function remove($id): void
    {
        $product = $this->_products->get($id);
        $this->_products->remove($product);
    }

    public function removeModification($id, $modificationId): void
    {
        $product = $this->_products->get($id);
        $product->removeModification($modificationId);
        $this->_products->save($product);
    }

    public function removePhoto($id, $photoId): void
    {
        $product = $this->_products->get($id);
        $product->removePhoto($photoId);
        $this->_products->save($product);
    }

    public function removeRelatedProduct($id, $otherId): void
    {
        $product = $this->_products->get($id);
        $other = $this->_products->get($otherId);
        $product->revokeRelatedProduct($other->id);
        $this->_products->save($product);
    }
}