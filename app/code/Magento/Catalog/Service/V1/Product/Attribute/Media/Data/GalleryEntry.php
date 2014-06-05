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

use \Magento\Framework\Service\Data\AbstractObject;

class GalleryEntry extends AbstractObject
{
    const ID = 'id';
    const LABEL = 'label';
    const STORE_ID = 'store_id';
    const POSITION = 'position';
    const DISABLED = 'disabled';
    const ROLES = 'roles';

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
     * Retrieve store ID
     *
     * @todo maybe remove this
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
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
     * Retrieve gallery entry roles (thumbnail, image, small_image etc)
     *
     * @return string[]|null
     */
    public function getRoles()
    {
        // @todo maybe change the name
        return $this->_get(self::ROLES);
    }
}
