<?php
/**
 * Product Media Attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * @todo implement this interface as \Magento\Catalog\Model\Product\Attribute\Media\GalleryEntry.
 * Move logic from service there.
 */
interface ProductAttributeMediaGalleryEntryInterface
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
    public function getId();

    /**
     * Retrieve gallery entry alternative text
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Retrieve gallery entry position (sort order)
     *
     * @return int
     */
    public function getPosition();

    /**
     * Check if gallery entry is hidden from product page
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Retrieve gallery entry image types (thumbnail, image, small_image etc)
     *
     * @return string[]|null
     */
    public function getTypes();

    /**
     * Get file path
     *
     * @return string|null
     */
    public function getFile();
}
