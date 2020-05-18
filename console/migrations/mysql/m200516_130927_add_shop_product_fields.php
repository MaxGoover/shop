<?php

use yii\db\Migration;

/**
 * Class m200516_130927_add_shop_product_fields
 */
class m200516_130927_add_shop_product_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%shop_products}}', 'weight', $this->integer()->notNull());
        $this->addColumn('{{%shop_products}}', 'quantity', $this->integer()->notNull());

        $this->addColumn('{{%shop_modifications}}', 'quantity', $this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%shop_modifications}}', 'quantity');

        $this->dropColumn('{{%shop_products}}', 'quantity');
        $this->dropColumn('{{%shop_products}}', 'weight');
    }
}
