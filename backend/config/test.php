<?php

use yii\helpers\ReplaceArrayValue;

return [
    'id' => 'app-backend-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
        ],
        'session' => [
            'cookieParams' => new ReplaceArrayValue(['httpOnly' => true]),
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
