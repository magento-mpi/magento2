<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api;

/**
 * Custom Attribute Data object builder
 */
class AttributeDataBuilder extends CompositeExtensibleDataBuilder
{
    /**
     * Set attribute code
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->set(AttributeInterface::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Set attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->set(AttributeInterface::VALUE, $value);
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Api\MetadataServiceInterface $metadataService
     * @param \Magento\Framework\ObjectManager\Config $objectManagerConfig
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Api\MetadataServiceInterface $metadataService,
        \Magento\Framework\ObjectManager\Config $objectManagerConfig
    ) {
        parent::__construct(
            $objectManager,
            $metadataService,
            $objectManagerConfig,
            'Magento\Framework\Api\AttributeInterface'
        );
    }
}
