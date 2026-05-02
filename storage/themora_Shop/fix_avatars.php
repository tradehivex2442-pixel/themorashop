<?php
require 'app/bootstrap.php';
use App\Core\Database;

// Fix existing avatar paths
Database::execute("UPDATE users SET avatar = REPLACE(avatar, '/themora_Shop/public/assets/', '') WHERE avatar LIKE '/themora_Shop/public/assets/%'");
Database::execute("UPDATE users SET avatar = REPLACE(avatar, 'images/avatars/', '') WHERE avatar LIKE 'images/avatars/%'");
// Ensure they start with images/avatars/ IF they are clean filenames
Database::execute("UPDATE users SET avatar = CONCAT('images/avatars/', avatar) WHERE avatar NOT LIKE 'images/%' AND avatar IS NOT NULL AND avatar != ''");

echo "Avatar database paths normalized.\n";
