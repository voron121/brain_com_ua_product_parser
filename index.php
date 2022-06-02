<?php

include __DIR__ ."/vendor/autoload.php";

use App\Auth;
use App\CategoriesParser;
use App\ProductDetailParser;
use App\PriceListGenerator;

$test = new PriceListGenerator();

//echo "<pre>";
$test->execute();


?>