<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

/**
 * Builder for the ProductLink Service Data Object
 *
 * @method ProductLink create()
 */
class ProductLinkBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    /**
     * @var array
     */
    protected $customAttributes = [];

    /**
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param array $customAttributesCodes
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        array $customAttributesCodes = array()
    ) {
        $this->customAttributes = $customAttributesCodes;
        parent::__construct($objectFactory, $valueBuilder);
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return $this
     * @codeCoverageIgnore
     */
    public function setType($type)
    {
        return $this->_set(ProductLink::TYPE, $type);
    }

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     * @codeCoverageIgnore
     */
    public function setSku($sku)
    {
        return $this->_set(ProductLink::SKU, $sku);
    }

    /**
     * Set product position
     *
     * @param int $position
     * @return $this
     * @codeCoverageIgnore
     */
    public function setPosition($position)
    {
        return $this->_set(ProductLink::POSITION, $position);
    }

    /**
     * Get custom attributes codes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        return array_merge(parent::getCustomAttributesCodes(), $this->customAttributes);
    }
}
