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
use Magento\Framework\Service\SimpleDataObjectConverter;
use Magento\Framework\Service\Data\AttributeValue;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\ObjectManager;
use Magento\Webapi\Model\Config\ClassReflector\TypeProcessor;
use Magento\Webapi\Model\Cache\Type as WebapiCache;

/**
 * Data object processor for de-serialization using class reflection
 */
class DataObjectProcessor
{
    const IS_METHOD_PREFIX = 'is';
    const GETTER_PREFIX = 'get';
    const DATA_INTERFACE_METHODS_CACHE_PREFIX = 'dataInterfaceMethods';

    /**
     * @var WebapiCache
     */
    protected $cache;

    /**
     * @var TypeProcessor
     */
    protected $typeProcessor;

    /**
     * @var array
     */
    protected $dataInterfaceMethodsMap = [];

    /**
     * Initialize dependencies.
     *
     * @param WebapiCache $cache
     * @param TypeProcessor $typeProcessor
     */
    public function __construct(WebapiCache $cache, TypeProcessor $typeProcessor)
    {
        $this->cache = $cache;
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
        $methods = $this->getDataInterfaceMethods($dataObjectType);
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
                    $outputData[SimpleDataObjectConverter::camelCaseToSnakeCase(substr($methodName, 2))] = $value;
                }
            } else if (substr($methodName, 0, 3) === self::GETTER_PREFIX) {
                $value = $dataObject->{$methodName}();
                if ($value !== null) {
                    $key = SimpleDataObjectConverter::camelCaseToSnakeCase(substr($methodName, 3));
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
            $value = $this->buildOutputDataArray($value, get_class($value));
        }
        $data[AttributeValue::VALUE] = $value;
        return $data;
    }

    /**
     * Return data interface methods loaded from cache
     *
     * @param string $dataInterfaceName
     * @return array
     */
    protected function getDataInterfaceMethods($dataInterfaceName)
    {
        $key = self::DATA_INTERFACE_METHODS_CACHE_PREFIX . "-" . md5($dataInterfaceName);
        if (!isset($this->dataInterfaceMethodsMap[$key])) {
            $methods = $this->cache->load($key);
            if ($methods) {
                $this->dataInterfaceMethodsMap[$key] = unserialize($methods);
            } else {
                $class = new ClassReflection($dataInterfaceName);
                $this->dataInterfaceMethodsMap[$key] = $class->getMethods();
                $this->cache->save(serialize($this->dataInterfaceMethodsMap[$key]), $key);
            }
        }
        return $this->dataInterfaceMethodsMap[$key];
    }
}
