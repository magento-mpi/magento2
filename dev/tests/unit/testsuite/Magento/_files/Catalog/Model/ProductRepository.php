<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Class ProductRepositoryMock
 */
class ProductRepositoryMock extends \Magento\Catalog\Model\ProductRepository
{
    /**
     * The stub method
     *
     * @param array $data
     * @return string
     */
    public function stubGetCacheKey(array $data)
    {
        return $this->getCacheKey($data);
    }
}
