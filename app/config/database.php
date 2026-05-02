<?php
// THEMORA SHOP — Database Configuration
$isLocal = env('APP_ENV', 'production') === 'development';

return [
    'driver'   => $isLocal ? 'mysql' : env('DB_DRIVER', 'pgsql'),
    'host'     => env('DB_HOST', $isLocal ? 'localhost' : 'db.ckmbfggrjocszdwrgaib.supabase.co'),
    'port'     => env('DB_PORT', $isLocal ? '3306' : '5432'),
    'database' => env('DB_NAME', $isLocal ? 'themora_shop' : 'postgres'),
    'username' => env('DB_USER', $isLocal ? 'root' : 'postgres'),
    'password' => env('DB_PASS', ''),
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'prefix'   => '',
];
