<?php

namespace App;

class ProductsParser extends Base
{
    const LIMIT = 100;

    const API_SERVICE = 'products';

    /**
     * @param int $categoryID
     * @return string
     */
    private function getProductsListEndpoint(int $categoryID, int $offset): string
    {
        return $this->getEndpoint() . '/' . $categoryID . '/' . $this->sessionToken . '?offset=' . $offset;
    }

    /**
     * @return array
     */
    private function getCategories(): array
    {
        $stmt = $this->db->query('SELECT categoryID FROM categories');
        return $stmt->fetchAll();
    }

    /**
     * @param int $step
     * @return int
     */
    private function getOffset(int $step): int
    {
        $offset = 0;
        if ($step > 0) {
            $offset = self::LIMIT * $step;
        }
        return $offset;
    }

    /**
     * @return array
     */
    private function getProductsByCategory(int $categoryID): array
    {
        $i = 0;
        $productsList = [];
        do {
            $offset = $this->getOffset($i);
            $productsResponse = $this->http->get($this->getProductsListEndpoint($categoryID, $offset));
            if ($productsResponse->getStatusCode() != 200) {
                throw new Exception('Error getting products list: http code ' . $productsResponse->getStatusCode());
            }
            $products = json_decode($productsResponse->getBody()->getContents());
            if ($products->status != 1) {
                throw new Exception('Error getting products list: ' . $products->result);
            }
            $this->writeProducts($products->result->list);
            $productsCountTotal = $products->result->count;
            $i++;
            usleep(rand(100000, 1000000));
        } while($offset < $productsCountTotal);
        return $productsList;
    }

    /**
     * @param array $products
     * @return void
     */
    private function writeProducts(array $products): void
    {
        $query = 'INSERT INTO products
                    SET productID = :productID,
                        product_code = :product_code,
                        warranty = :warranty,
                        is_archive = :is_archive,
                        is_exclusive = :is_exclusive,
                        vendorID = :vendorID,
                        articul = :articul,
                        volume = :volume,
                        weight = :weight,
                        kbt = :kbt,
                        is_new = :is_new,
                        categoryID = :categoryID,
                        reservation_limit = :reservation_limit,
                        name = :name,
                        brief_description = :brief_description,
                        country = :country,
                        price = :price,
                        price_uah = :price_uah,
                        recommendable_price = :recommendable_price,
                        retail_price_uah = :retail_price_uah,
                        bonus = :bonus,
                        full_image = :full_image,
                        medium_image = :medium_image
                    ON DUPLICATE KEY UPDATE 
                        product_code = :product_code,
                        warranty = :warranty,
                        is_archive = :is_archive,
                        is_exclusive = :is_exclusive,
                        vendorID = :vendorID,
                        articul = :articul,
                        volume = :volume,
                        weight = :weight,
                        kbt = :kbt,
                        is_new = :is_new,
                        categoryID = :categoryID,
                        reservation_limit = :reservation_limit,
                        name = :name,
                        brief_description = :brief_description,
                        country = :country,
                        price = :price,
                        price_uah = :price_uah,
                        recommendable_price = :recommendable_price,
                        retail_price_uah = :retail_price_uah,
                        bonus = :bonus,
                        full_image = :full_image,
                        medium_image = :medium_image';
        $stmt = $this->db->prepare($query);
        foreach ($products as $product) {
            // echo $product->name."\n\r";
            $stmt->execute([
                "productID" => (int)$product->productID,
                "product_code" => $product->product_code,
                "warranty" => $product->warranty,
                "is_archive" => $product->is_archive ? "yes" : "no",
                "is_exclusive" => $product->is_exclusive ? "yes" : "no",
                "vendorID" => (int)$product->vendorID,
                "articul" => $product->articul,
                "volume" => $product->volume,
                "weight" => $product->weight,
                "kbt" => $product->kbt,
                "is_new" => $product->is_new,
                "categoryID" => (int)$product->categoryID,
                "reservation_limit" => (int)$product->reservation_limit,
                "name" => $product->name,
                "brief_description" => $product->brief_description,
                "country" => $product->country,
                "price" => $product->price,
                "price_uah" => $product->price_uah,
                "recommendable_price" => $product->recommendable_price,
                "retail_price_uah" => $product->retail_price_uah,
                "bonus" => $product->bonus,
                "full_image" => $product->full_image,
                "medium_image" => $product->medium_image
            ]);
        }
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        foreach ($this->getCategories() as $category) {
            $this->writeProducts($category->categoryID);
        }
    }

}