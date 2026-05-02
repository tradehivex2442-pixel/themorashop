<?php
// THEMORA SHOP — Database Configuration
return [
    'driver'   => env('DB_DRIVER', 'pgsql'),
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', '5432'),
    'database' => env('DB_NAME', 'postgres'),
    'username' => env('DB_USER', 'postgres'),
    'password' => env('DB_PASS', ''),
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'prefix'   => '',
];
