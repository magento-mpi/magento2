<?php
/**
 * Product Media Attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

use \Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class GalleryEntry extends AbstractExtensibleObject
{
    const ID = 'id';
    const LABEL = 'label';
    const POSITION = 'position';
    const DISABLED = 'disabled';
    const TYPES = 'types';
    const FILE = 'file';

    /**
     * Retrieve gallery entry ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Retrieve gallery entry alternative text
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Retrieve gallery entry position (sort order)
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Check if gallery entry is hidden from product page
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->_get(self::DISABLED);
    }

    /**
     * Retrieve gallery entry image types (thumbnail, image, small_image etc)
     *
     * @return string[]|null
     */
    public function getTypes()
    {
        return $this->_get(self::TYPES);
    }

    /**
     * Get file path
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->_get(self::FILE);
    }
}
