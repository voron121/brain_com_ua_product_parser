<?php

include __DIR__ ."/vendor/autoload.php";

use App\Auth;
use App\CategoriesParser;
use App\ProductDetailParser;
use App\PriceListGenerator;
use App\VendorsParser;

$test = new VendorsParser();

//echo "<pre>";
$test->execute();


?>