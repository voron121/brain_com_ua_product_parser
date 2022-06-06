<?php

namespace App;

abstract class PriceListBase
{
    protected $db;

    protected $config;

    public function __construct()
    {
        $this->config = new Config();
        $this->db = DBConnector::getDB();
    }

    /**
     * @return void
     */
    abstract public function execute(): void;
}