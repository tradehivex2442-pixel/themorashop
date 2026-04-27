<?php
require 'app/bootstrap.php';
use App\Core\Database;

echo "<h1>Image Diagnostics</h1>";

// 1. Check Product Thumbnails
$products = Database::fetchAll('SELECT id, title, thumbnail FROM products');
echo "<h2>Product Thumbnails (" . count($products) . ")</h2>";
echo "<ul>";
foreach ($products as $p) {
    $path = $p['thumbnail'];
    $absolute = PUB_PATH . str_replace(url(), '', $path);
    if ($path && !file_exists($absolute) && !str_starts_with($path, 'http')) {
        echo "<li>ID: {$p['id']} | Title: {$p['title']} | <span style='color:red'>Broken Path: {$path}</span></li>";
    } else {
        echo "<li>ID: {$p['id']} | Title: {$p['title']} | Path: {$path} " . (str_starts_with($path, 'http') ? '(External)' : '(OK)') . "</li>";
    }
}
echo "</ul>";

// 2. Check Category Icons (SVG/Img)
$cats = Database::fetchAll('SELECT id, name, icon FROM categories');
echo "<h2>Category Icons</h2>";
foreach ($cats as $c) {
    echo "ID: {$c['id']} | Name: {$c['name']} | Icon: {$c['icon']}<br>";
}

// 3. Test asset() and url()
echo "<h2>Helper Tests</h2>";
echo "URL('/'): " . url('/') . "<br>";
echo "Asset('css/app.css'): " . asset('css/app.css') . "<br>";
echo "PUB_PATH: " . PUB_PATH . "<br>";
