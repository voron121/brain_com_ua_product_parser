<?php

namespace App;

use Exception;
use App\CategoriesParser;
use App\ProductsParser;
use App\ProductDetailParser;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Config;
use App\Robot;

class ParserRobot extends Robot
{
    public function execute(): void
    {
        try {
            $this->logFile = 'parser.log';
            $config = new Config();
            $logger = new Logger('parser');
            $logger->pushHandler(new StreamHandler($config->logPath . $this->logFile));
            if ($this->isRobotLock(__CLASS__)) {
                $logger->warning('Bite the tail! Robot ' . __CLASS__ . ' is still working!');
                exit();
            }
            echo "Start parsing categories. \n";
            $logger->info('Start parsing categories.');
            //(new CategoriesParser())->execute();
            echo "Parsing categories success!. \n";
            $logger->info('Parsing categories success!');

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
            $logger->pushHandler(new StreamHandler($config->logPath . $this->logFile));
            $logger->warning($e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine());
            echo "Error! " . $e->getMessage() . " file: " . $e->getFile(). " line: " . $e->getLine();
        }
    }
}