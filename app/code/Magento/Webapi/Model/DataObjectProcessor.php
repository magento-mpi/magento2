<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;
use Magento\Framework\Service\Data\AttributeValue;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\ObjectManager;
use Magento\Webapi\Model\Config as ModelConfig;
use Magento\Webapi\Model\Config\ClassReflector\TypeProcessor;

/**
 * Data object processor for de-serialization using class reflection
 */
class DataObjectProcessor
{
    const IS_METHOD_PREFIX = 'is';
    const GETTER_PREFIX = 'get';
    
    /**
     * @var ModelConfig
     */
    protected $config;

    /** @var TypeProcessor */
    protected $typeProcessor;

    /**
     * Initialize DataObjectProcessor dependencies
     *
     * @param ModelConfig $config
     * @param TypeProcessor $typeProcessor
     */
    public function __construct(ModelConfig $config, TypeProcessor $typeProcessor)
    {
        $this->config = $config;
        $this->typeProcessor = $typeProcessor;
    }

    /**
     * Use class reflection on given data interface to build output data array
     *
     * @param mixed $dataObject
     * @param string $dataObjectType
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function buildOutputDataArray($dataObject, $dataObjectType)
    {
        $methods = $this->config->getDataInterfaceMethods($dataObjectType);
        $outputData = [];

        /** @var MethodReflection $method */
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
                        $value = $this->buildOutputDataArray($value, $this->getMethodReturnType($method));
                    } else if (is_array($value)) {
                        $valueResult = array();
                        foreach ($value as $singleValue) {
                            if (is_object($singleValue)) {
                                $singleValue = $this->buildOutputDataArray(
                                    $singleValue,
                                    $this->getMethodReturnType($method)
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
     * @param MethodReflection $methodReflection
     * @return string
     */
    public function getMethodReturnType($methodReflection)
    {
        return str_replace('[]', '', $this->typeProcessor->getGetterReturnType($methodReflection)['type']);
    }

    /**
     * Convert array of custom_attributes to use flat array structure
     *
     * @param \Magento\Framework\Api\AttributeInterface[] $customAttributes
     * @return array
     */
    protected function convertCustomAttributes($customAttributes)
    {
        $result = array();
        foreach ((array)$customAttributes as $customAttribute) {
            $result[] = $this->convertCustomAttribute($customAttribute);
        }
        return $result;
    }

    /**
     * Convert custom_attribute object to use flat array structure
     *
     * @param \Magento\Framework\Api\AttributeInterface $customAttribute
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
