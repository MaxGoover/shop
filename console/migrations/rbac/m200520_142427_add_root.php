<?php

use shop\entities\User\User;
use yii\db\Migration;

/**
 * Class m200520_142427_add_root
 */
class m200520_142427_add_root extends Migration
{
    public function safeUp()
    {
        // Insert: root into users table
        $this->batchInsert(
            'users',
            [
                'id',
                'username',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'email',
                'status',
                'created_at',
                'updated_at',
                'verification_token',
                'phone',
            ],
            [
                [
                    1,
                    'root',
                    'UTNjBf6CD5ALzqGfFlzznfB2pRvZccLx',
                    '$2y$13$3QwBeDWD4aj9m.r69ixZkOgAgYO/UnCY.uqyUGDQ84aC4ZbTJiMoS',
                    null,
                    'root@gmail.ru',
                    10,
                    1538038342,
                    1538038342,
                    null,
                    '8005553535'
                ],
            ]
        );

        // Insert: root into auth_item
        $this->batchInsert(
            'auth_items',
            [
                'name',
                'type',
                'description',
                'rule_name',
                'data',
                'created_at',
                'updated_at',
            ],
            [
                // Add root role with access to everything
                ['/*', 2, null, null, null, 1538039242, 1538039242],
                ['full_access', 2, 'Full super administrator access', null, null, 1538039321, 1538039321],
                ['root', 1, 'Super administrator', null, null, 1538039368, 1538039368],
            ]
        );

        // Insert: root into auth_item_child
        $this->batchInsert(
            'auth_item_children',
            [
                'parent',
                'child',
            ],
            [
                ['full_access', '/*'],
                ['root', 'full_access'],
            ]
        );

        // Insert: root auth_assignment
        $this->batchInsert(
            'auth_assignments',
            [
                'item_name',
                'user_id',
                'created_at',
            ],
            [
                // Root role binding to root user
                ['root', 1, '1538039392'],
            ]
        );
    }

    public function safeDown()
    {

    }
}
