<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class PriceAttributeMetadata
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class PriceAttributeMetadata extends AttributeMetadata
{
    /**#@+
     * Constants used as keys into $_data
     */
    const IS_FILTERABLE = 'is_filterable';

    const IS_FILTERABLE_IN_SEARCH = 'is_filterable_in_search';

    const POSITION = 'position';

    const USED_FOR_SORT_BY = 'used_for_sort_by';
    /**#@-*/

    /**
     * Whether it used in layered navigation
     *
     * @return bool
     */
    public function getIsFilterable()
    {
        return (bool)$this->_get(self::IS_FILTERABLE);
    }

    /**
     * Whether it is used in search results layered navigation
     *
     * @return bool
     */
    public function getIsFilterableInSearch()
    {
        return (bool)$this->_get(self::IS_FILTERABLE_IN_SEARCH);
    }

    /**
     * Get position
     *
     * @return bool
     */
    public function getPosition()
    {
        return (bool)$this->_get(self::POSITION);
    }

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool
     */
    public function getUsedForSortBy()
    {
        return (bool)$this->_get(self::USED_FOR_SORT_BY);
    }
}
