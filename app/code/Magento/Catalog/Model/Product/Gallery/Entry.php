<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Gallery;

class Entry extends \Magento\Framework\Service\Data\AbstractExtensibleObject
    implements \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface
{
    /**
     * Retrieve gallery entry ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Retrieve gallery entry alternative text
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * Retrieve gallery entry position (sort order)
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->getData('position');
    }

    /**
     * Check if gallery entry is hidden from product page
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getData('is_disabled');
    }

    /**
     * Retrieve gallery entry image types (thumbnail, image, small_image etc)
     *
     * @return string[]|null
     */
    public function getTypes()
    {
        return $this->getData('types');
    }

    /**
     * Get file path
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->getData('file');
    }
} 
