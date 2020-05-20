<?php

namespace shop\cart\storage;

use shop\cart\CartItem;
use shop\entities\Shop\Product\Product;
use yii\db\Connection;
use yii\db\Query;

class DbStorage implements StorageInterface
{
    private $_userId;
    private $_db;

    public function __construct($userId, Connection $db)
    {
        $this->_userId = $userId;
        $this->_db = $db;
    }

    public function load(): array
    {
        $rows = (new Query())
            ->select('*')
            ->from('{{%shop_cart_items}}')
            ->where(['user_id' => $this->_userId])
            ->orderBy(['product_id' => SORT_ASC, 'modification_id' => SORT_ASC])
            ->all($this->_db);

        return array_map(function (array $row) {
            /** @var Product $product */
            if ($product = Product::find()->active()->andWhere(['id' => $row['product_id']])->one()) {
                return new CartItem($product, $row['modification_id'], $row['quantity']);
            }
            return false;
        }, $rows);
    }

    public function save(array $items): void
    {
        $this->_db->createCommand()->delete('{{%shop_cart_items}}', [
            'user_id' => $this->_userId,
        ])->execute();

        $this->_db->createCommand()->batchInsert(
            '{{%shop_cart_items}}',
            [
                'user_id',
                'product_id',
                'modification_id',
                'quantity'
            ],
            \array_map(function (CartItem $item) {
                return [
                    'user_id' => $this->_userId,
                    'product_id' => $item->getProductId(),
                    'modification_id' => $item->getModificationId(),
                    'quantity' => $item->getQuantity(),
                ];
            }, $items)
        )->execute();
    }
} 