<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;
use Magento\Framework\Api\ExtensibleDataBuilder;

/**
 * DataBuilder class for
 * \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface
 */
class ProductAttributeMediaGalleryEntryInterfaceDataBuilder extends \Magento\Framework\Api\ExtensibleDataBuilder
{
    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface');
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * @param string|null $label
     */
    public function setLabel($label)
    {
        $this->data['label'] = $label;
        return $this;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * @param bool $isDisabled
     */
    public function setIsDisabled($isDisabled)
    {
        $this->data['disabled'] = $isDisabled;
        return $this;
    }

    /**
     * @param string $types
     */
    public function setTypes($types)
    {
        $this->data['types'] = $types;
        return $this;
    }

    /**
     * @param string|null $file
     */
    public function setFile($file)
    {
        $this->data['file'] = $file;
        return $this;
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
