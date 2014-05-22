<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data;

/**
 * Builder for the LinkedProductEntity Service Data Object
 *
 * @method LinkedProductEntity create()
 */
class LinkedProductEntityBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->_set(LinkedProductEntity::ID, $productId);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(LinkedProductEntity::TYPE, $type);
    }

    /**
     * Set attribute set
     *
     * @param int $attributeSet
     * @return $this
     */
    public function setAttributeSetId($attributeSet)
    {
        return $this->_set(LinkedProductEntity::ATTRIBUTE_SET_ID, $attributeSet);
    }

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->_set(LinkedProductEntity::SKU, $sku);
    }

    /**
     * Set product position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(LinkedProductEntity::POSITION, $position);
    }
}
