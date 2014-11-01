<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * @codeCoverageIgnore
 */
class AttributeGroupBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * Set Id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->_set(AttributeGroup::KEY_ID, $id);
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->_set(AttributeGroup::KEY_NAME, $name);
        return $this;
    }
}
