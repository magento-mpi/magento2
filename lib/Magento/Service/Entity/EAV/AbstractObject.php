<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity\EAV;

/**
 * Class EAV AbstractObject
 */
abstract class AbstractObject extends \Magento\Service\Entity\AbstractObject
{
    /**
     * Array key for custom attributes
     */
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return int|string|bool|float The attribute value. Null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        if (isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            && array_key_exists($attributeCode, $this->_data[self::CUSTOM_ATTRIBUTES_KEY])
        ) {
            return $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode];
        } else {
            return null;
        }
    }

    /**
     * Retrieve custom attributes values as an associative array.
     *
     * @return string[]
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY]
            : [];
    }
}
