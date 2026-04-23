<?php
require 'app/bootstrap.php';
use App\Core\Database;
$cols = Database::fetchAll('DESCRIBE categories');
print_r($cols);
