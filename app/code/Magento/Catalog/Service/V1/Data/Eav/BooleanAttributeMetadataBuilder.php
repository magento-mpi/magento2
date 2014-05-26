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
 * Class BooleanAttributeMetadataBuilder
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class BooleanAttributeMetadataBuilder extends AttributeMetadataBuilder
{
    /**
     * Set whether it is used for sorting in product listing
     *
     * @param  bool $usedForSortBy
     * @return bool
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return (bool)$this->_set(BooleanAttributeMetadata::USED_FOR_SORT_BY, (bool)$usedForSortBy);
    }
}
