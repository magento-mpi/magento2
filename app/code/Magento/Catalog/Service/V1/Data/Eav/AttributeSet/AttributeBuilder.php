<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\AttributeSet;

class AttributeBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set attribute set id
     *
     * @param int $id
     * @return $this
     */
    public function setAttributeSetId($id)
    {
        return $this->_set(Attribute::ATTRIBUTE_SET_ID, $id);
    }

    /**
     * Set attribute group id
     *
     * @param string $id
     * @return $this
     */
    public function setAttributeGroupId($id)
    {
        return $this->_set(Attribute::ATTRIBUTE_GROUP_ID, $id);
    }

    /**
     * Get attribute id
     *
     * @param string $id
     * @return $this
     */
    public function setAttributeId($id)
    {
        return $this->_set(Attribute::ATTRIBUTE_ID, $id);
    }

    /**
     * Get attribute entity type id
     *
     * @param string $id
     * @return $this
     */
    public function setEntityTypeId($id)
    {
        return $this->_set(Attribute::ENTITY_TYPE_ID, $id);
    }


    /**
     * Set attribute set sort order index
     *
     * @param int $index
     * @return $this
     */
    public function setSortOrder($index)
    {
        return $this->_set(Attribute::SORT_ORDER, $index);
    }
}
