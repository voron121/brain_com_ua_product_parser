<?php
include __DIR__ ."/vendor/autoload.php";

use App\PriceListGenerator;

try {
    echo "Start create price list. \n";
    (new PriceListGenerator())->execute();
    echo "Create price list success!. \n";
} catch (Throwable $e) {
    echo "Error! " . $e->getMessage();
}
?>