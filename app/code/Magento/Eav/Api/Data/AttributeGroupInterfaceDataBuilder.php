<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;
use Magento\Framework\Api\ExtensibleDataBuilder;

/**
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeGroupInterface
 */
class AttributeGroupInterfaceDataBuilder extends \Magento\Framework\Api\ExtensibleDataBuilder
{
    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->data['attribute_group_id'] = $id;
        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->data['attribute_group_name'] = $name;
        return $this;
    }

    /**
     * @param int $attributeSetId
     */
    public function setAttributeSetId($attributeSetId)
    {
        $this->data['attribute_set_id'] = $attributeSetId;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Eav\Api\Data\AttributeGroupInterface');
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }
}
