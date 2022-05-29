<?php

namespace App;

class ProductsParser extends Base
{
    /**
     * @return string
     */
    protected function getEndpoint(): string
    {
        return $this->config->apiProductsEndpoint . '/' . $this->sessionToken;
    }


}