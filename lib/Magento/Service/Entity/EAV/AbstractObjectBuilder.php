<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Entity\EAV;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractObjectBuilder extends \Magento\Service\Entity\AbstractObjectBuilder
{
    /**
     * Set array of custom attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function setCustomAttributes($attributes)
    {
        foreach ($attributes as $attributeCode => $attributeValue) {
            $this->setCustomAttribute($attributeCode, $attributeValue);
        }
        return $this;
    }

    /**
     * Set custom attribute value
     *
     * @param string $attributeCode
     * @param string|int|float|bool $attributeValue
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        if (in_array($attributeCode, $this->getCustomAttributesCodes())) {
            $this->_data[AbstractObject::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $attributeValue;
        }
        return $this;
    }

    /**
     * Template method used to configure the attribute codes for the custom attributes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        return [];
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->_data = [];
        $dataObjectMethods = get_class_methods($this->_getDataObjectType());
        $customAttributesCodes = $this->getCustomAttributesCodes();
        foreach ($data as $key => $value) {
            /* First, verify is there any getter for the key on the Service Data Object */
            $possibleMethods = ['get' . $this->_snakeCaseToCamelCase($key), 'is' . $this->_snakeCaseToCamelCase($key)];
            if (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->_data[$key] = $value;
            } elseif (in_array($key, $customAttributesCodes)) {
                /* If key corresponds to custom attribute code, populate custom attributes */
                $this->_data[AbstractObject::CUSTOM_ATTRIBUTES_KEY][$key] = $value;
            }
        }
        return $this;
    }
}