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
    const USED_FOR_SORT_BY = 'used_for_sort_by';

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
