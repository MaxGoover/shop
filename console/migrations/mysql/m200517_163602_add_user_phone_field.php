<?php

use yii\db\Migration;

/**
 * Class m200517_163602_add_user_phone_field
 */
class m200517_163602_add_user_phone_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'phone', $this->string()->notNull());

        $this->createIndex('{{%idx-users-phone}}', '{{%users}}', 'phone', true);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'phone');
    }
}
