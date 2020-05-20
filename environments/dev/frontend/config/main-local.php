<?php

$config = [
    'components' => [
        'authClientCollection' => [
            'class' => \yii\authclient\Collection::class,
            'clients' => [
                'vk' => [
                    'class' => \yii\authclient\clients\VKontakte::class,
                    'clientId' => '',
                    'clientSecret' => '',
                ],
            ],
        ]
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'panels' => [
            'queue' => \yii\queue\debug\Panel::class,
        ],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
    ];
}

return $config;
