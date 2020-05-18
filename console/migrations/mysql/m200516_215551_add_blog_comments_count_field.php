<?php

use yii\db\Migration;

/**
 * Class m200516_215551_add_blog_comments_count_field
 */
class m200516_215551_add_blog_comments_count_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%blog_posts}}', 'comments_count', $this->integer()->notNull());

        $this->update('{{%blog_posts}}', ['comments_count' => 0]);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%blog_posts}}', 'comments_count');
    }
}
