<?php

namespace App;

use GuzzleHttp\Client;
use Exception;

abstract class Base
{
    protected $sessionToken;

    protected $db;

    protected $http;

    protected $config;

    public function __construct()
    {
        $this->db = DBConnector::getDB();
        $this->config = new Config();
        $this->http = new Client();
        $this->sessionToken = Auth::getSessionToken();
    }

    abstract protected function getEndpoint(): string;

    abstract public function execute(): void;
}