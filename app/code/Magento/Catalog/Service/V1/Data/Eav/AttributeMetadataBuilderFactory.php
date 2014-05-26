<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;


/**
 * Class AttributeMetadataBuilderFactory
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class AttributeMetadataBuilderFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param  string $attributeType
     * @return AttributeMetadataBuilder
     */
    public function create($attributeType)
    {
        $builderClassName = __NAMESPACE__ . '\\' . ucfirst($attributeType) . 'AttributeMetadataBuilder';
        if (!class_exists($builderClassName)) {
            $builderClassName = __NAMESPACE__ . '\\' . 'AttributeMetadataBuilder';
        }
        return $this->objectManager->get($builderClassName);
    }
}
