<?php
// THEMORA SHOP — Database Configuration (InfinityFree / Standard PHP)
$isLocal = env('APP_ENV', 'production') === 'development';

return [
    'driver'   => 'mysql',
    'host'     => env('DB_HOST', $isLocal ? 'localhost' : 'sqlXXX.infinityfree.com'),
    'port'     => env('DB_PORT', '3306'),
    'database' => env('DB_NAME', $isLocal ? 'themora_shop' : 'your_db_name'),
    'username' => env('DB_USER', $isLocal ? 'root' : 'your_db_user'),
    'password' => env('DB_PASS', ''),
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'prefix'   => '',
];
