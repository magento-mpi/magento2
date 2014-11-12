<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Api\Data;
use Magento\Framework\Api\CompositeExtensibleDataBuilder;

/**
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeGroupInterface
 */
class AttributeGroupDataBuilder extends \Magento\Framework\Api\CompositeExtensibleDataBuilder
{
    /**
     * @param string|null $attributeGroupId
     * @return $this
     */
    public function setAttributeGroupId($attributeGroupId)
    {
        $this->set('attribute_group_id', $attributeGroupId);
        return $this;
    }

    /**
     * @param string|null $attributeGroupName
     * @return $this
     */
    public function setAttributeGroupName($attributeGroupName)
    {
        $this->set('attribute_group_name', $attributeGroupName);
        return $this;
    }

    /**
     * @param int|null $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId)
    {
        $this->set('attribute_set_id', $attributeSetId);
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Api\MetadataServiceInterface $metadataService
     * @param \Magento\Framework\ObjectManager\Config $objectManagerConfig
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, \Magento\Framework\Api\MetadataServiceInterface $metadataService, \Magento\Framework\ObjectManager\Config $objectManagerConfig)
    {
        parent::__construct($objectManager, $metadataService, $objectManagerConfig, 'Magento\Eav\Api\Data\AttributeGroupInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        /** TODO: temporary fix while problem with hasDataChanges flag not solved. MAGETWO-30324 */
        $object = parent::create();
        $object->setDataChanges(true);
        return $object;
    }
}
