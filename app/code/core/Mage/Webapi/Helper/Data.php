<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Webapi module helper.
 *
 * @copyright  {}
 */
class Mage_Webapi_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Web API ACL resources tree root ID.
     */
    const RESOURCES_TREE_ROOT_ID = '__root__';

    /**
     * Reformat request data to be compatible with method specified interface: <br/>
     * - sort arguments in correct order <br/>
     * - set default values for omitted arguments
     * - instantiate objects of necessary classes
     *
     * @param string|object $classOrObject Resource class name
     * @param string $methodName Resource method name
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
     * @throws Mage_Webapi_Exception If required fields do not have values specified in $data
     */
    protected function _formatParamData($data, $dataType, Mage_Webapi_Model_Config $apiConfig)
    {
        if ($this->isTypeSimple($dataType) || is_null($data)) {
            $formattedData = $data;
        } elseif ($this->isArrayType($dataType)) {
            $formattedData = $this->_formatArrayData($data, $dataType, $apiConfig);
        } else {
            $formattedData = $this->_formatComplexObjectData($data, $dataType, $apiConfig);
        }
        return $formattedData;
    }

    /**
     * Format data of array type.
     *
     * @param array $data
     * @param string $dataType
     * @param Mage_Webapi_Model_Config $apiConfig
     * @return array
     * @throws Mage_Webapi_Exception If passed data is not an array
     */
    protected function _formatArrayData($data, $dataType, $apiConfig)
    {
        $itemDataType = $this->getArrayItemType($dataType);
        $formattedData = array();
        if (!is_array($data)) {
            throw new Mage_Webapi_Exception(
                $this->__('Data corresponding to "%s" type is expected to be an array.', $dataType),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
        foreach ($data as $itemData) {
            $formattedData[] = $this->_formatParamData($itemData, $itemDataType, $apiConfig);
        }
        return $formattedData;
    }

    /**
     * Format data as object of the specified class.
     *
     * @param array|object $data
     * @param string $dataType
     * @param Mage_Webapi_Model_Config $apiConfig
     * @return object Object of required data type
     * @throws LogicException If specified $dataType is invalid
     * @throws Mage_Webapi_Exception If required fields does not have values specified in $data
     */
    protected function _formatComplexObjectData($data, $dataType, $apiConfig)
    {
        $dataTypeMetadata = $apiConfig->getTypeData($dataType);
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
        if (!is_array($data)) {
            throw new Mage_Webapi_Exception(
                $this->__('Data corresponding to "%s" type is expected to be an array.', $dataType),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
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

    /**
     * Normalize short type names to full type names.
     *
     * @param string $type
     * @return string
     */
    public function normalizeType($type)
    {
        $normalizationMap = array(
            'str' => 'string',
            'integer' => 'int',
            'bool' => 'boolean',
        );

        return isset($normalizationMap[$type]) ? $normalizationMap[$type] : $type;
    }

    /**
     * Check if given type is a simple type.
     *
     * @param string $type
     * @return bool
     */
    public function isTypeSimple($type)
    {
        if ($this->isArrayType($type)) {
            $type = $this->getArrayItemType($type);
        }

        return in_array($type, array('string', 'int', 'float', 'double', 'boolean'));
    }

    /**
     * Check if given type is an array of type items.
     * Example:
     * <pre>
     *  ComplexType[] -> array of ComplexType items
     *  string[] -> array of strings
     * </pre>
     *
     * @param string $type
     * @return bool
     */
    public function isArrayType($type)
    {
        return (bool)preg_match('/(\[\]$|^ArrayOf)/', $type);
    }

    /**
     * Get item type of the array.
     * Example:
     * <pre>
     *  ComplexType[] => ComplexType
     *  string[] => string
     *  int[] => integer
     * </pre>
     *
     * @param string $arrayType
     * @return string
     */
    public function getArrayItemType($arrayType)
    {
        return $this->normalizeType(str_replace('[]', '', $arrayType));
    }

    /**
     * Translate complex type class name into type name.
     *
     * Example:
     * <pre>
     *  Mage_Customer_Model_Webapi_CustomerData => CustomerData
     *  Mage_Catalog_Model_Webapi_ProductData => CatalogProductData
     * </pre>
     *
     * @param string $class
     * @return string
     * @throws InvalidArgumentException
     */
    public function translateTypeName($class)
    {
        if (preg_match('/(.*)_(.*)_Model_Webapi_\2?(.*)/', $class, $matches)) {
            $moduleNamespace = $matches[1] == 'Mage' ? '' : $matches[1];
            $moduleName = $matches[2];
            $typeNameParts = explode('_', $matches[3]);

            return ucfirst($moduleNamespace . $moduleName . implode('', $typeNameParts));
        }
        throw new InvalidArgumentException(sprintf('Invalid parameter type "%s".', $class));
    }

    /**
     * Translate array complex type name.
     *
     * Example:
     * <pre>
     *  ComplexTypeName[] => ArrayOfComplexTypeName
     *  string[] => ArrayOfString
     * </pre>
     *
     * @param string $type
     * @return string
     */
    public function translateArrayTypeName($type)
    {
        return 'ArrayOf' . ucfirst($this->getArrayItemType($type));
    }

    /**
     * Translate controller class name into resource name.
     * Example:
     * <pre>
     *  Mage_Customer_Controller_Webapi_CustomerController => customer
     *  Mage_Customer_Controller_Webapi_Customer_AddressController => customerAddress
     *  Mage_Catalog_Controller_Webapi_ProductController => catalogProduct
     *  Mage_Catalog_Controller_Webapi_Product_ImagesController => catalogProductImages
     *  Mage_Catalog_Controller_Webapi_CategoryController => catalogCategory
     * </pre>
     *
     * @param string $class
     * @return string
     * @throws InvalidArgumentException
     */
    public function translateResourceName($class)
    {
        $resourceNameParts = $this->getResourceNameParts($class);
        return lcfirst(implode('', $resourceNameParts));
    }

    /**
     * Identify the list of resource name parts including subresources using class name.
     *
     * Examples of input/output pairs: <br/>
     * - 'Mage_Customer_Controller_Webapi_Customer_Address' => array('Customer', 'Address') <br/>
     * - 'Enterprise_Customer_Controller_Webapi_Customer_Address' => array('EnterpriseCustomer', 'Address') <br/>
     * - 'Mage_Catalog_Controller_Webapi_Product' => array('Catalog', 'Product')
     *
     * @param string $className
     * @return array
     * @throws InvalidArgumentException When class is not valid API resource.
     */
    public function getResourceNameParts($className)
    {
        if (preg_match(Mage_Webapi_Model_Config_Reader::RESOURCE_CLASS_PATTERN, $className, $matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Mage') ? '' : $moduleNamespace;
            $resourceNameParts = explode('_', trim($matches[3], '_'));
            if ($moduleName == $resourceNameParts[0]) {
                /** Avoid duplication of words in resource name */
                $moduleName = '';
            }
            $parentResourceName = $moduleNamespace . $moduleName . array_shift($resourceNameParts);
            array_unshift($resourceNameParts, $parentResourceName);
            return $resourceNameParts;
        }
        throw new InvalidArgumentException(sprintf('The controller class name "%s" is invalid.', $className));
    }

    /**
     * Retrieve list of allowed method names in action controllers.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_GET,
            Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE,
        );
    }

    /**
     * Identify API method name without version suffix by its reflection.
     *
     * @param ReflectionMethod|string $method Method name or method reflection.
     * @return string Method name without version suffix on success.
     * @throws InvalidArgumentException When method name is invalid API resource method.
     */
    public function getMethodNameWithoutVersionSuffix($method)
    {
        if ($method instanceof ReflectionMethod) {
            $methodNameWithSuffix = $method->getName();
        } else {
            $methodNameWithSuffix = $method;
        }
        $regularExpression = $this->getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $methodName = $methodMatches[1];
            return $methodName;
        }
        throw new InvalidArgumentException(sprintf('"%s" is an invalid API resource method.', $methodNameWithSuffix));
    }

    /**
     * Get regular expression to be used for method name separation into name itself and version.
     *
     * @return string
     */
    public function getMethodNameRegularExpression()
    {
        return sprintf('/(%s)(V\d+)/', implode('|', $this->getAllowedMethods()));
    }

    /**
     * Identify resource type by method name.
     *
     * @param string $methodName
     * @return string 'collection' or 'item'
     * @throws InvalidArgumentException When method does not match the list of allowed methods
     */
    public function getActionTypeByMethod($methodName)
    {
        $collection = Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_COLLECTION;
        $item = Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_ITEM;
        $actionTypeMap = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_GET => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_LIST => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE => $collection,
        );
        if (!isset($actionTypeMap[$methodName])) {
            throw new InvalidArgumentException(sprintf('The "%s" method is not a valid resource method.', $methodName));
        }
        return $actionTypeMap[$methodName];
    }

    /**
     * Identify request body param name, if it is expected by method.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool|string Return body param name if body is expected, false otherwise
     * @throws LogicException
     */
    public function getBodyParamName(ReflectionMethod $methodReflection)
    {
        $bodyParamName = false;
        /**#@+
         * Body param position in case of top level resources.
         */
        $bodyPosCreate = 1;
        $bodyPosMultiCreate = 1;
        $bodyPosUpdate = 2;
        $bodyPosMultiUpdate = 1;
        $bodyPosMultiDelete = 1;
        /**#@-*/
        $bodyParamPositions = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE => $bodyPosCreate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE => $bodyPosMultiCreate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE => $bodyPosUpdate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE => $bodyPosMultiUpdate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE => $bodyPosMultiDelete
        );
        $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
        $isBodyExpected = isset($bodyParamPositions[$methodName]);
        if ($isBodyExpected) {
            $bodyParamPosition = $bodyParamPositions[$methodName];
            if ($this->isSubresource($methodReflection)
                && $methodName != Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE
            ) {
                /** For subresources parent ID param must precede request body param. */
                $bodyParamPosition++;
            }
            $methodInterfaces = $methodReflection->getPrototypes();
            /** @var \Zend\Server\Reflection\Prototype $methodInterface */
            $methodInterface = reset($methodInterfaces);
            $methodParams = $methodInterface->getParameters();
            if (empty($methodParams) || (count($methodParams) < $bodyParamPosition)) {
                throw new LogicException(sprintf(
                    'Method "%s" must have parameter for passing request body. '
                        . 'Its position must be "%s" in method interface.',
                    $methodReflection->getName(),
                    $bodyParamPosition
                ));
            }
            /** @var $bodyParamReflection \Zend\Code\Reflection\ParameterReflection */
            /** Param position in the array should be counted from 0. */
            $bodyParamReflection = $methodParams[$bodyParamPosition - 1];
            $bodyParamName = $bodyParamReflection->getName();
        }
        return $bodyParamName;
    }

    /**
     * Identify ID param name if it is expected for the specified method.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool|string Return ID param name if it is expected; false otherwise.
     * @throws LogicException If resource method interface does not contain required ID parameter.
     */
    public function getIdParamName(ReflectionMethod $methodReflection)
    {
        $idParamName = false;
        $isIdFieldExpected = false;
        if (!$this->isSubresource($methodReflection)) {
            /** Top level resource, not subresource */
            $methodsWithId = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_GET,
                Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            );
            $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithId)) {
                $isIdFieldExpected = true;
            }
        } else {
            /**
             * All subresources must have ID field:
             * either subresource ID (for item operations) or parent resource ID (for collection operations)
             */
            $isIdFieldExpected = true;
        }

        if ($isIdFieldExpected) {
            /** ID field must always be the first parameter of resource method */
            $methodInterfaces = $methodReflection->getPrototypes();
            /** @var \Zend\Server\Reflection\Prototype $methodInterface */
            $methodInterface = reset($methodInterfaces);
            $methodParams = $methodInterface->getParameters();
            if (empty($methodParams)) {
                throw new LogicException(sprintf(
                    'The "%s" method must have at least one parameter: resource ID.',
                    $methodReflection->getName()
                ));
            }
            /** @var ReflectionParameter $idParam */
            $idParam = reset($methodParams);
            $idParamName = $idParam->getName();
        }
        return $idParamName;
    }

    /**
     * Identify if API resource is top level resource or subresource.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool
     * @throws InvalidArgumentException In case when class name is not valid API resource class.
     */
    public  function isSubresource(ReflectionMethod $methodReflection)
    {
        $className = $methodReflection->getDeclaringClass()->getName();
        if (preg_match(Mage_Webapi_Model_Config_Reader::RESOURCE_CLASS_PATTERN, $className, $matches)) {
            return count(explode('_', trim($matches[3], '_'))) > 1;
        }
        throw new InvalidArgumentException(sprintf('"%s" is not a valid resource class.', $className));
    }
}
