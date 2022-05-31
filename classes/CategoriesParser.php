<?php

namespace App;

class CategoriesParser extends Base
{
    /**
     * @return string
     */
    protected function getEndpoint(): string
    {
        return $this->config->apiCategoriesEndpoint . '/' . $this->sessionToken;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getCategories(): array
    {
        $categoriesResponse = $this->http->get($this->getEndpoint());
        if ($categoriesResponse->getStatusCode() != 200) {
            throw new Exception('Error getting categories: http code ' . $categoriesResponse->getStatusCode());
        }
        $categories = json_decode($categoriesResponse->getBody()->getContents());
        if ($categories->status != 1) {
            throw new Exception('Error getting categories: ' . $categories->result);
        }
        return $categories->result;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function writeCategories(): void
    {
        $query = 'INSERT INTO categories
                    SET categoryID = :categoryID,
                        parentID = :parentID,
                        realcat = :realcat,
                        name = :name
                    ON DUPLICATE KEY UPDATE 
                        parentID = :parentID,
                        realcat = :realcat,
                        name = :name';
        $stmt = $this->db->prepare($query);
        foreach ($this->getCategories() as $category) {
            //echo $category->name."\n\r";
            $stmt->execute([
                'categoryID' => $category->categoryID,
                'parentID' => $category->parentID,
                'realcat' => $category->realcat,
                'name' => $category->name
            ]);
        }
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->writeCategories();
    }

}