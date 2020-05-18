<?php

use yii\db\Migration;

/**
 * Class m200513_120958_rename_user_table
 */
class m200513_120958_rename_user_table extends Migration
{
    public function safeUp()
    {
        $this->renameTable('{{%user}}', '{{%users}}');
    }

    public function safeDown()
    {
        $this->renameTable('{{%users}}', '{{%user}}');
    }
}
