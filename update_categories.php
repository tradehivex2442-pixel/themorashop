<?php
require 'app/bootstrap.php';
use App\Core\Database;

$mapping = [
    1 => ['name' => 'PACKS', 'slug' => 'packs', 'icon' => 'bi-bag'],
    2 => ['name' => 'EBOOK', 'slug' => 'ebook', 'icon' => 'bi-book'],
    3 => ['name' => 'SOFTWARE', 'slug' => 'software', 'icon' => 'bi-code-slash'],
    4 => ['name' => 'TOOLS', 'slug' => 'tools', 'icon' => 'bi-tools'],
    5 => ['name' => 'AI TOOLS', 'slug' => 'ai-tools', 'icon' => 'bi-cpu'],
    6 => ['name' => 'SOURCE CODE', 'slug' => 'source-code', 'icon' => 'bi-file-earmark-code'],
    7 => ['name' => 'MOBILE APPS', 'slug' => 'mobile-apps', 'icon' => 'bi-phone'],

];

foreach ($mapping as $id => $data) {
    Database::query(
        "UPDATE categories SET name = ?, slug = ?, icon = ? WHERE id = ?",
        [$data['name'], $data['slug'], $data['icon'], $id]
    );
}

// Delete Fonts (ID 8) or any other extra categories
Database::query("DELETE FROM categories WHERE id > 7");

echo "Categories updated successfully.\n";
