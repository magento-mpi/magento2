<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class SelectAttributeMetadata
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class SelectAttributeMetadata extends AttributeMetadata
{
    /**
     * Whether it used in layered navigation
     *
     * @return bool
     */
    public function getIsFilterable()
    {
        return (bool)$this->_get(AttributeMetadata::IS_FILTERABLE);
    }

    /**
     * Whether it is used in search results layered navigation
     *
     * @return bool
     */
    public function getIsFilterableInSearch()
    {
        return (bool)$this->_get(AttributeMetadata::IS_FILTERABLE_IN_SEARCH);
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->_get(AttributeMetadata::POSITION);
    }

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool
     */
    public function getUsedForSortBy()
    {
        return (bool)$this->_get(AttributeMetadata::USED_FOR_SORT_BY);
    }
}
