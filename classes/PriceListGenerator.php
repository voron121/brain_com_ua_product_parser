<?php

namespace App;

class PriceListGenerator extends PriceListBase
{
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
        $stmt = $this->db->query('SELECT count(productID) FROM products WHERE products.description IS NOT NULL');
        return $stmt->fetchColumn();
    }

    /**
     * @return int
     */
    private function calcPricelistsCount(): int
    {
        return ceil($this->getProductsCount() / $this->config->productsLimit);
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $priceListsCount = $this->calcPricelistsCount();
        for ($i = 0; $i <= $priceListsCount; $i++) {
            $offset = $this->getOffset($i);
            $priceListCreator = new PriceListCreator($offset);
            $priceListCreator->execute();
            $priceListCreator = null;
            usleep(rand(100000, 1000000));
        }
    }

}