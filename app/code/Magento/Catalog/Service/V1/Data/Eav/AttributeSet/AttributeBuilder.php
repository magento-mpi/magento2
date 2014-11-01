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
class AttributeBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
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
