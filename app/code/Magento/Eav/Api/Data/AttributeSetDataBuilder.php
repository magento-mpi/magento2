<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Api\Data;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeSetInterface
 */
class AttributeSetDataBuilder extends \Magento\Framework\Api\Builder
{
    /**
     * @param int|null $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId)
    {
        $this->_set('attribute_set_id', $attributeSetId);
        return $this;
    }

    /**
     * @param string $attributeSetName
     * @return $this
     */
    public function setAttributeSetName($attributeSetName)
    {
        $this->_set('attribute_set_name', $attributeSetName);
        return $this;
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->_set('sort_order', $sortOrder);
        return $this;
    }

    /**
     * @param int|null $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->_set('entity_type_id', $entityTypeId);
        return $this;
    }

    public function __construct(
        ObjectFactory $objectFactory,
        MetadataServiceInterface $metadataService,
        \Magento\Framework\Api\AttributeDataBuilder $attributeValueBuilder,
        \Magento\Framework\Reflection\DataObjectProcessor $objectProcessor,
        \Magento\Framework\Reflection\TypeProcessor $typeProcessor,
        \Magento\Framework\Serialization\DataBuilderFactory $dataBuilderFactory,
        \Magento\Framework\ObjectManager\Config $objectManagerConfig,
        $modelClassInterface = 'Magento\Eav\Api\Data\AttributeSetInterface'
    ) {
        parent::__construct(
            $objectFactory, $metadataService, $attributeValueBuilder, $objectProcessor,
            $typeProcessor, $dataBuilderFactory, $objectManagerConfig, $modelClassInterface
        );
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
