<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

$driver = config('database.driver', 'mysql');
$host   = config('database.host', 'localhost');
$port   = config('database.port', '3306');
$dbname = config('database.database', 'themora_shop');
$user   = config('database.username', 'root');

echo "Trying to connect to: <b>$driver://$user@$host:$port/$dbname</b><br><br>";

try {
    $db = Database::getInstance();
    echo "<span style='color:green'>SUCCESS: Connected to database!</span>";
    
    $res = Database::fetchOne("SELECT current_user");
    echo "<pre>"; print_r($res); echo "</pre>";

} catch (\Exception $e) {
    echo "<span style='color:red'>FAILURE: " . $e->getMessage() . "</span>";
}
