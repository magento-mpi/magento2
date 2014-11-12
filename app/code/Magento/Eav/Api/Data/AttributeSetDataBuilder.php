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
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeSetInterface
 */
class AttributeSetDataBuilder extends \Magento\Framework\Api\CompositeExtensibleDataBuilder
{
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
     * @param string $attributeSetName
     * @return $this
     */
    public function setAttributeSetName($attributeSetName)
    {
        $this->set('attribute_set_name', $attributeSetName);
        return $this;
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->set('sort_order', $sortOrder);
        return $this;
    }

    /**
     * @param int|null $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->set('entity_type_id', $entityTypeId);
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
        parent::__construct($objectManager, $metadataService, $objectManagerConfig, 'Magento\Eav\Api\Data\AttributeSetInterface');
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
