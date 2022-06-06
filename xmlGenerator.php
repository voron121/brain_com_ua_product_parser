<?php
include __DIR__ ."/vendor/autoload.php";

use App\PriceListGenerator;
use App\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

try {
    define('LOG_FILE', 'xmlGeneratorLog.log');
    $config = new Config();
    $logger = new Logger('xmlGeneratorLog');
    $logger->pushHandler(new StreamHandler($config->logPath . LOG_FILE));

    echo "Start create price list. \n";
    $logger->info('Start create price list.');
    (new PriceListGenerator())->execute();
    echo "Create price list success!. \n";
    $logger->info('Create price list success!.');
} catch (Throwable $e) {
    $logger = new Logger('parser');
    $logger->pushHandler(new StreamHandler($config->logPath . LOG_FILE));
    $logger->warning($e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine());
    echo "Error! " . $e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine();
}
?>