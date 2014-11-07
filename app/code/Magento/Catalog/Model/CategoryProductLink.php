<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

class CategoryProductLink extends \Magento\Framework\Api\AbstractSimpleObject implements
    \Magento\Catalog\Api\Data\CategoryProductLinkInterface
{
    /**
     * Get product SKU
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Get category id
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->_get(self::CATEGORY_ID);
    }
}
