<?php
/**
 * Webapi module helper.
 *
 * @copyright  {}
 */
class Mage_Webapi_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Web API ACL resources tree root ID
     */
    const RESOURCES_TREE_ROOT_ID = '__root__';

    /**
     * Reformat request data to be compatible with method specified interface: <br/>
     * - sort arguments in correct order <br/>
     * - set default values for omitted arguments
     * - instantiate objects of necessary classes
     *
     * @param string|object $classOrObject
     * @param string $methodName
     * @param array $requestData Data to be passed to method
     * @param Mage_Webapi_Model_Config $apiConfig
     * @return array Array of prepared method arguments
     * @throws Mage_Webapi_Exception
     */
    public function prepareMethodParams(
        $classOrObject,
        $methodName,
        $requestData,
        Mage_Webapi_Model_Config $apiConfig
    ) {
        $methodReflection = $this->createMethodReflection($classOrObject, $methodName);
        $methodData = $apiConfig->getMethodMetadata($methodReflection);
        $methodArguments = array();
        if (isset($methodData['interface']['in']['parameters'])
            && is_array($methodData['interface']['in']['parameters'])
        ) {
            foreach ($methodData['interface']['in']['parameters'] as $paramName => $paramData) {
                if (isset($requestData[$paramName])) {
                    $methodArguments[$paramName] = $this->_formatParamData(
                        $requestData[$paramName],
                        $paramData['type'],
                        $apiConfig
                    );
                } elseif (!$paramData['required']) {
                    $methodArguments[$paramName] = $paramData['default'];
                } else {
                    throw new Mage_Webapi_Exception($this->__('Required parameter "%s" is missing.', $paramName),
                        Mage_Webapi_Exception::HTTP_BAD_REQUEST);
                }
            }
        }
        return $methodArguments;
    }

    /**
     * Format $data according to specified $dataType recursively.
     *
     * Instantiate objects of proper classes and set data to its fields.
     *
     * @param mixed $data
     * @param string $dataType
     * @param Mage_Webapi_Model_Config $apiConfig
     * @return mixed
     * @throws LogicException If specified $dataType is invalid
     * @throws Mage_Webapi_Exception If required fields does not have values specified in $data
     */
    protected function _formatParamData($data, $dataType, Mage_Webapi_Model_Config $apiConfig)
    {
        if ($apiConfig->isTypeSimple($dataType) || is_null($data)) {
            return $data;
        } elseif ($apiConfig->isArrayType($dataType)) {
            $itemDataType = $apiConfig->getArrayItemType($dataType);
            $formattedData = array();
            foreach ($data as $itemData) {
                $formattedData[] = $this->_formatParamData($itemData, $itemDataType, $apiConfig);
            }
            return $formattedData;
        } else {
            $dataTypeMetadata = $apiConfig->getDataType($dataType);
            $typeToClassMap = $apiConfig->getTypeToClassMap();
            if (!isset($typeToClassMap[$dataType])) {
                throw new LogicException(sprintf('Specified data type "%s" does not match any class.', $dataType));
            }
            $complexTypeClass = $typeToClassMap[$dataType];
            if (is_object($data) && (get_class($data) == $complexTypeClass)) {
                /** In case of SOAP the object creation is performed by soap server. */
                return $data;
            }
            $complexDataObject = new $complexTypeClass();
            foreach ($dataTypeMetadata['parameters'] as $fieldName => $fieldMetadata) {
                if (isset($data[$fieldName])) {
                    $fieldValue = $data[$fieldName];
                } elseif (($fieldMetadata['required'] == false)) {
                    $fieldValue = $fieldMetadata['default'];
                } else {
                    throw new Mage_Webapi_Exception($this->__('Value of "%s" attribute is required.', $fieldName),
                        Mage_Webapi_Exception::HTTP_BAD_REQUEST);
                }
                $complexDataObject->$fieldName = $this->_formatParamData(
                    $fieldValue,
                    $fieldMetadata['type'],
                    $apiConfig
                );
            }
            return $complexDataObject;
        }
    }

    /**
     * Create Zend method reflection object.
     *
     * @param string|object $classOrObject
     * @param string $methodName
     * @return Zend\Server\Reflection\ReflectionMethod
     */
    public function createMethodReflection($classOrObject, $methodName)
    {
        $methodReflection = new \ReflectionMethod($classOrObject, $methodName);
        $classReflection = new \ReflectionClass($classOrObject);
        $zendClassReflection = new Zend\Server\Reflection\ReflectionClass($classReflection);
        $zendMethodReflection = new Zend\Server\Reflection\ReflectionMethod($zendClassReflection, $methodReflection);
        return $zendMethodReflection;
    }

    /**
     * Convert objects and arrays to array recursively.
     *
     * @param  array|object $data
     */
    // TODO: Remove if not used anymore
    public function toArray(&$data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_array($value) or is_object($value)) {
                    $this->toArray($value);
                }
            }
        }
    }

    /**
     * Convert singular form of word to plural.
     *
     * @param string $singular
     * @return string
     */
    public function convertSingularToPlural($singular)
    {
        $plural = $singular;
        $conversionMatrix = array(
            '/(x|ch|ss|sh)$/i' => "$1es",
            '/([^aeiouy]|qu)y$/i' => "$1ies",
            '/s$/i' => "s",
            /** Add 's' to any string longer than 0 characters */
            '/(.+)$/' => "$1s"
        );
        foreach ($conversionMatrix as $singularPattern => $pluralPattern) {
            if (preg_match($singularPattern, $singular)) {
                $plural = preg_replace($singularPattern, $pluralPattern, $singular);
                break;
            }
        }
        return $plural;
    }
}
