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
use Magento\Framework\Model\AbstractExtensibleModel;

class DataObjectProcessor
{
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
                    $outputData[$this->camelCaseToSnakeCase(substr($methodName, 2))] = $value;
                }
            } else if (substr($methodName, 0, 3) === self::GETTER_PREFIX) {
                $value = $dataObject->{$methodName}();
                if ($value !== null) {
                    $key = $this->camelCaseToSnakeCase(substr($methodName, 3));
                    if ($key === AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY) {
                        $value = $this->convertCustomAttributes($value);
                    } else if (is_object($value)) {
                        $value = $this->buildOutputDataArray($value, $this->getMethodReturnType($class, $methodName));
                    } else if (is_array($value)) {
                        $valueResult = array();
                        foreach ($value as $singleValue) {
                            if (is_object($singleValue)) {
                                $singleValue = $this->buildOutputDataArray(
                                    $singleValue,
                                    $this->getMethodReturnType($class, $methodName)
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
     * Convert a CamelCase string read from method into field key in snake_case
     *
     * e.g. DefaultShipping => default_shipping
     *      Postcode => postcode
     *
     * @param string $name
     * @return string
     */
    protected function camelCaseToSnakeCase($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }

    /**
     * Get return type by reading the DocBlock of the given method
     *
     * @param ClassReflection $class
     * @param string $methodName
     * @return string
     */
    protected function getMethodReturnType($class, $methodName)
    {
        $dataObjectType = $class->getMethod($methodName)->getDocBlock()->getTag('return')->getType();
        if (strpos($dataObjectType, '|null') !== false) {
            $dataObjectType = str_replace('|null', '', $dataObjectType);
        }
        return $dataObjectType;
    }

    /**
     * Convert array of custom_attributes to use flat array structure
     *
     * @param \Magento\Framework\Service\Data\AttributeValue[] $customAttributes
     * @return array
     */
    protected function convertCustomAttributes($customAttributes)
    {
        $result = array();
        if (is_array($customAttributes)) {
            foreach ($customAttributes as $customAttribute) {
                $result[] = $this->convertCustomAttribute($customAttribute);
            }
        }
        return $result;
    }

    /**
     * Convert custom_attribute object to use flat array structure
     *
     * @param \Magento\Framework\Service\Data\AttributeValue $customAttribute
     * @return array
     */
    protected function convertCustomAttribute($customAttribute)
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
