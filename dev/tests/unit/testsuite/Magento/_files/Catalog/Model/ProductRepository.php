<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
