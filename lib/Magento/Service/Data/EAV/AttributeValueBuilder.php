<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Data\Eav;

/**
 * Class AttributeBuilder
 */
class AttributeValueBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * Set attribute code
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(AttributeValue::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Set attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(AttributeValue::VALUE, $value);
    }
}
