<?php
require 'app/bootstrap.php';
use App\Core\Database;

$tables = Database::fetchAll('SHOW TABLES');
print_r($tables);
