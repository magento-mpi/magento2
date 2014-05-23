<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\AttributeSet;

class Attribute extends \Magento\Framework\Service\Data\AbstractObject
{
    /**
     * table field for attribute_set_id
     */
    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    /**
     * table field for attribute_id
     */
    const ATTRIBUTE_ID = 'attribute_id';

    /**
     * table field for attribute_group_id
     */
    const ATTRIBUTE_GROUP_ID = 'attribute_group_id';

    /**
     * table field for entity_type_id
     */
    const ENTITY_TYPE_ID = 'entity_type_id';

    /**
     * table field for sort order index
     */
    const SORT_ORDER = 'sort_order';

    /**
     * Get attribute set id
     *
     * @return int
     */
    public function getAttributeSetId()
    {
        return $this->_get(self::ATTRIBUTE_SET_ID);
    }

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
     * Get attribute entity type id
     *
     * @return string
     */
    public function getEntityTypeId()
    {
        return $this->_get(self::ENTITY_TYPE_ID);
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
