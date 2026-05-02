<?php
require_once __DIR__ . '/../app/bootstrap.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Supabase / Postgres Connection Debug</h2>";

// Check if pdo_pgsql is installed
echo "PDO Drivers installed: <b>" . implode(', ', \PDO::getAvailableDrivers()) . "</b><br>";
if (!in_array('pgsql', \PDO::getAvailableDrivers())) {
    echo "<span style='color:red'>ERROR: pdo_pgsql extension is MISSING on this server!</span><br>";
}

// Check Variables directly from different sources
$driver = config('database.driver');
$host   = config('database.host');
$user   = config('database.username');
$pass   = config('database.password');
$db     = config('database.database');

echo "<h3>Config Values:</h3>";
echo "Driver: <b>$driver</b><br>";
echo "Host: <b>$host</b><br>";
echo "User: <b>$user</b><br>";
echo "DB: <b>$db</b><br>";

if ($host === 'localhost' || $host === 'CHECK_IF_CODE_UPDATED') {
    echo "<span style='color:orange'>WARNING: Still using default host. Environment variables are NOT being read from Vercel Dashboard.</span><br>";
}

echo "<h3>Attempting Connection...</h3>";
try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "<span style='color:green'>SUCCESS: Connected to Supabase!</span>";
} catch (\Exception $e) {
    echo "<span style='color:red'>CONNECTION FAILED: " . $e->getMessage() . "</span>";
}
