<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Builder for AttributeSetExtended
 */
class AttributeSetExtendedBuilder extends AttributeSetBuilder
{
    /**
     * Set attbibute set parent(skeleton) id
     *
     * @param int $skeletonId
     * @return $this
     */
    public function setSkeletonId($skeletonId)
    {
        return $this->_set(AttributeSetExtended::SKELETON_ID, $skeletonId);
    }
}
