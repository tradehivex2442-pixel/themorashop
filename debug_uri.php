<?php
$_SERVER['SCRIPT_NAME'] = '/themora_Shop/public/index.php';
$_SERVER['REQUEST_URI'] = '/themora_Shop/public/products/tryuttjyyuiu';
require 'app/bootstrap.php';
$req = new \App\Core\Request();
echo "Base: " . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . "\n";
echo "Parsed URI for Router: " . $req->uri() . "\n";
