<?php
require 'app/bootstrap.php';
use App\Core\Database;

try {
    Database::execute("ALTER TABLE users ADD COLUMN bio TEXT AFTER avatar");
} catch (Exception $e) {}

try {
    Database::execute("ALTER TABLE users ADD COLUMN notif_order_success TINYINT(1) DEFAULT 1");
} catch (Exception $e) {}

try {
    Database::execute("ALTER TABLE users ADD COLUMN notif_download_expiry TINYINT(1) DEFAULT 1");
} catch (Exception $e) {}

try {
    Database::execute("ALTER TABLE users ADD COLUMN notif_newsletter TINYINT(1) DEFAULT 0");
} catch (Exception $e) {}

try {
    Database::execute("ALTER TABLE users ADD COLUMN notif_affiliate TINYINT(1) DEFAULT 1");
} catch (Exception $e) {}

echo "Database schema updated successfully.\n";
