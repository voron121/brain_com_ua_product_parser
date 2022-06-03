<?php

namespace App;

class VendorsParser extends Base
{
    const API_SERVICE = 'vendors';

    /**
     * @param int $productID
     * @return string
     */
    private function getVendorEndpoint(): string
    {
        return $this->getEndpoint() . '/' . $this->sessionToken;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getVendors(): array
    {
        $vendorsResponse = $this->http->get($this->getVendorEndpoint());
        if ($vendorsResponse->getStatusCode() != 200) {
            throw new Exception('Error getting vendors: http code ' . $vendorsResponse->getStatusCode());
        }
        $vendors = json_decode($vendorsResponse->getBody()->getContents());
        if ($vendors->status != 1) {
            throw new Exception('Error getting vendors: ' . $vendors->result);
        }
        return array_column($vendors->result, null, 'vendorID');
    }

    /**
     * @return void
     * @throws Exception
     */
    private function writeVendors(): void
    {
        $query = 'INSERT INTO vendors
                    SET vendorID = :vendorID,
                        name = :name
                    ON DUPLICATE KEY UPDATE
                        name = :name';
        $stmt = $this->db->prepare($query);
        foreach ($this->getVendors() as $vendor) {
            $stmt->execute([
                'vendorID' => $vendor->vendorID,
                'name' => $vendor->name
            ]);
        }
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->writeVendors();
    }

}