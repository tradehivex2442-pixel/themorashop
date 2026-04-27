<?php
require 'app/bootstrap.php';
use App\Core\Database;

$values = [
    'google_client_id' => '1039716376020-86pgvhbfonesoqjvpqavak1efhoeg3ej.apps.googleusercontent.com',
    'google_client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET'
];

foreach ($values as $key => $val) {
    Database::execute(
        "INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?",
        [$key, $val, $val]
    );
}

echo "Database settings updated successfully.\n";
unlink(__FILE__); // Self-delete
