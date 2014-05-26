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
 * Class DateAttributeMetadataBuilder
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class DateAttributeMetadataBuilder extends AttributeMetadataBuilder
{
    /**
     * Set whether it is used for sorting in product listing
     *
     * @param  bool $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return $this->_set(DateAttributeMetadata::USED_FOR_SORT_BY, (bool)$usedForSortBy);
    }
}
