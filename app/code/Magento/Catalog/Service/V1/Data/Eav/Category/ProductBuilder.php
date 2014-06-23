<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category;

class ProductBuilder extends \Magento\Catalog\Service\V1\Data\ProductBuilder
{
    /**
     * Set product position list in category
     *
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Product::POSITION, $value);
    }
}
