<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Base Class for extensible data Objects
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractExtensibleObject extends AbstractSimpleObject implements ExtensibleDataInterface
{
    /**
     * Array key for custom attributes
     */
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Api\AttributeValue|null null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES])
            && isset($this->_data[self::CUSTOM_ATTRIBUTES][$attributeCode])
                ? $this->_data[self::CUSTOM_ATTRIBUTES][$attributeCode]
                : null;
    }

    /**
     * Retrieve custom attributes values.
     *
     * @return \Magento\Framework\Api\AttributeValue[]|null
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES]) ? $this->_data[self::CUSTOM_ATTRIBUTES] : [];
    }
}
