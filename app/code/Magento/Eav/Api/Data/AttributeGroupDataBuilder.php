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
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeGroupInterface
 */
class AttributeGroupDataBuilder extends \Magento\Framework\Api\Builder
{
    /**
     * @param string|null $attributeGroupId
     * @return $this
     */
    public function setAttributeGroupId($attributeGroupId)
    {
        $this->_set('attribute_group_id', $attributeGroupId);
        return $this;
    }

    /**
     * @param string|null $attributeGroupName
     * @return $this
     */
    public function setAttributeGroupName($attributeGroupName)
    {
        $this->_set('attribute_group_name', $attributeGroupName);
        return $this;
    }

    /**
     * @param int|null $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId)
    {
        $this->_set('attribute_set_id', $attributeSetId);
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
        $modelClassInterface = 'Magento\Eav\Api\Data\AttributeGroupInterface'
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
