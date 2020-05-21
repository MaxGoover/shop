<?php

use yii\db\Migration;

/**
 * Class m200521_072213_fill_shop_products_data
 */
class m200521_072213_fill_shop_products_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_072213_fill_shop_products_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_072213_fill_shop_products_data cannot be reverted.\n";

        return false;
    }
    */
}
