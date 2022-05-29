<?php

include __DIR__ ."/vendor/autoload.php";

use App\Auth;
use App\CategoriesParser;

$test = new CategoriesParser();

//echo "<pre>";
var_dump( $test->writeCategories() );


?>