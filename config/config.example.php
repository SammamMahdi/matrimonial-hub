<?php

/**
 * Copy this file to config.php and adjust for your machine.
 * config.php is git-ignored so your credentials never reach the repository.
 */

return [
    'app' => [
        'name'  => 'Matrimonial Hub',
        'env'   => 'local',
        // Show stack traces in the browser. Never enable this on a public server.
        'debug' => true,
        // Absolute URL base. Leave '' to auto-detect from the request.
        'url'   => '',
    ],

    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'matrimonial',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'session' => [
        'name'     => 'matrimonial_session',
        // Send the cookie over HTTPS only. Turn this on in production.
        'secure'   => false,
        'lifetime' => 60 * 60 * 24 * 7,
    ],

    'uploads' => [
        // Where profile photos are written, relative to the project root.
        'dir'        => 'public/uploads',
        'max_bytes'  => 4 * 1024 * 1024,
        'mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
    ],
];
