<?php

include __DIR__ ."/vendor/autoload.php";

use App\Auth;
use App\CategoriesParser;
use App\ProductsParser;

$test = new ProductsParser();

//echo "<pre>";
$test->execute();


?>