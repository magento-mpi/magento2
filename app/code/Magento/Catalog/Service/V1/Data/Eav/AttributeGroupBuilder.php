<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Catalog\Service\V1\Data\Eav\AttributeGroup;

class AttributeGroupBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
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
