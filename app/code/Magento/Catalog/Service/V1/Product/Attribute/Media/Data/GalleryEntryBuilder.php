<?php
/**
 * Builder for media attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

use Magento\Framework\Api\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class GalleryEntryBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set gallery entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setId($entityId)
    {
        return $this->_set(GalleryEntry::ID, $entityId);
    }

    /**
     * Set media alternative text
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(GalleryEntry::LABEL, $label);
    }

    /**
     * Set gallery entity position (sort order)
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(GalleryEntry::POSITION, $position);
    }

    /**
     * Set disabled flag that shows if gallery entity is hidden from product page
     *
     * @param bool $isDisabled
     * @return $this
     */
    public function setDisabled($isDisabled)
    {
        return $this->_set(GalleryEntry::DISABLED, $isDisabled);
    }

    /**
     * Set gallery entry types (thumbnail, image, small_image etc)
     *
     * @param array $roles
     * @return $this
     */
    public function setTypes(array $roles)
    {
        return $this->_set(GalleryEntry::TYPES, $roles);
    }

    /**
     * Set file path
     *
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        return $this->_set(GalleryEntry::FILE, $file);
    }
}
