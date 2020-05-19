<?php

namespace shop\services\yandex;

use shop\entities\Shop\DeliveryMethod;
use shop\readModels\Shop\CategoryReadRepository;
use shop\readModels\Shop\DeliveryMethodReadRepository;
use shop\readModels\Shop\ProductReadRepository;
use yii\helpers\Html;

class YandexMarket
{
    private $_shop;
    private $_categories;
    private $_products;
    private $_deliveryMethods;

    public function __construct(
        ShopInfo $shop,
        CategoryReadRepository $categories,
        ProductReadRepository $products,
        DeliveryMethodReadRepository $deliveryMethods
    )
    {
        $this->_shop = $shop;
        $this->_categories = $categories;
        $this->_products = $products;
        $this->_deliveryMethods = $deliveryMethods;
    }

    public function generate(callable $productUrlGenerator): string
    {
        \ob_start();

        $writer = new \XMLWriter();
        $writer->openURI('php://output');

        $writer->startDocument('1.0', 'UTF-8');
        $writer->startDTD('yml_catalog SYSTEM "shops.dtd"');
        $writer->endDTD();

        $writer->startElement('yml_catalog');
        $writer->writeAttribute('date', date('Y-m-d H:i'));

        $writer->startElement('shop');
        $writer->writeElement('name', Html::encode($this->_shop->name));
        $writer->writeElement('company', Html::encode($this->_shop->company));
        $writer->writeElement('url', Html::encode($this->_shop->url));

        $writer->startElement('currencies');

        $writer->startElement('currency');
        $writer->writeAttribute('id', 'RUR');
        $writer->writeAttribute('rate', 1);
        $writer->endElement();

        $writer->endElement();

        $writer->startElement('categories');

        foreach ($this->_categories->getAll() as $category) {
            $writer->startElement('category');
            $writer->writeAttribute('id', $category->id);
            if (($parent = $category->parent) && !$parent->isRoot()) {
                $writer->writeAttribute('parentId', $parent->id);
            }
            $writer->writeRaw(Html::encode($category->name));
            $writer->endElement();
        }

        $writer->endElement();

        $writer->startElement('offers');

        $deliveries = $this->_deliveryMethods->getAll();

        foreach ($this->_products->getAllIterator() as $product) {
            $writer->startElement('offer');

            $writer->writeAttribute('id', $product->id);
            $writer->writeAttribute('type', 'vendor.model');
            $writer->writeAttribute('available', $product->isAvailable() ? 'true' : 'false');

            $writer->writeElement('url', Html::encode($productUrlGenerator($product)));
            $writer->writeElement('price', $product->price_new);
            $writer->writeElement('currencyId', 'RUR');
            $writer->writeElement('categoryId', $product->category_id);

            $available = \array_filter($deliveries, function (DeliveryMethod $method) use ($product) {
                return $method->isAvailableForWeight($product->weight);
            });

            if ($available) {
                $writer->writeElement('delivery', 'true');
                $writer->writeElement('local_delivery_cost', \max(\array_map(function (DeliveryMethod $method) {
                    return $method->cost;
                }, $available)));
            } else {
                $writer->writeElement('delivery', 'false');
            }

            $writer->writeElement('vendor', Html::encode($product->brand->name));
            $writer->writeElement('model', Html::encode($product->code));
            $writer->writeElement('description', Html::encode(\strip_tags($product->description)));

            foreach ($product->values as $value) {
                if (!empty($value->value)) {
                    $writer->startElement('param');
                    $writer->writeAttribute('name', $value->characteristic->name);
                    $writer->text($value->value);
                    $writer->endElement();
                }
            }

            $writer->endElement();
        }

        $writer->endElement();

        $writer->fullEndElement();
        $writer->fullEndElement();

        $writer->endDocument();

        return \ob_get_clean();
    }
}