<?php

namespace App;

use App\Config;

final class DBConnector
{
    /**
     * @return PDO
     */
    public static function getDB(): \PDO
    {
        $config = new Config();
        $pdo = new \PDO('mysql:host=' . $config->dbHost . ';dbname=' . $config->dbName . ';charset=utf8', $config->dbName, $config->dbPass);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        return $pdo;
    }
}