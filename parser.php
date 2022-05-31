<?php
include __DIR__ ."/vendor/autoload.php";

use App\Auth;
use App\CategoriesParser;
use App\ProductsParser;

try {
    echo "Start parsing categories. \n";
    (new CategoriesParser())->execute();
    echo "Parsing categories success!. \n";
    echo "Start parsing products. \n";
    (new ProductsParser())->execute();
    echo "Parsing products success!. \n";
} catch (Throwable $e) {
    echo "Error! " . $e->getMessage();
}
?>