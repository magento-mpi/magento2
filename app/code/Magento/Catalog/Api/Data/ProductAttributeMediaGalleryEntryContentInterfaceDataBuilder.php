<?php
namespace Magento\Catalog\Api\Data;
use Magento\Framework\Service\Data\ExtensibleDataBuilder;

/**
 * DataBuilder class for
 * \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryContentInterface
 */
class ProductAttributeMediaGalleryEntryContentInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param string $data
     */
    public function setEntryData($data)
    {
        $this->data['data'] = $data;
        return $this;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->data['mime_type'] = $mimeType;
        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryContentInterface');
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
