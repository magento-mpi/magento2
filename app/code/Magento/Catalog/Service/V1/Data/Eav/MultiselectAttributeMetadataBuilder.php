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
 * Class MultiselectAttributeMetadataBuilder
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class MultiselectAttributeMetadataBuilder extends AttributeMetadataBuilder
{
    /**
     * Set whether it used in layered navigation
     *
     * @param  bool $isFilterable
     * @return $this|bool
     */
    public function setIsFilterable($isFilterable)
    {
        return (bool)$this->_set(MultiselectAttributeMetadata::IS_FILTERABLE, (bool)$isFilterable);
    }

    /**
     * Set whether it is used in search results layered navigation
     *
     * @param  bool $isFilterableInSearch
     * @return bool
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        return (bool)$this->_set(MultiselectAttributeMetadata::IS_FILTERABLE_IN_SEARCH, (bool)$isFilterableInSearch);
    }

    /**
     * Set position
     *
     * @param  int $position
     * @return $this|bool
     */
    public function setPosition($position)
    {
        return (bool)$this->_set(MultiselectAttributeMetadata::POSITION, (int)$position);
    }
}
