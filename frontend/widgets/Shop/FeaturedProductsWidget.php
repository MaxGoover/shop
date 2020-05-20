<?php

namespace frontend\widgets\Shop;

use shop\readModels\Shop\ProductReadRepository;
use yii\base\Widget;

class FeaturedProductsWidget extends Widget
{
    public $limit;

    private $_repository;

    public function __construct(ProductReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->_repository = $repository;
    }

    public function run()
    {
        return $this->render('featured', [
            'products' => $this->_repository->getFeatured($this->limit)
        ]);
    }
}