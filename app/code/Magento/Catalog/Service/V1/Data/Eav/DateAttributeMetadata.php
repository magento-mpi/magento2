<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class DateAttributeMetadata
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class DateAttributeMetadata extends AttributeMetadata
{
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
