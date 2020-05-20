<?php

namespace shop\useCases\manage\Shop;

use shop\forms\manage\Shop\Product\ReviewEditForm;
use shop\repositories\Shop\ProductRepository;

class ReviewManageService
{
    private $_products;

    public function __construct(ProductRepository $products)
    {
        $this->_products = $products;
    }

    public function activate($id, $reviewId): void
    {
        $product = $this->_products->get($id);
        $product->activateReview($reviewId);
        $this->_products->save($product);
    }

    public function draft($id, $reviewId): void
    {
        $product = $this->_products->get($id);
        $product->draftReview($reviewId);
        $this->_products->save($product);
    }

    public function edit($id, $reviewId, ReviewEditForm $form): void
    {
        $product = $this->_products->get($id);
        $product->editReview(
            $reviewId,
            $form->vote,
            $form->text
        );
        $this->_products->save($product);
    }

    public function remove($id, $reviewId): void
    {
        $product = $this->_products->get($id);
        $product->removeReview($reviewId);
        $this->_products->save($product);
    }
}