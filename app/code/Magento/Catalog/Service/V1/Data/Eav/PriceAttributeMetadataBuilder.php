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
     * @return $this
     */
    public function setIsFilterable($isFilterable)
    {
        return $this->_set(PriceAttributeMetadata::IS_FILTERABLE, (bool)$isFilterable);
    }

    /**
     * Set whether it is used in search results layered navigation
     *
     * @param  bool $isFilterableInSearch
     * @return $this
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        return $this->_set(PriceAttributeMetadata::IS_FILTERABLE_IN_SEARCH, (bool)$isFilterableInSearch);
    }

    /**
     * Set position
     *
     * @param  int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(PriceAttributeMetadata::POSITION, (int)$position);
    }

    /**
     * Set whether it is used for sorting in product listing
     *
     * @param  bool $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return $this->_set(PriceAttributeMetadata::USED_FOR_SORT_BY, (bool)$usedForSortBy);
    }
}
