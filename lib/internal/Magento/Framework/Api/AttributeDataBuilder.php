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
class AttributeDataBuilder extends Builder
{
    /**
     * Set attribute code
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(AttributeInterface::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Set attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(AttributeInterface::VALUE, $value);
    }

    /**
     * @param ObjectFactory $objectFactory
     * @param MetadataServiceInterface $metadataService
     * @param \Magento\Framework\Reflection\DataObjectProcessor $objectProcessor
     * @param \Magento\Framework\Reflection\TypeProcessor $typeProcessor
     * @param \Magento\Framework\Serialization\DataBuilderFactory $dataBuilderFactory
     * @param \Magento\Framework\ObjectManager\Config $objectManagerConfig
     * @param string $modelClassInterface
     */
    public function __construct(
        ObjectFactory $objectFactory,
        MetadataServiceInterface $metadataService,
        \Magento\Framework\Reflection\DataObjectProcessor $objectProcessor,
        \Magento\Framework\Reflection\TypeProcessor $typeProcessor,
        \Magento\Framework\Serialization\DataBuilderFactory $dataBuilderFactory,
        \Magento\Framework\ObjectManager\Config $objectManagerConfig,
        $modelClassInterface = 'Magento\Framework\Api\AttributeInterface'
    ) {
        parent::__construct(
            $objectFactory,
            $metadataService,
            $this,
            $objectProcessor,
            $typeProcessor,
            $dataBuilderFactory,
            $objectManagerConfig,
            $modelClassInterface
        );
    }
}
