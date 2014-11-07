<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

/**
 * Interface AttributeGroupInterface must be implemented in \Magento\Eav\Model\Entity\Attribute\Group
 */
interface AttributeGroupInterface
{
    const GROUP_ID = 'attribute_group_id';

    const GROUP_NAME = 'attribute_group_name';

    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    /**
     * Retrieve id
     *
     * @return string
     */
    public function getAttributeGroupId();

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getAttributeGroupName();

    /**
     * Retrieve attribute set id
     *
     * @return int
     */
    public function getAttributeSetId();
}
