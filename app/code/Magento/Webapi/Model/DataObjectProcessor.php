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
    const SERVICE_INTERFACE_METHODS_CACHE_PREFIX = 'serviceInterfaceMethodsMap';

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
     * @var array
     */
    protected $serviceInterfaceMethodsMap = [];

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
        $methods = $this->getMethodsMap($dataObjectType);
        $outputData = [];

        /** @var MethodReflection $method */
        foreach ($methods as $methodName => $returnType) {
            if (substr($methodName, 0, 2) === self::IS_METHOD_PREFIX) {
                $value = $dataObject->{$methodName}();
                if ($value !== null) {
                    $outputData[SimpleDataObjectConverter::camelCaseToSnakeCase(substr($methodName, 2))] = $value;
                }
            } else if (substr($methodName, 0, 3) === self::GETTER_PREFIX &&
                $methodName !== 'getCustomAttribute') {
                $value = $dataObject->{$methodName}();
                if ($value !== null) {
                    $key = SimpleDataObjectConverter::camelCaseToSnakeCase(substr($methodName, 3));
                    if ($key === AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY) {
                        $value = $this->convertCustomAttributes($value);
                    } else if (is_object($value)) {
                        $value = $this->buildOutputDataArray($value, $returnType);
                    } else if (is_array($value)) {
                        $valueResult = array();
                        foreach ($value as $singleValue) {
                            if (is_object($singleValue)) {
                                $singleValue = $this->buildOutputDataArray($singleValue, $returnType);
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
     * Get return type by interface name and method
     *
     * @param string $interfaceName
     * @param string $methodName
     * @return string
     */
    public function getMethodReturnType($interfaceName, $methodName)
    {
        return $this->getMethodsMap($interfaceName)[$methodName];
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
     * Return service interface or Data interface methods loaded from cache
     *
     * @param string $interfaceName
     * @return array
     * <pre>
     * Service methods' reflection data stored in cache as 'methodName' => 'returnType'
     * ex.
     * [
     *  'create' => '\Magento\Customer\Api\Data\Customer',
     *  'validatePassword' => 'boolean'
     * ]
     * </pre>
     */
    public function getMethodsMap($interfaceName)
    {
        $key = self::SERVICE_INTERFACE_METHODS_CACHE_PREFIX . "-" . md5($interfaceName);
        if (!isset($this->serviceInterfaceMethodsMap[$key])) {
            $methodMap = $this->cache->load($key);
            if ($methodMap) {
                $this->serviceInterfaceMethodsMap[$key] = unserialize($methodMap);
            } else {
                $methodMap = [];
                $class = new ClassReflection($interfaceName);
                foreach ($class->getMethods() as $method) {
                    $isSuitableMethodType = !($method->isConstructor() || $method->isFinal()
                        || $method->isStatic() || $method->isDestructor());
                    $isExcludedMagicMethod = in_array($method->getName(), array('__sleep', '__wakeup', '__clone'));
                    $isSuitableClass = $method->class !== 'Magento\Framework\Api\ExtensibleDataInterface';
                    if ($isSuitableMethodType && !$isExcludedMagicMethod && $isSuitableClass) {
                        $methodMap[$method->getName()] = $this->typeProcessor->getGetterReturnType($method)['type'];
                    }
                }
                $this->serviceInterfaceMethodsMap[$key] = $methodMap;
                $this->cache->save(serialize($this->serviceInterfaceMethodsMap[$key]), $key);
            }
        }
        return $this->serviceInterfaceMethodsMap[$key];
    }
}
