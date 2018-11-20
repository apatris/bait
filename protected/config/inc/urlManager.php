<?php

return [
    'class' => 'app\components\managers\I18nUrlManager',
    'languages' => ['English' => 'en', 'Русский' => 'ru', 'Polska' => 'pl'],
    'aliases' => ['en' => 'en-US', 'ru' => 'ru-RU', 'pl' => 'pl-PL'],
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '<action:(user-profile)>/<id:\w+>' => 'site/<action>',
        '<controller:(proposals)>/<action:(send|successfully)>/<hash>' => '<controller>/<action>',
        '<controller:(proposals)>/<action:(delete|activate)>/<id>' => '<controller>/<action>',
        '<controller:(profile)>/<id>' => '<controller>/index',
    ],
];