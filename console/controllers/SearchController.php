<?php

namespace console\controllers;

use shop\entities\Shop\Product\Product;
use shop\services\search\ProductIndexer;
use yii\console\Controller;

class SearchController extends Controller
{
    private $_productIndexer;

    public function __construct(
        $id,
        $module,
        ProductIndexer $productIndexer,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_productIndexer = $productIndexer;
    }

    public function actionReindex(): void
    {
        $query = Product::find()
            ->active()
            ->with(['category', 'categoryAssignments', 'tagAssignments', 'values'])
            ->orderBy('id');

        $this->stdout('Clearing' . PHP_EOL);

        $this->_productIndexer->clear();

        $this->stdout('Indexing of products' . PHP_EOL);

        foreach ($query->each() as $product) {
            /** @var Product $product */
            $this->stdout('Product #' . $product->id . PHP_EOL);
            $this->_productIndexer->index($product);
        }

        $this->stdout('Done!' . PHP_EOL);
    }
}