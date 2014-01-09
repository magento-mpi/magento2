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
use Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor;
use Magento\Webapi\Model\Soap\Wsdl\ComplexTypeStrategy;

class ServiceArgsSerializer
{
    /** @var \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor */
    protected $_typeProcessor;

    /**
     * Initialize dependencies.
     *
     * TODO: Reconsider dependency on Soap\Config\Reader\TypeProcessor
     *
     * @param TypeProcessor $typeProcessor
     */
    public function __construct(TypeProcessor $typeProcessor)
    {
        $this->_typeProcessor = $typeProcessor;
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
     *
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
                    /** Eliminate 'item' node if present. It is wrapping all data, declared in WSDL as array */
                    $paramValue = isset($inputArray[$paramName][ComplexTypeStrategy::ARRAY_ITEM_KEY_NAME])
                        ? $inputArray[$paramName][ComplexTypeStrategy::ARRAY_ITEM_KEY_NAME]
                        : $inputArray[$paramName];
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
            if (preg_match("/.*{$precedingParamsPattern}\@param\s+({$paramType}\[\]).*/i", $docBlock->getContents())) {
                $isArray = true;
            }
        }
        return $isArray;
    }

    /**
     * Creates a new instance of the given class and populates it with the array of data.
     *
     * @param string|\ReflectionClass $class
     * @param array $data
     * @return object the newly created and populated object
     */
    protected function _createFromArray($class, $data)
    {
        $className = is_string($class) ? $class : $class->getName();
        try {
            $class = new ClassReflection($className);
            foreach ($data as $propertyName => $value) {
                $getterName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $propertyName)));
                $methodReflection = $class->getMethod($getterName);
                if ($methodReflection->isPublic()) {
                    $returnType = $this->_typeProcessor->getGetterReturnType($methodReflection)['type'];
                    $data[$propertyName] = $this->_convertValue($value, $returnType);
                }
            }
        } catch (\ReflectionException $e) {
            // Case where data array contains keys with no matching setter methods
            // TODO: do we need to do anything here or can we just ignore this and keep going?
        }
        $obj = new $className($data);
        return $obj;
    }

    /**
     * Convert data from array to DTO representation if type is DTO or array of DTOs.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function _convertValue($value, $type)
    {
        if (!$this->_typeProcessor->isTypeSimple($type)) {
            if ($this->_typeProcessor->isArrayType($type)) {
                $itemType = $this->_typeProcessor->getArrayItemType($type);
                foreach ($value as $key => $item) {
                    $result[$key] = $this->_createFromArray($itemType, $item);
                }
            } else {
                $result = $this->_createFromArray($type, $value);
            }
        } else {
            $result = $value;
        }
        return $result;
    }
}
