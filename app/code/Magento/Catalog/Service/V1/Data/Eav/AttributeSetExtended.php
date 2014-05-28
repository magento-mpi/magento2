<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Contains basic attribute set data and skeleton set id
 */
class AttributeSetExtended extends AttributeSet
{
    /**
     * Id of basic attribute set
     */
    const SKELETON_ID = 'skeleton_id';

    /**
     * Get skeleton(basic) attribute set id
     *
     * @return int
     */
    public function getSkeletonId()
    {
        return $this->_get(self::SKELETON_ID);
    }
}
