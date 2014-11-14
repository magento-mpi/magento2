<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Builder for AttributeSet
 *
 * @codeCoverageIgnore
 */
class AttributeSetBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set attribute set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set(AttributeSet::ID, $id);
    }

    /**
     * Set attribute set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->_set(AttributeSet::NAME, $name);
    }

    /**
     * Set attribute set sort order index
     *
     * @param int $index
     * @return $this
     */
    public function setSortOrder($index)
    {
        return $this->_set(AttributeSet::ORDER, $index);
    }
}
