<?php

/** @var array $params */

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'hostInfo' => $params['frontendHostInfo'],
    'rules' => [
        '' => 'site/index',
        '<_a:about|contact|signup|login|logout>' => 'site/<_a>',

        '<_c:[\w\-]+>' => '<_c>/index',
        '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
        '<_c:[\w\-]+>/<_a:[\w-]+>' => '<_c>/<_a>',
        '<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => '<_c>/<_a>',
    ],
    'showScriptName' => false,
];