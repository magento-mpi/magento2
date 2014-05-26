<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Class PriceAttributeMetadataBuilder
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class PriceAttributeMetadataBuilder extends AttributeMetadataBuilder
{
    /**
     * Set whether it used in layered navigation
     *
     * @param  bool $isFilterable
     * @return $this|bool
     */
    public function setIsFilterable($isFilterable)
    {
        return (bool)$this->_set(PriceAttributeMetadata::IS_FILTERABLE, $isFilterable);
    }

    /**
     * Set whether it is used in search results layered navigation
     *
     * @param  bool $isFilterableInSearch
     * @return bool
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        return (bool)$this->_set(PriceAttributeMetadata::IS_FILTERABLE_IN_SEARCH, $isFilterableInSearch);
    }

    /**
     * Set position
     *
     * @param  int $position
     * @return $this|bool
     */
    public function setPosition($position)
    {
        return (bool)$this->_set(PriceAttributeMetadata::POSITION, (int)$position);
    }

    /**
     * Set whether it is used for sorting in product listing
     *
     * @param  bool $usedForSortBy
     * @return bool
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return (bool)$this->_set(PriceAttributeMetadata::USED_FOR_SORT_BY, (bool)$usedForSortBy);
    }
}
