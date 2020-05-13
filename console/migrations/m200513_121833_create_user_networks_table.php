<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_networks}}`.
 */
class m200513_121833_create_user_networks_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%user_networks}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'identity' => $this->string()->notNull(),
            'network' => $this->string(16)->notNull(),
        ], $tableOptions);

        $this->createIndex('{{%idx-user_networks-identity-name}}', '{{%user_networks}}', ['identity', 'network'], true);

        $this->createIndex('{{%idx-user_networks-user_id}}', '{{%user_networks}}', 'user_id');

        $this->addForeignKey('{{%fk-user_networks-user_id}}', '{{%user_networks}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_networks}}');
    }
}
