<?php

namespace App;
use DOMDocument;
use DomainException;

class PriceListGenerator
{
    const PRICE_LIST_NAME = 'brain_for_rozetka.xml';

    const PRICE_LIST_PATH = __DIR__ . '/../pricelist/';

    protected $db;

    protected $dom;

    public function __construct()
    {
        $this->db = DBConnector::getDB();
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;
    }

    /**
     * @return array
     */
    private function getCategories(): array
    {
        $query = 'SELECT categoryID, parentID, realcat, name FROM categories';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    /**
     * @return array
     */
    private function getProducts(): array
    {
        $query = 'SELECT productID, 
                         name, 
                         description, 
                         country, 
                         categoryID, 
                         product_code, 
                         is_archive, 
                         articul, 
                         weight, 
                         price_uah, 
                         full_image, 
                         options 
                    FROM products
                    LIMIT 0, 2';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    /**
     * @return \DOMElement|\DOMNode|false
     * @throws \DOMException
     */
    private function getShopNode(): \DOMElement
    {
        $shopNode = $this->dom->createElement('shop');
        $shopNode->appendChild($this->dom->createElement('name', 'brain'));
        $shopNode->appendChild($this->dom->createElement('company', 'brain'));
        $shopNode->appendChild($this->dom->createElement('url', 'https://google.com'));
        return $shopNode;
    }

    /**
     * @return \DOMElement
     * @throws \DOMException
     */
    private function getYmlСatalogNode(): \DOMElement
    {
        $yml = $this->dom->createElement('yml_catalog');
        $yml->setAttribute('date', date('Y-m-d h:i:s'));
        return $yml;
    }

    /**
     * @return void
     * @throws \DOMException
     */
    private function getCurrenciesNode(): \DOMElement
    {
        $currency = $this->dom->createElement('currency');
        $currency->setAttribute('id', 'UAH');
        $currency->setAttribute('rate', '1');
        $currencies = $this->dom->createElement('currencies');
        $currencies->appendChild($currency);
        return $currencies;
    }

    /**
     * @return void
     * @throws \DOMException
     */
    private function getCategoriesNode(): \DOMElement
    {
        $categories = $this->dom->createElement('categories');
        foreach ($this->getCategories() as $category) {
            $categoryNode = $this->dom->createElement('category', $category->name);
            $categoryNode->setAttribute('id', $category->categoryID);
            if ($category->parentID != 1) {
                $categoryNode->setAttribute('parentId', $category->parentID);
            }
            $categories->appendChild($categoryNode);
        }
        return $categories;
    }

    private function createPriceList()
    {
        $priceList = $this->getYmlСatalogNode();
        $shopNode = $this->getShopNode();
        $shopNode->appendChild($this->getCurrenciesNode());
        $shopNode->appendChild($this->getCategoriesNode());
        $priceList->appendChild($shopNode);
        $this->dom->appendChild($priceList);
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->createPriceList();
        var_dump( $this->dom->saveXML() );

        file_put_contents(   self::PRICE_LIST_PATH . self::PRICE_LIST_NAME, $this->dom->saveXML());
    }
}