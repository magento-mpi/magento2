<?php
/**
 * Service Args Serializer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Controller;

use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;
use Zend\Code\Reflection\ParameterReflection;
use Magento\ObjectManager;
use Magento\Webapi\Model\Config\ClassReflector\TypeProcessor;
use Magento\Webapi\Model\Soap\Wsdl\ComplexTypeStrategy;

class ServiceArgsSerializer
{
    /** @var \Magento\Webapi\Model\Config\ClassReflector\TypeProcessor */
    protected $_typeProcessor;

    /** @var ObjectManager */
    protected $_objectManager;

    /**
     * Initialize dependencies.
     *
     * @param TypeProcessor $typeProcessor
     * @param ObjectManager $objectManager
     */
    public function __construct(
        TypeProcessor $typeProcessor,
        ObjectManager $objectManager
    ) {
        $this->_typeProcessor = $typeProcessor;
        $this->_objectManager = $objectManager;
    }

    /**
     * Converts the provided input array from key-value format to a list of parameters suitable for the specified
     * class / method.
     *
     * The input array should have the field name as the key, and the value will either be a primitive or another
     * key-value array.  The top level of this array needs keys that match the names of the parameters on the
     * service method.
     *
     * Mismatched types are caught by the PHP runtime, not explicitly checked for by this code.
     *
     * @param string $serviceClassName name of the service class that we are trying to call
     * @param string $serviceMethodName name of the method that we are trying to call
     * @param array $inputArray data to send to method in key-value format
     * @return array list of parameters that can be used to call the service method
     */
    public function getInputData($serviceClassName, $serviceMethodName, array $inputArray)
    {
        /** TODO: Reflection causes performance degradation when used in runtime. Should be optimized via caching */
        $serviceClass = new ClassReflection($serviceClassName);
        /** @var MethodReflection $serviceMethod */
        $serviceMethod = $serviceClass->getMethod($serviceMethodName);
        /** @var ParameterReflection[] $params */
        $params = $serviceMethod->getParameters();

        $inputData = [];
        foreach ($params as $param) {
            $paramName = $param->getName();
            if (isset($inputArray[$paramName])) {
                if ($this->_isArrayParam($param)) {
                    $paramType = "{$param->getType()}[]";
                    if (isset($inputArray[$paramName][ComplexTypeStrategy::ARRAY_ITEM_KEY_NAME])) {
                        /** Eliminate 'item' node if present. It is wrapping all data, declared in WSDL as array */
                        $item = $inputArray[$paramName][ComplexTypeStrategy::ARRAY_ITEM_KEY_NAME];
                        if (is_array($item)) {
                            $isAssociative = array_keys($item) !== range(0, count($item) - 1);
                            /**
                             * In case when only one filter is passed, it will not be wrapped into a subarray
                             * within item node. If several filters are passed, they will be wrapped into
                             * an indexed array within item node.
                             */
                            $paramValue = $isAssociative ? [$item] : $item;
                        }
                    } else {
                        $paramValue = $inputArray[$paramName];
                    }
                } else {
                    $paramType = $param->getType();
                    $paramValue = $inputArray[$paramName];
                }
                $inputData[] = $this->_convertValue($paramValue, $paramType);
            } else {
                $inputData[] = $param->getDefaultValue();                   // not set, so use default
            }
        }

        return $inputData;
    }

    /**
     * Check if parameter is an array.
     *
     * @param ParameterReflection $param
     * @return bool
     */
    protected function _isArrayParam($param)
    {
        $isArray = $param->isArray();
        $docBlock = $param->getDeclaringFunction()->getDocBlock();
        /** If array type is not set explicitly in the method interface, examine annotations */
        if (!$isArray && $docBlock) {
            /** This pattern will help to skip parameters declarations which precede to the current one */
            $precedingParamsPattern = str_repeat('.*\@param.*', $param->getPosition());
            $paramType = str_replace('\\', '\\\\', $param->getType());
            if (preg_match("/.*{$precedingParamsPattern}\@param\s+({$paramType}\[\]).*/is", $docBlock->getContents())) {
                $isArray = true;
            }
        }
        return $isArray;
    }

    /**
     * Creates a new instance of the given class and populates it with the array of data. The data can
     * be in different forms depending on the adapter being used, REST vs. SOAP. For REST, the data is
     * in snake_case (e.g. tax_class_id) while for SOAP the data is in camelCase (e.g. taxClassId).
     *
     * @param string|\ReflectionClass $class
     * @param array $data
     * @return object the newly created and populated object
     */
    protected function _createFromArray($class, $data)
    {
        $className = is_string($class) ? $class : $class->getName();
        $builder = $this->_objectManager->create($className . "Builder");
        $class = new ClassReflection($className);
        foreach ($data as $propertyName => $value) {
            // Converts snake_case to uppercase CamelCase to help form getter/setter method names
            $camelCaseProperty = str_replace(' ', '', ucwords(str_replace('_', ' ', $propertyName)));
            $methodName = $this->_processGetterMethod($class, $camelCaseProperty);
            $methodReflection = $class->getMethod($methodName);
            if ($methodReflection->isPublic()) {
                $returnType = $this->_typeProcessor->getGetterReturnType($methodReflection)['type'];
                $setterName = 'set' . $camelCaseProperty;
                $builder->$setterName($this->_convertValue($value, $returnType));
            }
        }
        return $builder->create();
    }

    /**
     * Convert data from array to Data Object representation if type is Data Object or array of Data Objects.
     *
     * @param mixed $value
     * @param string $type Convert given value to the this type
     * @return mixed
     */
    protected function _convertValue($value, $type)
    {
        if ($this->_typeProcessor->isTypeSimple($type)) {
            $result = $this->_typeProcessor->processSimpleType($value, $type);
        } elseif ($this->_typeProcessor->isArrayType($type)) {
            $itemType = $this->_typeProcessor->getArrayItemType($type);
            // Initializing the result for array type else it will return null for empty array
            $result = is_array($value) ? [] : null;
            foreach ($value as $key => $item) {
                $result[$key] = $this->_createFromArray($itemType, $item);
            }
        } else {
            $result = $this->_createFromArray($type, $value);
        }
        return $result;
    }

    /**
     * Find the getter method for a given property in the Data Object class
     *
     * @param ClassReflection $class
     * @param string $camelCaseProperty
     * @return string processed method name
     * @throws \Exception If $camelCaseProperty has no corresponding getter method
     */
    protected function _processGetterMethod(ClassReflection $class, $camelCaseProperty)
    {
        $getterName = 'get' . $camelCaseProperty;
        $boolGetterName = 'is' . $camelCaseProperty;
        if ($class->hasMethod($getterName)) {
            $methodName = $getterName;
        } elseif ($class->hasMethod($boolGetterName)) {
            $methodName = $boolGetterName;
        } else {
            throw new \Exception(sprintf(
                'Property :"%s" does not exist in the Data Object class: "%s".',
                $camelCaseProperty,
                $class->getName()
            ));
        }
        return $methodName;
    }
}
