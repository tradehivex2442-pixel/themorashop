<?php
require_once __DIR__ . '/../app/bootstrap.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>System Environment Debug</h2>";

echo "Current APP_ENV: <b>" . env('APP_ENV', 'NOT SET (Defaulting to production)') . "</b><br>";
echo "Current DB_DRIVER: <b>" . config('database.driver') . "</b><br>";
echo "Current DB_HOST: <b>" . config('database.host') . "</b><br>";

echo "<h3>PHP Info for DB:</h3>";
echo "PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "<br>";

echo "<h3>Attempting Connection...</h3>";
try {
    $driver = config('database.driver');
    $host = config('database.host');
    $port = config('database.port');
    $db = config('database.database');
    $user = config('database.username');
    $pass = config('database.password');
    
    if ($driver === 'pgsql') {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
    } else {
        $dsn = "mysql:host=$host;port=$port;dbname=$db";
    }
    
    echo "Connecting with DSN: <b>$dsn</b><br>";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "<span style='color:green'>SUCCESS: Connected!</span>";
} catch (\Exception $e) {
    echo "<span style='color:red'>FAILURE: " . $e->getMessage() . "</span>";
}
