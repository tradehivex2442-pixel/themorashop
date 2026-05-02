<?php
require 'app/bootstrap.php';

echo "<h2>Cleaning up Product Image Paths...</h2>";

use App\Core\Database;

try {
    // 1. Clean thumbnail paths
    $sql = "UPDATE products SET thumbnail = REPLACE(thumbnail, '/themora_Shop/public/assets/', '') WHERE thumbnail LIKE '/themora_Shop/public/assets/%'";
    Database::execute($sql);
    echo "<p style='color:green'>✅ Thumbnail paths cleaned!</p>";

    // 2. Clean file paths if any
    $sql2 = "UPDATE products SET file_path = REPLACE(file_path, '/themora_Shop/public/assets/', '') WHERE file_path LIKE '/themora_Shop/public/assets/%'";
    Database::execute($sql2);
    echo "<p style='color:green'>✅ Product file paths cleaned!</p>";

    echo "<br><p><b>Done!</b> Now all your images should show up dynamically on the Home page and Products page.</p>";
    echo "<a href='public/products'>Go to Shop</a>";

} catch (\Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
