<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'transport' => $mailerAuth,
    'messageConfig' => [
        'charset' => 'UTF-8',
        'from' => ['info@apatris.pl' => 'Bait'],
    ],
    'useFileTransport' => false,
];