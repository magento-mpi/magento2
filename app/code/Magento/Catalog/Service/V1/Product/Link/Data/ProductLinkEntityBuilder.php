<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * Builder for the ProductLinkEntity Service Data Object
 *
 * @method ProductLinkEntity create()
 */
class ProductLinkEntityBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->_set(ProductLinkEntity::ID, $productId);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(ProductLinkEntity::TYPE, $type);
    }

    /**
     * Set attribute set
     *
     * @param int $attributeSet
     * @return $this
     */
    public function setAttributeSetId($attributeSet)
    {
        return $this->_set(ProductLinkEntity::ATTRIBUTE_SET_ID, $attributeSet);
    }

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->_set(ProductLinkEntity::SKU, $sku);
    }

    /**
     * Set product position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(ProductLinkEntity::POSITION, $position);
    }
}
