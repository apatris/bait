<?php

return [
    'user' => [
        'class' => 'dektrium\user\Module',
        'admins' => ['admin'],
        'enableConfirmation' => false,
        'enableRegistration' => true,
        //'enableGeneratingPassword' => true,
        'rememberFor' => 2419200,
    ],
    'rbac' => [
        'class' => 'dektrium\rbac\RbacWebModule',
    ],
    'i18n' => [
        'class' => Zelenin\yii\modules\I18n\Module::className(),
    ]
];