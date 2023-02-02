<?php

namespace App;

use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Config;
use App\Robot;
use App\ProductsActualiser;

class ProductActualiserRobot extends Robot
{
    /**
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->logFile = 'ProductsActualiser.log';
            $config = new Config();
            $logger = new Logger('ProductsActualiser');
            $logger->pushHandler(new StreamHandler($config->logPath . $this->logFile));
            if ($this->isRobotLock(__CLASS__)) {
                $logger->warning('Bite the tail! Robot ' . __CLASS__ . ' is still working!');
                exit();
            }
            echo "Start working with products. \n";
            $logger->info('Start working with products.');
            (new ProductsActualiser($logger))->execute();
            echo "Working with products finished! \n";
            $logger->info('Working with products finished!');
        } catch (Throwable $e) {
            $logger = new Logger('ProductsActualiser');
            $logger->pushHandler(new StreamHandler($config->logPath . $this->logFile));
            $logger->warning($e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine());
            echo "Error! " . $e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine();
        }
    }
}