<?php
/**
 * Builder for the Eav Attribute Service Data Object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

use Magento\Customer\Service\V1\Data\Eav\Attribute;

class AttributeBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string $attributeCode
     * @return AttributeBuilder
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(Attribute::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * @param string $value
     * @return AttributeBuilder
     */
    public function setValue($value)
    {
        return $this->_set(Attribute::VALUE, $value);
    }
}
