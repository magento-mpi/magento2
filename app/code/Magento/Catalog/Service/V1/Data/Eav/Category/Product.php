<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category;

/**
 * @codeCoverageIgnore
 */
class Product extends \Magento\Catalog\Service\V1\Data\Product
{
    /**
     * Constants used as keys into $_data
     */
    const POSITION = 'position';

    /**
     * Get product position
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(Product::POSITION);
    }
}
