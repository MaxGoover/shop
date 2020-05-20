<?php

use yii\helpers\ReplaceArrayValue;

return [
    'id' => 'app-common-tests',
    'basePath' => \dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => \yii\web\User::class,
            'identityClass' => \common\auth\Identity::class,
            'identityCookie' => new ReplaceArrayValue(['name' => '_identity', 'httpOnly' => true]),
        ],
    ],
];
