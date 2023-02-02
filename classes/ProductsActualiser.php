<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ProductsActualiser
{
    const MIN_PRODUCT_QUANTITY = 0;
    const MAX_PRODUCT_QUANTITY = 50;

    private $config;

    private $logger;

    private $storeDB;

    private $productsToDecrease = [];

    private $productsToIncrease = [];
    private $productsDataFromPriceList;

    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->config = new Config();
        $this->storeDB = DBConnector::getStoreDB();
        $this->productsDataFromPriceList = $this->getProductsCode();
    }

    /**
     * @return array
     */
    private function getProductsFromFile(): array
    {
        return array_values(json_decode(file_get_contents($this->config->jsonPricePath), true));
    }

    /**
     * @return array
     */
    private function getProductsCode(): array
    {
        $products = $this->getProductsFromFile();
        $productsCodeList = [];
        for ($i = 0; $i < count($products); $i++) {
            if (empty($products[$i])) {
                continue;
            }
            $productsCodeList[$products[$i]['Code']] = [
                'upc' => $products[$i]['Code'],
                'price' => $products[$i]['RecommendedPrice']
            ];
        }
        $this->logger->info('Getting  ' . count($productsCodeList) . ' products from file to processed');
        return $productsCodeList;
    }

    /**
     * @return array
     */
    private function getStoreProductsList(): array
    {
        $query = 'SELECT product_id, upc FROM oc_product';
        $stmt = $this->storeDB->query($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @return array
     */
    private function getStoreProductsUPCList(): array
    {
        $upcList = [];
        $products = $this->getStoreProductsList();
        array_walk($products, function ($item) use (&$upcList) {
            $upcList[$item->upc] = $item->product_id;
        });
        return $upcList;
    }

    /**
     * @return array
     */
    private function createProductsListToChangeProductQuantity(): void
    {
        $productsFromStore = $this->getStoreProductsUPCList();
        foreach ($productsFromStore as $upc => $productId) {
            if (isset($this->productsDataFromPriceList[$upc])) {
                $this->productsToIncrease[$productId] = $productId;
            } else {
                $this->productsToDecrease[$productId] = $productId;
            }
        }
        $this->logger->info('Getting  ' . count($this->productsToDecrease) . ' products to decrease quantity');
        $this->logger->info('Getting  ' . count($this->productsToIncrease) . ' products to increase quantity');
    }

    /**
     * @return void
     */
    private function decreaseProductQuantity(): void
    {
        $productsIds = array_map(function($item) {
            return $this->storeDB->quote($item);
        }, $this->productsToDecrease);
        $query = 'UPDATE oc_product 
                    SET quantity = ' . self::MIN_PRODUCT_QUANTITY . ' 
                    WHERE product_id  IN (' . implode(",", $productsIds) . ')';
        $this->storeDB->query($query);
        $this->logger->info('Decrease product quantity for  ' . count($productsIds) . ' products');
    }

    /**
     * @return void
     */
    private function increaseProductQuantity(): void
    {
        $productsIds = array_map(function($item) {
            return $this->storeDB->quote($item);
        }, $this->productsToIncrease);
        $query = 'UPDATE oc_product 
                    SET quantity = ' . self::MAX_PRODUCT_QUANTITY . ' 
                    WHERE product_id  IN (' . implode(",", $productsIds) . ')';
        $this->storeDB->query($query);
        $this->logger->info('Increase product quantity for  ' . count($productsIds) . ' products');
    }

    /**
     * @return void
     */
    private function updateProductPrice(): void
    {
        $productsToIncreaseList = array_flip($this->productsToIncrease);
        $query = 'UPDATE oc_product SET price = :price WHERE product_id = :product_id';
        $stmt = $this->storeDB->prepare($query);
        $updatedProductPriceCount = 0;
        foreach ($this->getStoreProductsUPCList() as $upc => $productsId) {
            if (!isset($productsToIncreaseList[$productsId]) || !isset($this->productsDataFromPriceList[$upc])) {
                continue;
            }
            $stmt->execute([
                'product_id' => $productsId,
                'price' => $this->productsDataFromPriceList[$upc]['price']
            ]);
            $updatedProductPriceCount++;
        }
        $this->logger->info('Update price for  ' .$updatedProductPriceCount . ' products');
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->createProductsListToChangeProductQuantity();
        $this->decreaseProductQuantity();
        $this->increaseProductQuantity();
        $this->updateProductPrice();
    }
}