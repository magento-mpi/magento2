<?php
namespace Magento\Eav\Api\Data;
use Magento\Framework\Service\Data\ExtensibleDataBuilder;

/**
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeFrontendLabelInterface
 */
class AttributeFrontendLabelInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->data['label'] = $label;
        return $this;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Eav\Api\Data\AttributeFrontendLabelInterface');
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
