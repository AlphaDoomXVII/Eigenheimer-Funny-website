<?php

return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'localhost',
        'port'     => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'eigenheimer',
        'username' => getenv('DB_USERNAME') ?: 'timo',
        'password' => getenv('DB_PASSWORD') ?: 'timo',
    ],
];
