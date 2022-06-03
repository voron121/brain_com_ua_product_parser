<?php

namespace App;

use GuzzleHttp\Client;
use Exception;

abstract class Base
{
    const API_SERVICE = '';

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

    protected function getEndpoint(): string
    {
        return $this->config->apiEndpoint . '/' . static::API_SERVICE;
    }

    abstract public function execute(): void;
}