<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;
use Magento\Framework\Service\Data\ExtensibleDataBuilder;

/**
 * DataBuilder class for \Magento\Catalog\Api\Data\ProductAttributeTypeInterface
 */
class ProductAttributeTypeInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->data['label'] = $label;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductAttributeTypeInterface');
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
