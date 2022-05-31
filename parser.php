<?php
include __DIR__ ."/vendor/autoload.php";

use App\CategoriesParser;
use App\ProductsParser;
use App\ProductDetailParser;

try {
    echo "Start parsing categories. \n";
    (new CategoriesParser())->execute();
    echo "Parsing categories success!. \n";

    echo "Start parsing products. \n";
    (new ProductsParser())->execute();
    echo "Parsing products success!. \n";

    echo "Start parsing products details. \n";
    (new ProductDetailParser())->execute();
    echo "Parsing products details success!. \n";
} catch (Throwable $e) {
    echo "Error! " . $e->getMessage();
}
?>