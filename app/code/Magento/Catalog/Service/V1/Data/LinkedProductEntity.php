<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data;

/**
 * LinkedProductEntity Service Data Object
 */
class LinkedProductEntity extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants for Data Object keys
     */
    const ID = 'product_id';
    const TYPE = 'type';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const SKU = 'sku';
    const POSITION = 'position';

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Get attribute set
     *
     * @return int
     */
    public function getAttributeSetId()
    {
        return $this->_get(self::ATTRIBUTE_SET_ID);
    }

    /**
     * Get product sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Get product position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }
}
