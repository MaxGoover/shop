<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => ['site/login', 'site/error'],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'common\bootstrap\SetUp',
        'log',
    ],
    'components' => [
        'backendUrlManager' => require __DIR__ . '/urlManager.php',
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'frontendUrlManager' => require __DIR__ . '/../../frontend/config/urlManager.php',
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => $params['cookieValidationKey'],
            'csrfParam' => '_csrf-backend',
        ],
        'session' => [
            'name' => '_session',
            'cookieParams' => [
                'domain' => $params['cookieDomain'],
                'httpOnly' => true,
            ],
        ],
        'urlManager' => function () {
            return Yii::$app->get('backendUrlManager');
        },
        'user' => [
            'identityClass' => 'common\entities\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
            ],
        ],
    ],
    'controllerNamespace' => 'backend\controllers',
    'id' => 'app-backend',
    'modules' => [],
    'params' => $params,
];
