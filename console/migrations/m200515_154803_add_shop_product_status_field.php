<?php

use yii\db\Migration;

/**
 * Class m200515_154803_add_shop_product_status_field
 */
class m200515_154803_add_shop_product_status_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%shop_products}}', 'status', $this->smallInteger()->notNull());
        $this->update('{{%shop_products}}', ['status' => 1]);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%shop_products}}', 'status');
    }
}
