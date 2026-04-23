<?php
require 'app/bootstrap.php';
use App\Core\Database;

$categories = Database::fetchAll('SELECT * FROM categories');
foreach ($categories as $cat) {
    echo "ID: " . $cat['id'] . " | Name: " . $cat['name'] . " | Slug: " . $cat['slug'] . " | Icon: " . ($cat['icon'] ?? 'N/A') . "\n";
}
