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
    const CUSTOM_ATTRIBUTE_CODE = 'custom_attributes';
    const IS_METHOD_PREFIX = 'is';
    const GETTER_PREFIX = 'get';

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
            $methodName = $method->getName();
            if (substr($methodName, 0, 2) === self::IS_METHOD_PREFIX) {
                $value = $dataObject->{$methodName}();
                if ($value !== null) {
                    $outputData[$this->_fieldNameConverter(substr($methodName, 2))] = $value;
                }
            } elseif (substr($methodName, 0, 3) === self::GETTER_PREFIX) {
                $value = $dataObject->{$methodName}();
                if ($value !== null) {
                    $key = $this->_fieldNameConverter(substr($methodName, 3));
                    if ($key === self::CUSTOM_ATTRIBUTE_CODE) {
                        $value = $this->_customAttributesConverter($value);
                    } elseif (is_object(($value))) {
                        $value = $this->buildOutputDataArray($value, $this->_dataObjectTypeHelper($class, $methodName));
                    } elseif (is_array($value)) {
                        $valueResult = array();
                        foreach ($value as $singleValue) {
                            if (is_object($singleValue)) {
                                $singleValue = $this->buildOutputDataArray(
                                    $singleValue,
                                    $this->_dataObjectTypeHelper($class, $methodName)
                                );
                            }
                            $valueResult[] = $singleValue;
                        }
                        $value = $valueResult;
                    }
                    $outputData[$key] = $value;
                }
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

    /**
     * Helper to get data object type by reading the DocBlock of given method
     *
     * @param ClassReflection $class
     * @param string $methodName
     * @return string
     */
    protected function _dataObjectTypeHelper($class, $methodName)
    {
        $dataObjectType = $class->getMethod($methodName)->getDocBlock()->getTag('return')->getType();
        if (strpos($dataObjectType, '|null') !== false) {
            $dataObjectType = str_replace('|null', '', $dataObjectType);
        }
        return $dataObjectType;
    }

    /**
     * Converter for array of custom_attributes
     *
     * @param mixed $customAttributes
     * @return array
     */
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

    /**
     * Helper method for _customAttributesConverter
     *
     * @param mixed $customAttribute
     * @return array
     */
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
