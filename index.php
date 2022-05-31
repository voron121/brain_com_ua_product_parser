<?php

include __DIR__ ."/vendor/autoload.php";

use App\Auth;
use App\CategoriesParser;
use App\ProductDetailParser;

$test = new ProductDetailParser();

//echo "<pre>";
$test->execute();


?>