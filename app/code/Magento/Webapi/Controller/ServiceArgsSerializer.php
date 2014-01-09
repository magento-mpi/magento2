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

use Magento\Service\Entity\AbstractDto;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;
use Zend\Code\Reflection\ParameterReflection;
use Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor;

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
        $serviceClass = new ClassReflection($serviceClassName);
        /** @var MethodReflection $serviceMethod */
        $serviceMethod = $serviceClass->getMethod($serviceMethodName);
        /** @var ParameterReflection[] $params */
        $params = $serviceMethod->getParameters();

        $inputData = [];
        foreach ($params as $param) {
            $paramName = $param->getName();

            if (isset($inputArray[$paramName])) {
                $paramType = $param->isArray() ? "{$param->getType()}[]" : $param->getType();
                $inputData[] = $this->_convertValue($inputArray[$paramName], $paramType);
            } else {
                $inputData[] = $param->getDefaultValue();                   // not set, so use default
            }
        }

        return $inputData;
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
                    $returnType = $this->_getReturnType($methodReflection);
                    $data[$propertyName] = $this->_convertValue($value, $returnType);
                }
            }
        } catch (\ReflectionException $e) {
            // Case where data array contains keys with no matching setter methods
            // TODO: do we need to do anything here or can we just ignore this and keep going?
        }
        /** @var $obj AbstractDto */
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
                    $value[$key] = $this->_createFromArray($itemType, $item);
                }
            } else {
                $value = $this->_createFromArray($type, $value);
            }
        }
        return $value;
    }

    /**
     * Identify getter return type by method reflection.
     *
     * @param \Zend\Code\Reflection\MethodReflection $methodReflection
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function _getReturnType($methodReflection)
    {
        // TODO: Avoid code duplication with \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor::_processMethod
        $methodDocBlock = $methodReflection->getDocBlock();
        if (!$methodDocBlock) {
            throw new \InvalidArgumentException('Each getter must have description with @return annotation.');
        }
        $returnAnnotations = $methodDocBlock->getTags('return');
        if (empty($returnAnnotations)) {
            throw new \InvalidArgumentException('Getter return type must be specified using @return annotation.');
        }
        /** @var \Zend\Code\Reflection\DocBlock\Tag\ReturnTag $returnAnnotation */
        $returnAnnotation = current($returnAnnotations);
        $returnType = $returnAnnotation->getType();
        /*
         * Adding this code as a workaround since \Zend\Code\Reflection\DocBlock\Tag\ReturnTag::initialize does not
         * detect and return correct type for array of objects in annotation.
         * eg @return \Magento\Webapi\Service\Entity\SimpleDto[] is returned with type
         * \Magento\Webapi\Service\Entity\SimpleDto instead of \Magento\Webapi\Service\Entity\SimpleDto[]
         */
        $match = array();
        preg_match('/(?<=@return )\S+/i', $methodDocBlock->getContents(), $match);
        if (isset($match[0]) && $this->_typeProcessor->isArrayType($match[0])) {
            $returnType = $returnType . '[]';
        }
        if (preg_match('/^(.+)\|null$/', $returnType, $matches)) {
            /** If return value is optional, alternative return type should be set to null */
            $returnType = $matches[1];
            return $returnType;
        }
        return $returnType;
    }
}
