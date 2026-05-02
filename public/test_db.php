<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

$driver = env('DB_DRIVER', 'mysql');
$host   = env('DB_HOST', 'localhost');
$port   = env('DB_PORT', '3306');
$dbname = env('DB_NAME', 'themora_shop');
$user   = env('DB_USER', 'root');

echo "Trying to connect to: <b>$driver://$user@$host:$port/$dbname</b><br><br>";

try {
    $db = Database::getInstance();
    echo "<span style='color:green'>SUCCESS: Connected to database!</span>";
    
    $res = Database::fetchOne("SELECT current_user");
    echo "<pre>"; print_r($res); echo "</pre>";

} catch (\Exception $e) {
    echo "<span style='color:red'>FAILURE: " . $e->getMessage() . "</span>";
}
