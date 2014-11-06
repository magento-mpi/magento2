<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

/**
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeSetInterface
 *
 * @todo this utility class can be removed after API framework is able to generate builders
 * taking field mapping into account
 */
class AttributeSetInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param int $id
     * @return AttributeSetInterfaceDataBuilder
     */
    public function setId($id)
    {
        $this->data['attribute_set_id'] = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return AttributeSetInterfaceDataBuilder
     */
    public function setName($name)
    {
        $this->data['attribute_set_name'] = $name;
        return $this;
    }

    /**
     * @param int $sortOrder
     * @return AttributeSetInterfaceDataBuilder
     */
    public function setSortOrder($sortOrder)
    {
        $this->data['sort_order'] = $sortOrder;
        return $this;
    }

    /**
     * @param int $entityTypeId
     * @return AttributeSetInterfaceDataBuilder
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->data['entity_type_id'] = $entityTypeId;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Eav\Api\Data\AttributeSetInterface');
    }
}
