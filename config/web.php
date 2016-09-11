<?php

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
            'languages' => ['ru-RU', 'pl-PL']
        ],
        'request' => [
            'cookieValidationKey' => 'sZBlR-a5gAcapFmShtkR-Sa7sfQoXPft',
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
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'class' => 'pheme\i18n\I18nUrlManager',
            'languages' => ['English' => 'en', 'Русский' => 'ru', 'Polska' => 'pl'],
            'aliases' => ['en' => 'en-US', 'ru' => 'ru-RU', 'pl' => 'pl-PL'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<action:(invited-tree)>' => 'site/<action>',
                '<controller:(proposals)>/<action:(send|successfully)>/<hash>' => '<controller>/<action>',
                '<controller:(proposals)>/<action:(delete|activate)>/<id>' => '<controller>/<action>',
            ],
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableConfirmation' => false,
            'enableRegistration' => false,
            //'enableGeneratingPassword' => true,
            'rememberFor' => 2419200,
            'admins' => ['krava', 'admin']
        ],
        'rbac' => [
            'class' => 'dektrium\rbac\RbacWebModule',
        ],
        'i18n' => Zelenin\yii\modules\I18n\Module::className()
    ],
    'sourceLanguage' => 'en-US',
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['178.43.11.222']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['83.31.209.67']
    ];
}

return $config;
