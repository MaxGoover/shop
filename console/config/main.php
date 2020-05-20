<?php

$params = \array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => \dirname(__DIR__),
    'bootstrap' => [
        'log',
        'common\bootstrap\SetUp',
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
        'migrate-mysql'      => [
            'class'          => \yii\console\controllers\MigrateController::class,
            'migrationPath'  => [
                '@app/migrations/mysql',
            ],
            'migrationTable' => 'migration',
        ],
        'migrate-rbac'       => [
            'class'          => \yii\console\controllers\MigrateController::class,
            'migrationPath'  => [
                '@yii/rbac/migrations',
                '@app/migrations/rbac',
            ],
            'migrationTable' => 'migration_rbac',
        ],
// todo Разобраться почему пакет fishvision не находит миграции RBAC
//        'migrate-rbac' => [
//            'class' => \fishvision\migrate\controllers\MigrateController::class,
//            'autoDiscover' => true,
//            'migrationPaths' => [
//                '@app/migrations/rbac',
//                '@vendor/yiisoft/yii2/rbac/migrations',
//            ],
//        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'backendUrlManager' => require __DIR__ . '/../../backend/config/urlManager.php',
        'frontendUrlManager' => require __DIR__ . '/../../frontend/config/urlManager.php',
    ],
    'params' => $params,
];
