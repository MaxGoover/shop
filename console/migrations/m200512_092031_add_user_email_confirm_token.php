<?php

use yii\db\Migration;

/**
 * Class m200512_092031_add_user_email_confirm_token
 */
class m200512_092031_add_user_email_confirm_token extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'email_confirm_token', $this->string()->unique()->after('email'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'email_confirm_token');
    }
}
