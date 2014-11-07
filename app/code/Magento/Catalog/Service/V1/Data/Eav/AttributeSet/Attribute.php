<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\AttributeSet;

/**
 * @codeCoverageIgnore
 */
class Attribute extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**
     * table field for attribute_id
     */
    const ATTRIBUTE_ID = 'attribute_id';

    /**
     * table field for attribute_group_id
     */
    const ATTRIBUTE_GROUP_ID = 'attribute_group_id';

    /**
     * table field for sort order index
     */
    const SORT_ORDER = 'sort_order';

    /**
     * Get attribute id
     *
     * @return string
     */
    public function getAttributeId()
    {
        return $this->_get(self::ATTRIBUTE_ID);
    }

    /**
     * Get attribute id
     *
     * @return string
     */
    public function getAttributeGroupId()
    {
        return $this->_get(self::ATTRIBUTE_GROUP_ID);
    }

    /**
     * Get attribute set sort order index
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }
}
