<?php

namespace App;
use DOMDocument;
use DomainException;

class PriceListGenerator
{
    const PRICE_LIST_NAME = 'brain_for_rozetka.xml';

    const PRICE_LIST_PATH = __DIR__ . '/../pricelist/';

    const CURRENCY_CODE = 'UAH';

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
        $query = 'SELECT products.productID, 
                         products.name, 
                         products.description, 
                         products.country, 
                         products.categoryID,
                         products.vendorID,
                         products.product_code, 
                         products.is_archive, 
                         products.articul, 
                         products.weight, 
                         products.price_uah, 
                         products.medium_image,
                         vendors.name AS vendor,
                         products.options 
                    FROM products
                        LEFT JOIN vendors USING(vendorID)
                    WHERE products.description IS NOT NULL';
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
        $currency->setAttribute('id', self::CURRENCY_CODE);
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

    /**
     * @param $product
     * @return \DOMElement
     * @throws \DOMException
     */
    private function getOfferNode($product): \DOMElement
    {
        $offer = $this->dom->createElement('offer');
        $offer->setAttribute('id', $product->productID);
        $offer->setAttribute('available', $product->is_archive === 'no' ? 'true' : 'false');
        $offer->appendChild($this->dom->createElement('name', htmlspecialchars(strip_tags(trim($product->name))) ));
        $offer->appendChild($this->dom->createElement('description', htmlspecialchars(strip_tags(trim($product->description))) ));
        $offer->appendChild($this->dom->createElement('price', $product->price_uah));
        $offer->appendChild($this->dom->createElement('currencyId', self::CURRENCY_CODE));
        $offer->appendChild($this->dom->createElement('categoryId', $product->categoryID));
        $offer->appendChild($this->dom->createElement('picture', $product->medium_image));
        // HARDCODE: i don't know from what field get this value? so set 100
        $offer->appendChild($this->dom->createElement('stock_quantity', 100));
        $offer->appendChild($this->dom->createElement('vendorCode', $product->vendorID));
        $offer->appendChild($this->dom->createElement('brend', htmlspecialchars(strip_tags(trim($product->vendor)))));
        // Add params if they exist
        if (!is_null($product->options)) {
            $options = json_decode($product->options);
            foreach ($options as $option) {
                $param = $this->dom->createElement('param', htmlspecialchars(strip_tags(trim($option->value))) );
                $param->setAttribute('name', $option->name);
                $offer->appendChild($param);
            }
        }
        return $offer;
    }

    /**
     * @return \DOMElement
     * @throws \DOMException
     */
    private function getOffersNode(): \DOMElement
    {
        $offersNode = $this->dom->createElement('offers');
        foreach ($this->getProducts() as $product) {
            $offersNode->appendChild($this->getOfferNode($product));
        }
        return $offersNode;
    }

    /**
     * @return void
     * @throws \DOMException
     */
    private function createPriceList()
    {
        $priceList = $this->getYmlСatalogNode();
        $shopNode = $this->getShopNode();
        $shopNode->appendChild($this->getCurrenciesNode());
        $shopNode->appendChild($this->getCategoriesNode());
        $shopNode->appendChild($this->getOffersNode());
        $priceList->appendChild($shopNode);
        $this->dom->appendChild($priceList);
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->createPriceList();
        //var_dump( $this->dom->saveXML() );
        file_put_contents(   self::PRICE_LIST_PATH . self::PRICE_LIST_NAME, $this->dom->saveXML());
    }
}