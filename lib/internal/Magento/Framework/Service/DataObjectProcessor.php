<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service;

use Zend\Code\Reflection\ClassReflection;
use Magento\Framework\Service\Data\AttributeValue;

class DataObjectProcessor
{
    /**
     * Use class reflection on given data interface to build output data array
     *
     * @param mixed $dataObject
     * @param string $dataObjectType
     * @return array
     */
    public function buildOutputDataArray($dataObject, $dataObjectType)
    {
        $class = new ClassReflection($dataObjectType);
        $methods = $class->getMethods();

        $outputData = [];
        foreach ($methods as $method) {
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }
            if (substr($method->getName(), 0, 2) === 'is') {
                $outputData[$this->_fieldNameConverter(substr($method->getName(), 2))]
                    = $dataObject->{$method->getName()}();

            } elseif (substr($method->getName(), 0, 3) === 'get') {
                $key = $this->_fieldNameConverter(substr($method->getName(), 3));
                $value = $dataObject->{$method->getName()}();
                if ($key === 'custom_attributes') {
                    $value = $this->_customAttributesConverter($value);
                }
                $outputData[$key] = $value;
            }
        }
        return $outputData;
    }

    /**
     * Converts field names to use lowercase
     *
     * @param string $name
     * @return string
     */
    protected function _fieldNameConverter($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }

    protected function _customAttributesConverter($customAttributes)
    {
        $result = array();
        if (is_array($customAttributes)) {
            foreach ($customAttributes as $customAttribute) {
                $result[] = $this->_flatArrayConverter($customAttribute);
            }
        } else {
            $result[] = $this->_flatArrayConverter($customAttributes);
        }
        return $result;
    }

    protected function _flatArrayConverter($customAttribute)
    {
        $data = array();
        $data[AttributeValue::ATTRIBUTE_CODE] = $customAttribute->getAttributeCode();
        $value = $customAttribute->getValue();
        if (is_object($value)) {
            $value = $this->buildOutputDataArray($value, get_class($value)); // attribute_value can be any data object
        }
        $data[AttributeValue::VALUE] = $value;
        return $data;
    }
}
