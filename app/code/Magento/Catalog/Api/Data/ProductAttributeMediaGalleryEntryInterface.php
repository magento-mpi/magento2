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

interface ProductAttributeMediaGalleryEntryInterface
{
    const ID = 'id';
    const LABEL = 'label';
    const POSITION = 'position';
    const DISABLED = 'is_disabled';
    const TYPES = 'types';
    const FILE = 'file';

    /**
     * Retrieve gallery entry ID
     *
     * @return int
     */
    public function getId();

    /**
     * Retrieve gallery entry alternative text
     *
     * @return string
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
    public function getIsDisabled();

    /**
     * Retrieve gallery entry image types (thumbnail, image, small_image etc)
     *
     * @return string[]
     */
    public function getTypes();

    /**
     * Get file path
     *
     * @return string
     */
    public function getFile();
}
