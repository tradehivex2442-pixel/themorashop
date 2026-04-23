<?php
require 'app/bootstrap.php';
use App\Core\Database;

Database::execute("DELETE FROM settings WHERE `key` IN ('google_client_id', 'github_client_id')");
Database::execute("INSERT INTO settings (`key`, value) VALUES ('google_client_id', 'mock'), ('github_client_id', 'mock')");

echo "Social Login Settings Forced to Mock in Database.\n";
