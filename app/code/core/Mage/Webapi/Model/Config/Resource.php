<?php
use Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Reflection\ClassReflection,
    Zend\Server\Reflection,
    Zend\Server\Reflection\ReflectionMethod;

/**
 * Magento API Resources config.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Resource
{
    /**
     * @var DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var Magento_Autoload
     */
    protected $_autoloader;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Reflection
     */
    protected $_serverReflection;

    /**
     * Resources configuration data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Resources complex types
     *
     * @var array
     */
    protected $_types;

    /**
     * Map of types to real classes.
     *
     * @var array
     */
    protected $_soapServerClassMap = array();

    /**
     * Class map for auto loader.
     *
     * @var array
     */
    protected $_autoLoaderClassMap;

    /**
     * Initialize API resources config.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['directoryScanner']) && $options['directoryScanner'] instanceof DirectoryScanner) {
            $this->_directoryScanner = $options['directoryScanner'];
        } else {
            $directories = array();
            /** @var Mage_Core_Model_Config_Element $module */
            foreach (Mage::getConfig()->getNode('modules')->children() as $moduleName => $module) {
                if ($module->is('active')) {
                    $directory = Mage::getConfig()->getModuleDir('controllers', $moduleName) . DS . 'Webapi';
                    if (is_dir($directory)) {
                        $directories[] = $directory;
                    }
                }
            }
            $this->_directoryScanner = new DirectoryScanner($directories);
        }

        if (isset($options['autoloader']) && $options['autoloader'] instanceof Magento_Autoload) {
            $this->_autoloader = $options['autoloader'];
        } else {
            $this->_autoloader = Magento_Autoload::getInstance();
        }

        if (isset($options['applicationConfig']) && $options['applicationConfig'] instanceof Mage_Core_Model_Config) {
            $this->_applicationConfig = $options['applicationConfig'];
        } else {
            $this->_applicationConfig = Mage::getConfig();
        }

        if (isset($options['serverReflection']) && $options['serverReflection'] instanceof Reflection) {
            $this->_serverReflection = $options['serverReflection'];
        } else {
            $this->_serverReflection = new Reflection();
        }

        if (isset($options['data']) && is_array($options['data']) && !empty($options['data'])) {
            $this->_data = $options['data'];
        } else {
            $this->_extractData();
        }
    }

    /**
     * Retrieve method data for given resource name and method name.
     *
     * @param string $resourceName
     * @param string $methodName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getResourceMethodData($resourceName, $methodName)
    {
        if (!array_key_exists($resourceName, $this->_data['resources'])) {
            throw new InvalidArgumentException(
                sprintf('Resource "%s" is not found in config.', $resourceName));
        }
        if (!array_key_exists($methodName, $this->_data['resources'][$resourceName])) {
            throw new InvalidArgumentException(
                sprintf('Method "%s" for resource "%s" is not found in config.', $methodName, $resourceName));
        }
        return $this->_data['resources'][$resourceName][$methodName];
    }

    /**
     * Retrieve data type details for the given type name.
     *
     * @param string $typeName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getDataType($typeName)
    {
        if (!isset($this->_types[$typeName])) {
            throw new InvalidArgumentException(sprintf('Data type "%s" is not found in config.', $typeName));
        }
        return $this->_types[$typeName];
    }

    /**
     * Retrieve specific resource version interface data.
     *
     * @param string $resourceName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return array
     * @throws RuntimeException
     */
    public function getResource($resourceName, $resourceVersion)
    {
        $helper = Mage::helper('Mage_Webapi_Helper_Data');
        if (!isset($this->_data[$resourceName])) {
            throw new RuntimeException($helper->__('Unknown resource "%s".', $resourceName));
        }
        /** Allow to take resource version in two formats: with 'v' prefix and without it */
        $resourceVersion = is_numeric($resourceVersion) ? 'v' . $resourceVersion : $resourceVersion;
        if (!isset($this->_data[$resourceName]['versions'][$resourceVersion])) {
            throw new RuntimeException($helper->__('Unknown version "%s" for resource "%s".', $resourceVersion,
                $resourceName));
        }

        $resource = array();
        foreach ($this->_data[$resourceName]['versions'] as $version => $data) {
            $resource = array_replace_recursive($resource, $data);
            if ($version == $resourceVersion) {
                break;
            }
        }

        return $resource;
    }

    /**
     * Identify resource name by operation name.
     *
     * If $resourceVersion is set, the check for operation validity in specified resource version will be performed.
     * If $resourceVersion is not set, the only check will be: if resource exists.
     *
     * @param string $operationName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return string|bool Resource name on success; false on failure
     */
    public function getResourceNameByOperation($operationName, $resourceVersion = null)
    {
        $result = false;
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        $resourceExists = isset($this->_data[$resourceName]);
        $versionCheckNotRequired = $resourceVersion === null;
        /** Allow to take resource version in two formats: with 'v' prefix and without it */
        $resourceVersion = is_numeric($resourceVersion) ? 'v' . $resourceVersion : $resourceVersion;
        $operationValidForRequestedResourceVersion =
            isset($this->_data[$resourceName]['versions'][lcfirst($resourceVersion)]['methods'][$methodName]);
        if (($versionCheckNotRequired && $resourceExists) || $operationValidForRequestedResourceVersion) {
            $result = $resourceName;
        }
        return $result;
    }

    /**
     * Identify method name by operation name.
     *
     * @param string $operationName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return string|bool Method name on success; false on failure
     */
    public function getMethodNameByOperation($operationName, $resourceVersion)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        /** Allow to take resource version in two formats: with 'v' prefix and without it */
        $resourceVersion = is_numeric($resourceVersion) ? 'v' . $resourceVersion : $resourceVersion;
        return isset($this->_data[$resourceName]['versions'][lcfirst($resourceVersion)]['methods'][$methodName])
            ? $methodName : false;
    }

    /**
     * Parse operation name to separate resource name from method name.
     *
     * <pre>Result format:
     * array(
     *      0 => false|'resourceName',
     *      1 => false|'methodName'
     * )</pre>
     *
     * @param string $operationName
     * @return array
     */
    protected function _parseOperationName($operationName)
    {
        $result = array(false, false);
        /** Note that '(.*?)' must not be greedy to allow regexp to match 'multiUpdate' method before 'update' */
        $regEx = sprintf('/(.*?)(%s)$/i', implode('|', $this->_getAllowedMethods()));
        if (preg_match($regEx, $operationName, $matches)) {
            $resourceName = $matches[1];
            $methodName = lcfirst($matches[2]);
            $result = array($resourceName, $methodName);
        }
        return $result;
    }

    /**
     * Identify controller class by operation name and its version.
     *
     * @param string $operationName
     * @return bool|string Resource name on success; false if operation was not found
     * @throws LogicException
     */
    public function getControllerClassByOperationName($operationName)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        if ($resourceName) {
            if (isset($this->_data[$resourceName]['controller'])) {
                return $this->_data[$resourceName]['controller'];
            }
            throw new LogicException(sprintf('Resource "%s" must have associated controller class.', $resourceName));
        }
        return $resourceName;
    }

    /**
     * Identify module name by operation name.
     *
     * @param string $operationName
     * @return string|bool Module name on success; false on failure.
     * @throws LogicException In case when resource was found but module was not specified.
     */
    public function getModuleNameByOperation($operationName)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        if ($resourceName) {
            if (isset($this->_data[$resourceName]['module'])) {
                return $this->_data[$resourceName]['module'];
            }
            throw new LogicException(sprintf('Resource "%s" must have module specified.', $resourceName));
        }
        return $resourceName;
    }

    /**
     * Identify deprecation policy for the specified operation.
     *
     * Return result in the following format:<pre>
     * array(
     *     'deprecated' => true,              // either 'deprecated' or 'removed' item must be specified
     *     'removed' => true,
     *     'use_version' => N,                // version of operation to be used instead
     *     'use_operation' => 'operationName' // operation to be used instead
     * )
     * </pre>
     *
     * @param string $operationName
     * @return array|bool
     */
    // TODO: Reimplement according to AutoDiscovery code generation
    public function getOperationDeprecationPolicy($operationName)
    {
        return isset($this->_data['operations'][$operationName])
            && isset($this->_data['operations'][$operationName]['deprecation_policy'])
            ? $this->_data['operations'][$operationName]['deprecation_policy']
            : false;
    }

    /**
     * Extract configuration data from the action controllers files.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @return array
     */
    protected function _extractData()
    {
        if (is_null($this->_data)) {
            $this->_populateClassMap();

            foreach ($this->_autoLoaderClassMap as $className => $filename) {
                if (preg_match('/(.*)_Webapi_(.*)Controller*/', $className)) {
                    $data = array();
                    $data['controller'] = $className;
                    $data['rest_routes'] = array();
                    /** @var ReflectionMethod $methodReflection */
                    foreach ($this->_serverReflection->reflectClass($className)->getMethods() as $methodReflection) {
                        $method = $this->_getMethodNameWithoutVersionSuffix($methodReflection);
                        $version = $this->_getMethodVersion($methodReflection);
                        if ($method && $version) {
                            $data['versions'][$version]['methods'][$method] = $this->_getMethodData($methodReflection);
                            $data['rest_routes'] = array_merge($data['rest_routes'],
                                $this->_generateRestRoutes($methodReflection));
                        }
                    }
                    // Sort versions array for further fallback.
                    ksort($data['versions']);
                    $this->_data[$this->translateResourceName($className)] = $data;
                }
            }

            if (empty($this->_data)) {
                throw new InvalidArgumentException('Can not populate config - no action controllers were found.');
            }
        }

        return $this->_data;
    }

    /**
     * Identify API method version by its reflection.
     *
     * @param ReflectionMethod $methodReflection
     * @return string|bool Method version with 'v' prefix on success.
     *      false is returned in case when method should not be exposed via API.
     */
    protected function _getMethodVersion(ReflectionMethod $methodReflection)
    {
        $methodVersion = false;
        $methodNameWithSuffix = $methodReflection->getName();
        $regularExpression = $this->_getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $methodVersion = lcfirst($methodMatches[2]);
        }
        return $methodVersion;
    }

    /**
     * Identify API method name without version suffix by its reflection.
     *
     * @param ReflectionMethod $methodReflection
     * @return string|bool Method name without version suffix on success.
     *      false is returned in case when method should not be exposed via API.
     */
    protected function _getMethodNameWithoutVersionSuffix(ReflectionMethod $methodReflection)
    {
        $methodName = false;
        $methodNameWithSuffix = $methodReflection->getName();
        $regularExpression = $this->_getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $methodName = $methodMatches[1];
        }
        return $methodName;
    }

    /**
     * Get regular expression to be used for method name separation into name itself and version.
     *
     * @return string
     */
    protected function _getMethodNameRegularExpression()
    {
        return sprintf('/(%s)(V\d+)/', implode('|', $this->_getAllowedMethods()));
    }

    /**
     * Generate a list of routes available fo the specified method.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     */
    protected function _generateRestRoutes(ReflectionMethod $methodReflection)
    {
        $routes = array();
        // TODO: Check method interface against business rules (existence of ID field, body field where necessary)
        $idParamName = $this->_getIdParamName($methodReflection);
        $version = $this->_getMethodVersion($methodReflection);
        $routePath = "/$version";
        /** @var Mage_Webapi_Helper_Data $helper */
        $helper = Mage::helper('Mage_Webapi_Helper_Data');
        foreach ($this->getResourceNameParts($methodReflection->getDeclaringClass()->getName()) as $resourcePathPart) {
            $routePath .= "/" . lcfirst($helper->convertSingularToPlural($resourcePathPart));
        }
        if ($idParamName) {
            $routePath .= "/:$idParamName";
        }
        foreach ($this->_getPathCombinations($this->_getOptionalParamNames($methodReflection)) as $routeOptionalPart) {
            $routes[$routePath . $routeOptionalPart] = array(
                'resource_type' => $this->_getResourceTypeByMethod(
                    $this->_getMethodNameWithoutVersionSuffix($methodReflection)),
                'resource_version' => $version
            );
        }

        return $routes;
    }

    /**
     * Identify resource type by method name.
     *
     * @param string $methodName
     * @return string 'collection' or 'item'
     * @throws InvalidArgumentException When method does not match the list of allowed methods
     */
    protected function _getResourceTypeByMethod($methodName)
    {
        // TODO: Remove dependency on Mage_Webapi_Controller_Front_Rest
        $collection = Mage_Webapi_Controller_Front_Rest::RESOURCE_TYPE_COLLECTION;
        $item = Mage_Webapi_Controller_Front_Rest::RESOURCE_TYPE_ITEM;
        $methodToResourceTypeMap = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_LIST => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE => $collection,
        );
        if (!isset($methodToResourceTypeMap[$methodName])) {
            throw new InvalidArgumentException(sprintf('"%s" method is not valid resource method.', $methodName));
        }
        return $methodToResourceTypeMap[$methodName];
    }


    /**
     * Identify list of possible routes taking into account optional params.
     *
     * @param array $optionalParams
     * @return array List of possible route params
     */
    // TODO: Can be improved to collect all possible combinations (in mixed order)
    protected function _getPathCombinations($optionalParams)
    {
        $pathCombinations = array();
        $currentPath = '';
        $pathCombinations[] = $currentPath;
        foreach ($optionalParams as $paramName) {
            $currentPath .= "/$paramName/:$paramName";
            $pathCombinations[] = $currentPath;
        }
        return $pathCombinations;
    }

    /**
     * Retrieve all optional parameters names.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     */
    protected function _getOptionalParamNames(ReflectionMethod $methodReflection)
    {
        $optionalParamNames = array();
        /** ID field must always be the first parameter of resource method */
        $methodInterfaces = $methodReflection->getPrototypes();
        /** Take the most full interface, that includes optional parameters also. */
        /** @var \Zend\Server\Reflection\Prototype $methodInterface */
        $methodInterface = end($methodInterfaces);
        $methodParams = $methodInterface->getParameters();
        /** @var ReflectionParameter $paramReflection */
        foreach ($methodParams as $paramReflection) {
            if ($paramReflection->isOptional()) {
                $optionalParamNames[] = $paramReflection->getName();
            }
        }
        return $optionalParamNames;
    }

    /**
     * Identify ID param name if it is expected for the specified method.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool|string Return ID param name if it is expected; false otherwise.
     * @throws LogicException If resource method interface does not contain required ID parameter.
     */
    protected function _getIdParamName(ReflectionMethod $methodReflection)
    {
        $idParamName = false;
        $idFieldIsExpected = false;
        if (!$this->_isSubresource($methodReflection)) {
            /** Top level resource, not subresource */
            $methodsWithIdExpected = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            );
            $methodName = $this->_getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithIdExpected)) {
                $idFieldIsExpected = true;
            }
        } else {
            /**
             * All subresources must have ID field:
             * either subresource ID (for item operations) or parent resource ID (for collection operations)
             */
            $idFieldIsExpected = true;
        }

        if ($idFieldIsExpected) {
            /** ID field must always be the first parameter of resource method */
            $methodInterfaces = $methodReflection->getPrototypes();
            if (empty($methodInterfaces)) {
                throw new LogicException(sprintf('Method "%s" must have at least one parameter: resource ID.'),
                    $methodReflection->getName());
            }
            /** @var \Zend\Server\Reflection\Prototype $methodInterface */
            $methodInterface = reset($methodInterfaces);
            $methodParams = $methodInterface->getParameters();
            if (empty($methodParams)) {
                throw new LogicException(sprintf('Method "%s" must have at least one parameter: resource ID.'),
                    $methodReflection->getName());
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
     */
    protected function _isSubresource(ReflectionMethod $methodReflection)
    {
        $className = $methodReflection->getDeclaringClass()->getName();
        preg_match('/.*_Webapi_(.*)Controller*/', $className, $matches);
        return count(explode('_', $matches[1])) > 1;
    }

    /**
     * Get all modules routes defined in config.
     *
     * @return array
     */
    public function getRestRoutes()
    {
        $routes = array();
        $apiTypeRoutePath = str_replace(':api_type', 'rest', Mage_Webapi_Controller_Router_Route_ApiType::API_ROUTE);
        foreach ($this->_data as $resourceName => $resourceData) {
            foreach ($resourceData['rest_routes'] as $routePath => $routeData) {
                $fullRoutePath = $apiTypeRoutePath . $routePath;
                $route = new Mage_Webapi_Controller_Router_Route_Rest($fullRoutePath);
                $route->setResourceName($resourceName)
                    ->setResourceType($routeData['resource_type'])
                    ->setResourceVersion($routeData['resource_version']);
                $routes[] =$route;
            }
        }

        return $routes;
    }

    /**
     * Walk all files from directory scanner and set them into autoloader class map.
     *
     * @throws LogicException
     */
    protected function _populateClassMap()
    {
        $classMap = array();
        /** @var \Zend\Code\Scanner\FileScanner $file */
        foreach ($this->_directoryScanner->getFiles(true) as $file) {
            $filename = $file->getFile();
            $classes = $file->getClasses();
            if (count($classes) > 1) {
                throw new LogicException(sprintf('There can be only one class in controller file "%s".', $filename));
            }
            /** @var \Zend\Code\Scanner\ClassScanner $class */
            $class = reset($classes);
            $relativePath = str_replace($this->_applicationConfig->getOptions()->getBaseDir(), '', $filename);
            $classMap[$class->getName()] = $relativePath;
        }

        $this->_autoLoaderClassMap = $classMap;
        $this->_autoloader->addFilesMap($this->_autoLoaderClassMap);
    }

    /**
     * Retrieve method interface and documentation description.
     *
     * @param ReflectionMethod $method
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getMethodData(ReflectionMethod $method)
    {
        $methodData = array(
            'documentation' => $method->getDescription(),
        );

        // TODO: copy-past from Zend_Soap_AutoDiscover, review
        $prototype = null;
        $maxNumArgumentsOfPrototype = -1;
        /** @var \Zend\Server\Reflection\Prototype $tmpPrototype */
        foreach ($method->getPrototypes() as $tmpPrototype) {
            $numParams = count($tmpPrototype->getParameters());
            if ($numParams > $maxNumArgumentsOfPrototype) {
                $maxNumArgumentsOfPrototype = $numParams;
                $prototype = $tmpPrototype;
            }
        }
        if (is_null($prototype)) {
            throw new InvalidArgumentException(sprintf('No prototypes could be found for the "%s" function.',
                $method->getName()));
        }

        /** @var \Zend\Server\Reflection\ReflectionParameter $parameter */
        foreach ($prototype->getParameters() as $parameter) {
            $methodData['interface']['in']['parameters'][$parameter->getName()] = array(
                'type' => $this->_processType($parameter->getType()),
                'required' => !$parameter->isOptional(),
                'documentation' => $parameter->getDescription(),
            );
        }

        if ($prototype->getReturnType() != 'void') {
            $methodData['interface']['out']['parameters']['result'] = array(
                'type' => $this->_processType($prototype->getReturnType()),
                'documentation' => $prototype->getReturnValue()->getDescription()
            );
        }

        return $methodData;
    }

    /**
     * Process type name.
     * In case parameter type is a complex type (class) - process it's properties.
     *
     * @param string $type
     * @return string
     */
    protected function _processType($type)
    {
        $typeName = $this->normalizeType($type);
        if (!$this->isTypeSimple($typeName)) {
            $complexTypeName = $this->translateTypeName($type);
            if (!isset($this->_types[$complexTypeName])) {
                $this->_processComplexType($type);
                if (!$this->isArrayType($complexTypeName)) {
                    $this->_soapServerClassMap[$complexTypeName] = $type;
                }
            }
            $typeName = $complexTypeName;
        }

        return $typeName;
    }

    /**
     * Retrieve complex type information from class public properties.
     *
     * @param string $class
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _processComplexType($class)
    {
        $typeName = $this->translateTypeName($class);
        $this->_types[$typeName] = array();
        if ($this->isArrayType($class)) {
            $this->_processType($this->getArrayItemType($class));
        } else {
            if (!class_exists($class)) {
                throw new InvalidArgumentException(sprintf('Could not load class "%s" as parameter type.', $class));
            }
            $reflection = new ClassReflection($class);
            $this->_types[$typeName]['documentation'] = $this->_getDescription($reflection->getDocBlock());
            $defaultProperties = $reflection->getDefaultProperties();
            /** @var \Zend\Code\Reflection\PropertyReflection $property */
            foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                $doc = $property->getDocBlock();
                $tags = $doc->getTags('var');
                if (empty($tags)) {
                    throw new InvalidArgumentException('Property type must be defined with @var tag.');
                }
                /** @var \Zend\Code\Reflection\DocBlock\Tag\GenericTag $varTag */
                $varTag = current($tags);
                // TODO: quickfix. In php 5.3.13 DocBlockReflection returns tags as "string\r"
                $varType = str_replace("\r", '', $varTag->returnValue(0));
                $this->_types[$typeName]['parameters'][$propertyName] = array(
                    'type' => $this->_processType($varType),
                    'required' => is_null($defaultProperties[$propertyName]),
                    'default' => $defaultProperties[$propertyName],
                    'documentation' => $this->_getDescription($doc)
                );
            }
        }

        return $this->_types[$typeName];
    }

    /**
     * Get short and long description from docblock and concatenate.
     *
     * @param Zend\Code\Reflection\DocBlockReflection $doc
     * @return string
     */
    protected function _getDescription(\Zend\Code\Reflection\DocBlockReflection $doc)
    {
        $shortDescription = $doc->getShortDescription();
        $longDescription = $doc->getLongDescription();

        $description = $shortDescription;
        if ($longDescription && !empty($description)) {
            $description .= "\r\n";
        }
        $description .= $longDescription;

        return $description;
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
     * @param string $className
     * @return array
     * @throws InvalidArgumentException When class is not valid API resource.
     */
    protected function getResourceNameParts($className)
    {
        if (preg_match('/(.*)_Webapi_(.*)Controller*/', $className, $matches)) {
            list($moduleNamespace, $moduleName) = explode('_', $matches[1]);
            $moduleNamespace = $moduleNamespace == 'Mage' ? '' : $moduleNamespace;

            $resourceNameParts = explode('_', $matches[2]);
            if ($moduleName == $resourceNameParts[0]) {
                /** Avoid duplication of words in resource name */
                $moduleName = '';
            }
            $parentResourceName = $moduleNamespace . $moduleName . array_shift($resourceNameParts);
            array_unshift($resourceNameParts, $parentResourceName);
            return $resourceNameParts;
        }

        throw new InvalidArgumentException(sprintf('Invalid controller class name "%s".', $className));
    }

    /**
     * Translate complex type class name into type name.
     * Example:
     * <pre>
     *  Mage_Customer_Webapi_Customer_DataStructure => CustomerDataStructure
     *  Mage_Catalog_Webapi_Product_DataStructure => CatalogProductDataStructure
     * </pre>
     *
     * @param string $class
     * @return string
     * @throws InvalidArgumentException
     */
    public function translateTypeName($class)
    {
        if (preg_match('/(.*)_(.*)_Webapi_(.*)/', $class, $matches)) {
            $moduleNamespace = $matches[1] == 'Mage' ? '' : $matches[1];
            $moduleName = $matches[2];
            $typeNameParts = explode('_', $matches[3]);
            if ($moduleName == $typeNameParts[0]) {
                array_shift($typeNameParts);
            }

            return ucfirst($moduleNamespace . $moduleName . implode('', $typeNameParts));
        }

        throw new InvalidArgumentException(sprintf('Invalid parameter type "%s".', $class));
    }

    /**
     * Translate array complex type name.
     * Example:
     * <pre>
     *  ComplexTypeName[] => ArrayOfComplexType
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
     * Retrieve mapping of complex types defined in WSDL to real data classes.
     *
     * @return array
     */
    public function getSoapServerClassMap()
    {
        return $this->_soapServerClassMap;
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
        return (bool)preg_match('/\[\]$/', $type);
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
     * Retrieve list of allowed method names in action controllers.
     *
     * @return array
     */
    protected function _getAllowedMethods()
    {
        return array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE,
        );
    }
}
