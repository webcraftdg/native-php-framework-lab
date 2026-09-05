<?php

$config = [
    'sourceLanguage' => 'fr',
    'language' => 'fr',
    'timezone' => 'Europe/Paris',
    'connection' => [
        'dsn' => getstrenv('DB_DRIVER').':host=' . getstrenv('DB_HOST') . ';port=' . getstrenv('DB_PORT') . ';dbname=' . getstrenv('DB_DATABASE').';charset='.getstrenv('DB_CHARSET', 'utf8mb4'),
        'username' => getstrenv('DB_USER'),
        'password' => getstrenv('DB_PASSWORD'),
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    'request' => [
        'requestKey' => getenv('REQUEST_KEY'),
    ],
    'assetManager' => [

    ],
    'urlManager' => [
        'baseUrl' => getenv('BASE_URL'),
        'prefix' => getenv('URL_PREFIX'),
        'rules' => [
            [
                'pattern' => 'contacts/<id:(\d+)>/test/<truc:(\w+)>',
                'target' => 'default/view',
            ],
            [
                'pattern' => 'contacts/<id:(\d+)>/edit',
                'target' => 'default/update',
            ],
            [
                'pattern' => 'contacts/liste',
                'target' => 'default/default',
            ],
            [
                'pattern' => 'contacts/ajouter',
                'target' => 'default/create',
            ],
        ],
    ],
];

return $config;
