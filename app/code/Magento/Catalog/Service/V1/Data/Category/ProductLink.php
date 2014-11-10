<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Category;

/**
 * @deprecated
 * @todo remove this interface
 *
 * Replaced with @see \Magento\Catalog\Api\Data\CategoryProductLinkInterface
 */
class ProductLink extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const SKU = 'sku';

    const POSITION = 'position';
    /**#@-*/

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }
}
