<?php

namespace App;

class ProductDetailParser extends Base
{
    const API_SERVICE = 'product';

    /**
     * @param int $productID
     * @return string
     */
    private function getProductEndpoint(int $productID): string
    {
        return $this->getEndpoint() . '/' . $productID . '/' . $this->sessionToken;
    }

    /**
     * @return array
     */
    private function getProducts(int $offset): array
    {
        $query = 'SELECT productID 
                    FROM products 
                    LIMIT :limit 
                    OFFSET :offset';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':limit', $this->config->productsLimit, \PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();
        return array_keys(array_column($products, 'productID', 'productID'));
    }

    /**
     * @param int $step
     * @return int
     */
    private function getOffset(int $step): int
    {
        $offset = 0;
        if ($step > 0) {
            $offset = $this->config->productsLimit * $step + 1;
        }
        return $offset;
    }

    /**
     * @return int
     */
    private function getProductsCount(): int
    {
        $stmt = $this->db->query('SELECT count(productID) FROM products');
        return $stmt->fetchColumn();
    }

    /**
     * @return int
     */
    private function calcStepsCount(): int
    {
        return ceil($this->getProductsCount() / $this->config->productsLimit);
    }

    /**
     * @return array
     */
    private function getProductDetailsById(int $productID): array
    {
        $productResponse = $this->http->get($this->getProductEndpoint($productID));
        if ($productResponse->getStatusCode() != 200) {
            throw new Exception('Error getting products list: http code ' . $productResponse->getStatusCode());
        }
        $product = json_decode($productResponse->getBody()->getContents(), true);
        if ($product['status'] != 1) {
            throw new Exception('Error getting products list: ' . $product['result']);
        }
        return $product['result'];
    }

    /**
     * @param array $products
     * @return void
     */
    private function updateProduct(array $product): void
    {
        $query = 'UPDATE products
                    SET description = :description,
                        date_added = :date_added,
                        date_modified = :date_modified,
                        actionID = :actionID,
                        is_price_cut = :is_price_cut,
                        is_new = :is_new,
                        bonus = :bonus,
                        stocks = :stocks,
                        stocks_expected = :stocks_expected,
                        available = :available,
                        options = :options,
                        koduktved = :koduktved,
                        reservation_limit = :reservation_limit,
                        self_delivery = :self_delivery,
                        quantity_package_sale = :quantity_package_sale
                    WHERE productID = :productID';
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'description' => $product['description'],
            'date_added' => $product['date_added'],
            'date_modified' => $product['date_modified'],
            'is_price_cut' => $product['is_price_cut'] ? "yes" : "no",
            'is_new' => $product['is_new'] ? "yes" : "no",
            'actionID' => (int)$product['actionID'],
            'bonus' => $product['bonus'],
            'stocks' => json_encode($product['stocks']),
            'stocks_expected' => json_encode($product['stocks_expected']),
            'available' => json_encode($product['available']),
            'options' => json_encode($product['options']),
            'koduktved' => $product['koduktved'],
            'reservation_limit' => $product['reservation_limit'],
            'quantity_package_sale' => $product['quantity_package_sale'],
            'productID' => (int)$product['productID'],
            'self_delivery' => (int)$product['self_delivery'],
        ]);
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        for ($i = 0; $i <= $this->calcStepsCount(); $i++) {
            $offset = $this->getOffset($i);
            foreach ($this->getProducts($offset) as $productId) {
                $this->updateProduct($this->getProductDetailsById($productId));
                usleep(rand(100000, 1000000));
            }
        }
    }

}