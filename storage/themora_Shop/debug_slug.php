<?php
require 'app/bootstrap.php';
$slug = 'tryuttjyyuiu';
$product = \App\Core\Database::fetchOne("SELECT slug, LENGTH(slug) as len, status FROM products WHERE slug = ?", [$slug]);
var_dump($product);
if (!$product) {
    echo "Product not found. Checking LIKE...\n";
    $products = \App\Core\Database::fetchAll("SELECT slug, LENGTH(slug) as len FROM products WHERE slug LIKE 'tryuttjyyuiu%'");
    var_dump($products);
}
