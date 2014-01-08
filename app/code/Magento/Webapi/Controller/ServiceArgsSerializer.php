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


use Magento\Service\Entity\AbstractDtoBuilder;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;
use Zend\Code\Reflection\ParameterReflection;

class ServiceArgsSerializer
{
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
        $serviceClass = new ClassReflection($serviceClassName);
        /** @var MethodReflection $serviceMethod */
        $serviceMethod = $serviceClass->getMethod($serviceMethodName);
        /** @var ParameterReflection[] $params */
        $params = $serviceMethod->getParameters();

        $inputData = [];
        foreach ($params as $param) {
            $paramName = $param->getName();

            if (isset($inputArray[$paramName])) {
                $inputData[] = $this->_convertValue($inputArray[$paramName], $param);
            } else {
                $inputData[] = $param->getDefaultValue();                   // not set, so use default
            }
        }

        return $inputData;
    }

    /**
     * Creates a new instance of the given class and populates it with the array of data using setter methods
     * on the new object.
     *
     * @param string|\ReflectionClass $class
     * @param array $data
     *
     * @return object the newly created and populated object
     */
    protected function _createFromArray($class, $data)
    {
        $className = is_string($class) ? $class : $class->getName();
        $class = new ClassReflection($className . "Builder");
        /** @var $obj AbstractDtoBuilder */
        $obj = $class->newInstance();
        foreach ($data as $propertyName => $value) {
            $setterName = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $propertyName)));
            try {
                $method = $class->getMethod($setterName);
                if ($method->getNumberOfParameters() == 1) {
                    $param = $method->getParameters()[0];
                    $arg = $this->_convertValue($value, $param);
                    $method->invoke($obj, $arg);
                }
            } catch (\ReflectionException $e) {
                // Case where data array contains keys with no matching setter methods
                // TODO: do we need to do anything here or can we just ignore this and keep going?
            }
        }
        return $obj->create();
    }

    /**
     * @param mixed $value
     * @param ParameterReflection $param
     *
     * @return array|null|object
     */
    protected function _convertValue($value, ParameterReflection $param)
    {
        $converted = null;
        $paramClass = $param->getClass();
        if ($param->isArray()) {                                    // is array
            $paramType = $param->getType();  // from doc block
            if (!empty($paramType) && $paramType != 'array') {          // typed array
                $values = [];
                foreach ($value as $dtoArray) {
                    $values[] = $this->_createFromArray($paramType, $dtoArray);
                }
                $converted = $values;
            } else {                                                    // simple or associative array
                $converted = $value;
            }
        } elseif (is_null($paramClass)) {                           // is primitive
            $converted = $value;
        } else {                                                    // is object/DTO
            $converted = $this->_createFromArray($paramClass, $value);
        }
        return $converted;
    }
}
