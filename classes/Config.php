<?php

namespace App;

final class Config
{
    public $dbHost = 'localhost';
    public $dbName = 'someDB';
    public $storeDBName = 'someStoreDB';
    public $dbPass = 'admin:)';
    public $dbUser = 'admin:)';
    public $apiLogin = 'test';
    public $apiPassword = 'superTest';
    public $apiEndpoint = 'http://api.brain.com.ua';
    public $apiAuthEndpoint = 'http://api.brain.com.ua/auth';
    public $shopName = 'brain';
    public $shopCompany = 'brain';
    public $shopUrl = 'https://google.com';
    public $shopCurrency = 'UAH';
    public $priceListFileName = 'brain_for_rozetka.xml';
    public $priceListFilePath = __DIR__ . '/../pricelist/';
    public $logPath = __DIR__ . '/../logs/';

    public $jsonPricePath = __DIR__ . '/../json-pricelist/filename.json';


}