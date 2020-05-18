<?php

use yii\db\Migration;

/**
 * Class m200515_063620_add_shop_product_description_field
 */
class m200515_063620_add_shop_product_description_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%shop_products}}', 'description', $this->text()->after('name'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%shop_products}}', 'description');
    }
}
