<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api;

/**
 * Custom Attribute Data object builder
 */
class AttributeDataBuilder extends AbstractSimpleObjectBuilder
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

    /**
     * Return the Data type class name
     *
     * @return string
     */
    protected function _getDataObjectType()
    {
        return '\Magento\Framework\Api\AttributeValue';
    }

    public function populateWithArray(array $data)
    {

    }
}
