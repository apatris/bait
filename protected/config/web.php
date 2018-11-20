<?php

$db = require __DIR__ . '/inc/db.php';
$urlManager = require __DIR__ . '/inc/urlManager.php';
$mailerAuth = require __DIR__ . '/inc/mailerAuth.php';
$mailer = require __DIR__ . '/inc/mailer.php';
$modules = require __DIR__ . '/inc/modules.php';
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name' => 'Bait',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'authManager' => [
            'class' => 'dektrium\rbac\components\DbManager',
        ],
        'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::className(),
            'languages' => ['en' => 'en-US', 'ru-RU', 'pl-PL']
        ],
        'request' => [
            'cookieValidationKey' => 'sZBlR-a5gAcapFmShtkR-Sa1sfQoXPft',
            'baseUrl' => ''
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => $mailer,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => $urlManager,
    ],
    'modules' => $modules,
    'sourceLanguage' => 'en-US',
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        //'allowedIPs' => ['176.109.244.133']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        //'allowedIPs' => ['176.109.244.133']
    ];
}

return $config;
