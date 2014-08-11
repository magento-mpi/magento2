<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Category;

/**
 * @codeCoverageIgnore
 */
class ProductLink extends \Magento\Framework\Service\Data\AbstractObject
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
