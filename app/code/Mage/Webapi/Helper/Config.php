<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Webapi config helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Helper_Config extends Mage_Core_Helper_Abstract
{
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
            'mixed' => 'anyType' // TODO: Temporary solution
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

        return in_array(
            $type,
            array('string', 'int', 'float', 'double', 'boolean', 'anyType'/** TODO: Temporary solution */)
        );
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
     * Translate service interface name into service name.
     * Example:
     * <pre>
     * - Mage_Customer_Service_CustomerInterfaceV1         => customer          // $preserveVersion == false
     * - Mage_Customer_Service_Customer_AddressInterfaceV1 => customerAddressV1 // $preserveVersion == true
     * - Mage_Catalog_Service_ProductInterfaceV2           => catalogProductV2  // $preserveVersion == true
     * </pre>
     *
     * @param string $interfaceName
     * @param bool $preserveVersion Should version be preserved during interface name conversion into service name
     * @return string
     * @throws InvalidArgumentException
     */
    public function getServiceName($interfaceName, $preserveVersion = true)
    {
        $serviceNameParts = $this->getServiceNameParts($interfaceName, $preserveVersion);
        return lcfirst(implode('', $serviceNameParts));
    }

    /**
     * Identify the list of resource name parts including subresources using class name.
     *
     * Examples of input/output pairs: <br/>
     * - 'Mage_Customer_Service_Customer_AddressInterfaceV1' => array('Customer', 'Address', 'V1') <br/>
     * - 'Vendor_Customer_Service_Customer_AddressInterfaceV1' => array('VendorCustomer', 'Address', 'V1) <br/>
     * - 'Mage_Catalog_Service_ProductInterfaceV2' => array('CatalogProduct', 'V2')
     *
     * @param string $className
     * @param bool $preserveVersion Should version be preserved during class name conversion into resource name
     * @return array
     * @throws InvalidArgumentException When class is not valid API resource.
     */
    public function getServiceNameParts($className, $preserveVersion = false)
    {
        if (preg_match(Mage_Webapi_Model_Config_ReaderAbstract::RESOURCE_CLASS_PATTERN, $className, $matches)) {
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
            if ($preserveVersion) {
                $serviceVersion = $matches[4];
                $resourceNameParts[] = $serviceVersion;
            }
            return $resourceNameParts;
        }
        throw new InvalidArgumentException(sprintf('The service class name "%s" is invalid.', $className));
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
        return sprintf('/(%s)(V\d+)/', implode('|', Mage_Webapi_Controller_ActionAbstract::getAllowedMethods()));
    }

    /**
     * Identify request body param name, if it is expected by method.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool|string Return body param name if body is expected, false otherwise
     * @throws LogicException
     */
    public function getOperationBodyParamName(ReflectionMethod $methodReflection)
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
    public function getOperationIdParamName(ReflectionMethod $methodReflection)
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
    public function isSubresource(ReflectionMethod $methodReflection)
    {
        $className = $methodReflection->getDeclaringClass()->getName();
        if (preg_match(Mage_Webapi_Model_Config_ReaderAbstract::RESOURCE_CLASS_PATTERN, $className, $matches)) {
            return count(explode('_', trim($matches[3], '_'))) > 1;
        }
        throw new InvalidArgumentException(sprintf('"%s" is not a valid resource class.', $className));
    }

    /**
     * Generate SOAP operation name.
     *
     * @param string $interfaceName e.g. Mage_Catalog_Service_ProductInterfaceV1
     * @param string $methodName e.g. create
     * @return string e.g. catalogProductCreate
     */
    public function getSoapOperation($interfaceName, $methodName)
    {
        $serviceName = $this->getServiceName($interfaceName, false);
        $operationName = $serviceName . ucfirst($methodName);
        return $operationName;
    }
}
