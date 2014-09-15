<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Data;

/**
 * Custom Attribute Data object
 */
class AttributeValue extends AbstractSimpleObject
{
    /**#@+
     * Constant used as key into $_data
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Get attribute code
     *
     * @return string the attribute code
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * Get attribute value
     *
     * @return mixed the attribute value
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
