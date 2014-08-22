<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Data;

/**
 * Base Class for extensible data Objects
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractExtensibleObject extends AbstractSimpleObject
{
    /**
     * Array key for custom attributes
     */
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Service\Data\AttributeValue|null The value. Null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            && isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode])
                ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode]
                : null;
    }

    /**
     * Retrieve custom attributes values as an associative array.
     *
     * @return \Magento\Framework\Service\Data\AttributeValue[]|null
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY]) ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY] : array();
    }
}
