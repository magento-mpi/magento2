<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data;

/**
 * Builder for the CatalogProductLinkEntity Service Data Object
 *
 * @method CatalogProductLinkEntity create()
 */
class CatalogProductLinkEntityBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->_set(CatalogProductLinkEntity::ID, $productId);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(CatalogProductLinkEntity::TYPE, $type);
    }

    /**
     * Set attribute set
     *
     * @param int $attributeSet
     * @return $this
     */
    public function setAttributeSetId($attributeSet)
    {
        return $this->_set(CatalogProductLinkEntity::ATTRIBUTE_SET_ID, $attributeSet);
    }

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->_set(CatalogProductLinkEntity::SKU, $sku);
    }

    /**
     * Set product position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(CatalogProductLinkEntity::POSITION, $position);
    }
}
