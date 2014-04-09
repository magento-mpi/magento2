<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Data\EAV;

/**
 * Class EAV AbstractObject
 */
abstract class AbstractObject extends \Magento\Service\Data\AbstractObject
{
    /**
     * Array key for custom attributes
     */
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Service\Data\Eav\AttributeValue|null The attribute value. Null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        if (isset(
            $this->_data[self::CUSTOM_ATTRIBUTES_KEY]
        ) && array_key_exists(
            $attributeCode,
            $this->_data[self::CUSTOM_ATTRIBUTES_KEY]
        )
        ) {
            return $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode];
        } else {
            return null;
        }
    }

    /**
     * Retrieve custom attributes values as an associative array.
     *
     * @return \Magento\Service\Data\Eav\AttributeValue[]|null
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY]) ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY] : array();
    }

    /**
     * Return Data Object data in array format.
     *
     * @return array
     */
    public function __toArray()
    {
        if (!isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])) {
            return parent::__toArray();
        }
        $customAttributesValues = [];
        /** @var AttributeValue[] $customAttributes */
        $customAttributes = $this->_data[self::CUSTOM_ATTRIBUTES_KEY];
        foreach ($customAttributes as $attributeCode => $attributeValue) {
            $customAttributesValues[$attributeCode] = $attributeValue->getValue();
        }
        unset ($this->_data[self::CUSTOM_ATTRIBUTES_KEY]);
        $data = parent::__toArray();
        $data[self::CUSTOM_ATTRIBUTES_KEY] = $customAttributesValues;
        $this->_data[self::CUSTOM_ATTRIBUTES_KEY] = $customAttributes;
        return $data;
    }
}
