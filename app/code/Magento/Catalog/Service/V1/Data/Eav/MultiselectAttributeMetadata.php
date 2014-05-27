<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class MultiselectAttributeMetadata
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class MultiselectAttributeMetadata extends AttributeMetadata
{
    const IS_FILTERABLE = 'is_filterable';

    const IS_FILTERABLE_IN_SEARCH = 'is_filterable_in_search';

    const POSITION = 'position';

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
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->_get(self::POSITION);
    }
}
