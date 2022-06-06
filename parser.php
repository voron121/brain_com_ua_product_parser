<?php
include __DIR__ ."/vendor/autoload.php";

use App\CategoriesParser;
use App\ProductsParser;
use App\ProductDetailParser;
use App\VendorsParser;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Config;

try {
    define('LOG_FILE', 'parser.log');
    $config = new Config();
    $logger = new Logger('parser');
    $logger->pushHandler(new StreamHandler($config->logPath . LOG_FILE));

    echo "Start parsing categories. \n";
    $logger->info('Start parsing categories.');
    (new CategoriesParser())->execute();
    echo "Parsing categories success!. \n";
    $logger->info('Parsing categories success!');

    echo "Start parsing vendors. \n";
    $logger->info('Start parsing vendors.');
    (new VendorsParser())->execute();
    echo "Parsing vendors success!. \n";
    $logger->info('Parsing vendors success!');

    echo "Start parsing products. \n";
    $logger->info('Start parsing products.');
    (new ProductsParser())->execute();
    echo "Parsing products success!. \n";
    $logger->info('Parsing products success!');

    echo "Start parsing products details. \n";
    $logger->info('Start parsing products details.');
    (new ProductDetailParser())->execute();
    echo "Parsing products details success! \n";
    $logger->info('Parsing products details success!');
} catch (Throwable $e) {
    $logger = new Logger('parser');
    $logger->pushHandler(new StreamHandler($config->logPath . LOG_FILE));
    $logger->warning($e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine());
    echo "Error! " . $e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine();
}
?>