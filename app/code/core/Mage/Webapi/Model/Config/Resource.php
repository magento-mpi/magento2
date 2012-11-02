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
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * Resources configuration data.
     *
     * @var array
     */
    protected $_data;

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

        if (isset($options['helper']) && $options['helper'] instanceof Mage_Webapi_Helper_Data) {
            $this->_helper = $options['helper'];
        } else {
            $this->_helper = Mage::helper('Mage_Webapi_Helper_Data');
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
        $this->_extractData();
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
        if (!isset($this->_data['types'][$typeName])) {
            throw new InvalidArgumentException(sprintf('Data type "%s" was not found in config.', $typeName));
        }
        return $this->_data['types'][$typeName];
    }

    /**
     * Add or update type data in config.
     *
     * @param string $typeName
     * @param array $data
     */
    public function setTypeData($typeName, $data) {

        if (!isset($this->_data['types'][$typeName])) {
            $this->_data['types'][$typeName] = $data;
        } else {
            $this->_data['types'][$typeName] = array_merge_recursive($this->_data['types'][$typeName], $data);
        }
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
        if (!isset($this->_data['resources'][$resourceName])) {
            throw new RuntimeException($this->_helper->__('Unknown resource "%s".', $resourceName));
        }
        /** Allow to take resource version in two formats: with 'v' prefix and without it */
        $resourceVersion = is_numeric($resourceVersion) ? 'v' . $resourceVersion : lcfirst($resourceVersion);
        if (!isset($this->_data['resources'][$resourceName]['versions'][$resourceVersion])) {
            throw new RuntimeException($this->_helper->__('Unknown version "%s" for resource "%s".', $resourceVersion,
                $resourceName));
        }

        $resource = array();
        foreach ($this->_data['resources'][$resourceName]['versions'] as $version => $data) {
            $resource = array_replace_recursive($resource, $data);
            if ($version == $resourceVersion) {
                break;
            }
        }

        return $resource;
    }

    /**
     * Identify the maximum version of the specified resource available.
     *
     * @param string $resourceName
     * @return int
     * @throws InvalidArgumentException When resource with the specified name does not exist.
     */
    public function getResourceMaxVersion($resourceName)
    {
        if (!isset($this->_data['resources'][$resourceName])) {
            throw new InvalidArgumentException(sprintf('Resource "%s" does not exist.', $resourceName));
        }
        $resourceVersions = array_keys($this->_data['resources'][$resourceName]['versions']);
        foreach ($resourceVersions as &$version) {
            $version = str_replace('v', '', $version);
        }
        $maxVersion = max($resourceVersions);
        return (int)$maxVersion;
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
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        $resourceExists = isset($this->_data['resources'][$resourceName]);
        if (!$resourceExists) {
            return false;
        }
        $resourceData = $this->_data['resources'][$resourceName];
        $versionCheckRequired = is_string($resourceVersion);
        if ($versionCheckRequired) {
            /** Allow to take resource version in two formats: with 'v' prefix and without it */
            $resourceVersion = is_numeric($resourceVersion) ? 'v' . $resourceVersion : $resourceVersion;
            $resourceVersion = lcfirst($resourceVersion);
            $operationIsValid = isset($resourceData['versions'][$resourceVersion]['methods'][$methodName]);
            if (!$operationIsValid) {
                return false;
            }
        }
        return $resourceName;
    }

    /**
     * Identify method name by operation name.
     *
     * @param string $operationName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return string|bool Method name on success; false on failure
     */
    public function getMethodNameByOperation($operationName, $resourceVersion = null)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        $versionCheckRequired = is_string($resourceVersion);
        if (!$versionCheckRequired) {
            return $methodName;
        }
        /** Allow to take resource version in two formats: with 'v' prefix and without it */
        $resourceVersion = is_numeric($resourceVersion) ? 'v' . $resourceVersion : $resourceVersion;
        $resourceVersion = lcfirst($resourceVersion);
        return isset($this->_data['resources'][$resourceName]['versions'][$resourceVersion]['methods'][$methodName])
            ? $methodName : false;
    }

    /**
     * Parse operation name to separate resource name from method name.
     *
     * <pre>Result format:
     * array(
     *      0 => 'resourceName',
     *      1 => 'methodName'
     * )</pre>
     *
     * @param string $operationName
     * @return array
     * @throws InvalidArgumentException In case when the specified operation name is invalid.
     */
    protected function _parseOperationName($operationName)
    {
        /** Note that '(.*?)' must not be greedy to allow regexp to match 'multiUpdate' method before 'update' */
        $regEx = sprintf('/(.*?)(%s)$/i', implode('|', $this->_getAllowedMethods()));
        if (preg_match($regEx, $operationName, $matches)) {
            $resourceName = $matches[1];
            $methodName = lcfirst($matches[2]);
            $result = array($resourceName, $methodName);
            return $result;
        }
        throw new InvalidArgumentException(sprintf('The "%s" is not valid API resource operation name.',
            $operationName));
    }

    /**
     * Identify controller class by operation name and its version.
     *
     * @param string $operationName
     * @return string Resource name on success
     * @throws LogicException
     */
    public function getControllerClassByOperationName($operationName)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        if (isset($this->_data['resources'][$resourceName]['controller'])) {
            return $this->_data['resources'][$resourceName]['controller'];
        }
        throw new LogicException(sprintf('Resource "%s" must have associated controller class.', $resourceName));
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
        return false;
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

            $allRestRoutes = array();
            foreach ($this->_autoLoaderClassMap as $className => $filename) {
                if (preg_match('/(.*)_Webapi_(.*)Controller*/', $className)) {
                    $data = array();
                    $data['controller'] = $className;
                    $data['versions'] = array();
                    /** @var ReflectionMethod $methodReflection */
                    foreach ($this->_serverReflection->reflectClass($className)->getMethods() as $methodReflection) {
                        try {
                            $method = $this->getMethodNameWithoutVersionSuffix($methodReflection);
                        } catch (InvalidArgumentException $e) {
                            /** Resources can contain methods that should not be exposed through API. */
                            continue;
                        }
                        $version = $this->_getMethodVersion($methodReflection);
                        if ($version) {
                            $methodMetaData = $this->_extractMethodData($methodReflection);
                            $data['versions'][$version]['methods'][$method] = $methodMetaData;
                            $restRoutes = $this->generateRestRoutes($methodReflection);
                            $data['versions'][$version]['methods'][$method]['rest_routes'] = array_keys($restRoutes);
                            $allRestRoutes = array_merge($allRestRoutes, $restRoutes);
                        }
                    }
                    // Sort versions array for further fallback.
                    ksort($data['versions']);
                    $this->_data['resources'][$this->translateResourceName($className)] = $data;
                }
            }
            $this->_data['rest_routes'] = $allRestRoutes;

            if (!isset($this->_data['resources'])) {
                throw new LogicException('Can not populate config - no action controllers were found.');
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
     * @return string Method name without version suffix on success.
     * @throws InvalidArgumentException When method name is invalid API resource method.
     */
    public function getMethodNameWithoutVersionSuffix(ReflectionMethod $methodReflection)
    {
        $methodNameWithSuffix = $methodReflection->getName();
        $regularExpression = $this->_getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $methodName = $methodMatches[1];
            return $methodName;
        }
        throw new InvalidArgumentException(sprintf('"%s" is invalid API resource method.', $methodNameWithSuffix));
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
    public function generateRestRoutes(ReflectionMethod $methodReflection)
    {
        // TODO: Implement @restRoute annotations processing for adding custom routes
        $routes = array();
        $routePath = "/:" . Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION;
        $routeParts = $this->getResourceNameParts($methodReflection->getDeclaringClass()->getName());
        $partsCount = count($routeParts);
        for ($i = 0; $i < $partsCount; $i++) {
            if ($this->_isParentResourceIdExpected($methodReflection)
                /**
                 * In case of subresource route, parent ID must be specified before the last route part.
                 * E.g.: /v1/grandParent/parent/:parentId/resource
                 */
                && ($i == ($partsCount - 1))
            ) {
                $routePath .= "/:" . Mage_Webapi_Controller_Router_Route_Rest::PARAM_PARENT_ID;
            }
            $routePath .= "/" . lcfirst($this->_helper->convertSingularToPlural($routeParts[$i]));
        }
        if ($this->_isResourceIdExpected($methodReflection)) {
            $routePath .= "/:" . Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID;
        }

        foreach ($this->_getAdditionalRequiredParamNames($methodReflection) as $additionalRequiredParam) {
            $routePath .= "/$additionalRequiredParam/:$additionalRequiredParam";
        }

        $actionType = $this->getActionTypeByMethod($this->getMethodNameWithoutVersionSuffix($methodReflection));
        $resourceName = $this->translateResourceName($methodReflection->getDeclaringClass()->getName());
        $optionalParams = $this->_getOptionalParamNames($methodReflection);
        foreach ($this->_getPathCombinations($optionalParams, $routePath) as $finalRoutePath) {
            $routes[$finalRoutePath] = array('actionType' => $actionType, 'resourceName' => $resourceName);
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
    public function getActionTypeByMethod($methodName)
    {
        // TODO: Remove dependency on Mage_Webapi_Controller_Front_Rest
        $collection = Mage_Webapi_Controller_Front_Rest::ACTION_TYPE_COLLECTION;
        $item = Mage_Webapi_Controller_Front_Rest::ACTION_TYPE_ITEM;
        $methodToActionTypeMap = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_LIST => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE => $collection,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE => $item,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE => $collection,
        );
        if (!isset($methodToActionTypeMap[$methodName])) {
            throw new InvalidArgumentException(sprintf('"%s" method is not valid resource method.', $methodName));
        }
        return $methodToActionTypeMap[$methodName];
    }


    /**
     * Generate list of possible routes taking into account optional params.
     *
     * Note: this is called recursively.
     *
     * @param array $optionalParams
     * @param string $basePath
     * @return array List of possible route params
     */
    /**
     * TODO: Assure that performance is not heavily impacted during routes match process.
     * TODO: It can happen due creation of routes with optional parameters. HTTP get parameters can be used for that.
     */
    protected function _getPathCombinations($optionalParams, $basePath)
    {
        $pathCombinations = array();
        /** Add current base path to the resulting array of routes. */
        $pathCombinations[] = $basePath;
        foreach ($optionalParams as $key => $paramName) {
            /** Add current param name to the route path and make recursive call. */
            $optionalParamsWithoutCurrent = $optionalParams;
            unset($optionalParamsWithoutCurrent[$key]);
            $currentPath = "$basePath/$paramName/:$paramName";
            $pathCombinations = array_merge($pathCombinations, $this->_getPathCombinations(
                $optionalParamsWithoutCurrent, $currentPath));
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
     * Retrieve the list of names of required params except ID and Request body.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     */
    protected function _getAdditionalRequiredParamNames(ReflectionMethod $methodReflection)
    {
        $paramNames = array();
        $methodInterfaces = $methodReflection->getPrototypes();
        /** Take the most full interface, that includes optional parameters also. */
        /** @var \Zend\Server\Reflection\Prototype $methodInterface */
        $methodInterface = end($methodInterfaces);
        $methodParams = $methodInterface->getParameters();
        $idParamName = $this->getIdParamName($methodReflection);
        $bodyParamName = $this->getBodyParamName($methodReflection);
        /** @var ReflectionParameter $paramReflection */
        foreach ($methodParams as $paramReflection) {
            if (!$paramReflection->isOptional()
                && $paramReflection->getName() != $bodyParamName
                && $paramReflection->getName() != $idParamName
            ) {
                $paramNames[] = $paramReflection->getName();
            }
        }
        return $paramNames;
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
        $bodyPositionCreate = 1;
        $bodyPositionUpdate = 2;
        $bodyPositionMultiUpdate = 1;
        $bodyPositionMultiDelete = 1;
        /**#@-*/
        $bodyParamPositions = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE => $bodyPositionCreate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE => $bodyPositionUpdate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE => $bodyPositionMultiUpdate,
            Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE => $bodyPositionMultiDelete
        );
        $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
        $isBodyExpected = isset($bodyParamPositions[$methodName]);
        if ($isBodyExpected) {
            $bodyParamPosition = $bodyParamPositions[$methodName];
            if ($this->_isSubresource($methodReflection)
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
                throw new LogicException(sprintf('Method "%s" must have parameter for passing request body. '
                    . 'Its position must be "%s" in method interface.', $methodReflection->getName(),
                    $bodyParamPosition));
            }
            /** @var $bodyParamReflection \Zend\Code\Reflection\ParameterReflection */
            /** Param position in the array should be counted from 0. */
            $bodyParamReflection = $methodParams[$bodyParamPosition-1];
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
        if (!$this->_isSubresource($methodReflection)) {
            /** Top level resource, not subresource */
            $methodsWithIdExpected = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
            );
            $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithIdExpected)) {
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
                throw new LogicException(sprintf('Method "%s" must have at least one parameter: resource ID.',
                    $methodReflection->getName()));
            }
            /** @var ReflectionParameter $idParam */
            $idParam = reset($methodParams);
            $idParamName = $idParam->getName();
        }
        return $idParamName;
    }

    /**
     * Identify if method expects Parent resource ID to be present in the request.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return bool
     */
    protected function _isParentResourceIdExpected(ReflectionMethod $methodReflection)
    {
        $isIdFieldExpected = false;
        if ($this->_isSubresource($methodReflection)) {
            $methodsWithParentIdExpected = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE,
            );
            $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithParentIdExpected)) {
                $isIdFieldExpected = true;
            }
        }
        return $isIdFieldExpected;
    }

    /**
     * Identify if method expects Resource ID to be present in the request.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return bool
     */
    protected function _isResourceIdExpected(ReflectionMethod $methodReflection)
    {
        $isIdFieldExpected = false;
        $methodsWithIdExpected = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_RETRIEVE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
        );
        $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
        if (in_array($methodName, $methodsWithIdExpected)) {
            $isIdFieldExpected = true;
        }
        return $isIdFieldExpected;
    }

    /**
     * Identify if API resource is top level resource or subresource.
     *
     * @param ReflectionMethod $methodReflection
     * @return bool
     * @throws InvalidArgumentException In case when class name is not valid API resource class.
     */
    protected function _isSubresource(ReflectionMethod $methodReflection)
    {
        $className = $methodReflection->getDeclaringClass()->getName();
        preg_match('/.*_Webapi_(.*)Controller*/', $className, $matches);
        if (!isset($matches[1])) {
            throw new InvalidArgumentException(sprintf('"%s" is not valid resource class.', $className));
        }
        return count(explode('_', $matches[1])) > 1;
    }

    /**
     * Retrieve method metadata.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return array
     * @throws InvalidArgumentException If specified method was not previously registered in API config.
     */
    public function getMethodMetadata(ReflectionMethod $methodReflection)
    {
        $resourceName = $this->translateResourceName($methodReflection->getDeclaringClass()->getName());
        $resourceVersion = $this->_getMethodVersion($methodReflection);
        $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);

        if (!isset($this->_data['resources'][$resourceName]['versions'][$resourceVersion]['methods'][$methodName])) {
            throw new InvalidArgumentException(sprintf('"%s" method of "%s" resource in version "%s" is not registered.',
                $methodName, $resourceName, $resourceVersion));
        }
        return $this->_data['resources'][$resourceName]['versions'][$resourceVersion]['methods'][$methodName];
    }

    /**
     * Get all modules routes defined in config.
     *
     * @return Mage_Webapi_Controller_Router_Route_Rest[]
     * @throws LogicException When config data has invalid structure.
     */
    public function getAllRestRoutes()
    {
        $routes = array();
        foreach ($this->_data['rest_routes'] as $routePath => $routeData) {
            $routes[] = $this->_createRoute($routePath, $routeData['resourceName'], $routeData['actionType']);
        }
        return $routes;
    }

    /**
     * Create route object.
     *
     * @param string $routePath
     * @param string $resourceName
     * @param string $actionType
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _createRoute($routePath, $resourceName, $actionType)
    {
        $apiTypeRoutePath = str_replace(':api_type', 'rest', Mage_Webapi_Controller_Router_Route_ApiType::API_ROUTE);
        $fullRoutePath = $apiTypeRoutePath . $routePath;
        // TODO: Change to dependency injection
        $route = new Mage_Webapi_Controller_Router_Route_Rest($fullRoutePath);
        $route->setResourceName($resourceName)->setResourceType($actionType);
        return $route;
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
            $baseDir = $this->_applicationConfig->getOptions()->getBaseDir() . DS;
            $relativePath = str_replace($baseDir, '', $filename);
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
    protected function _extractMethodData(ReflectionMethod $method)
    {
        $methodData = array('documentation' => $method->getDescription());
        $prototypes = $method->getPrototypes();
        /** Take the most full interface, that also includes optional parameters. */
        /** @var \Zend\Server\Reflection\Prototype $prototype */
        $prototype = end($prototypes);
        /** @var \Zend\Server\Reflection\ReflectionParameter $parameter */
        foreach ($prototype->getParameters() as $parameter) {
            $parameterData = array(
                'type' => $this->_processType($parameter->getType()),
                'required' => !$parameter->isOptional(),
                'documentation' => $parameter->getDescription(),
            );
            if ($parameter->isOptional()) {
                $parameterData['default'] = $parameter->getDefaultValue();
            }
            $methodData['interface']['in']['parameters'][$parameter->getName()] = $parameterData;
        }
        if ($prototype->getReturnType() != 'void') {
            $methodData['interface']['out']['parameters']['result'] = array(
                'type' => $this->_processType($prototype->getReturnType()),
                'documentation' => $prototype->getReturnValue()->getDescription(),
                'required' => true,
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
            if (!isset($this->_data['types'][$complexTypeName])) {
                $this->_processComplexType($type);
                if (!$this->isArrayType($complexTypeName)) {
                    $this->_data['type_to_class_map'][$complexTypeName] = $type;
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
        // TODO: Think if array data type should be present here, currently it is added with empty metadata
        $this->_data['types'][$typeName] = array();
        if ($this->isArrayType($class)) {
            $this->_processType($this->getArrayItemType($class));
        } else {
            if (!$this->_autoloader->classExists($class)) {
                throw new InvalidArgumentException(sprintf('Could not load class "%s" as parameter type.', $class));
            }
            $reflection = new ClassReflection($class);
            $docBlock = $reflection->getDocBlock();
            $this->_data['types'][$typeName]['documentation'] = $docBlock ? $docBlock->getLongDescription() : '';
            $defaultProperties = $reflection->getDefaultProperties();
            /** @var \Zend\Code\Reflection\PropertyReflection $property */
            foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                $propertyDocBlock = $property->getDocBlock();
                if (!$propertyDocBlock) {
                    throw new InvalidArgumentException('Each property must have description with @var annotation.');
                }
                $varTags = $propertyDocBlock->getTags('var');
                if (empty($varTags)) {
                    throw new InvalidArgumentException('Property type must be defined with @var tag.');
                }
                /** @var \Zend\Code\Reflection\DocBlock\Tag\GenericTag $varTag */
                $varTag = current($varTags);
                $varContentParts = explode(' ', $varTag->getContent(), 2);
                $varType = current($varContentParts);
                $varInlineDoc = (count($varContentParts) > 1) ? end($varContentParts) : '';
                $optionalTags = $propertyDocBlock->getTags('optional');
                if (!empty($optionalTags)) {
                    /** @var \Zend\Code\Reflection\DocBlock\Tag\GenericTag $isOptionalTag */
                    $isOptionalTag = current($optionalTags);
                    $isOptional = $isOptionalTag->getName() == 'optional';
                } else {
                    $isOptional = false;
                }
                $this->_data['types'][$typeName]['parameters'][$propertyName] = array(
                    'type' => $this->_processType($varType),
                    'required' => !$isOptional && is_null($defaultProperties[$propertyName]),
                    'default' => $defaultProperties[$propertyName],
                    'documentation' => $varInlineDoc . $propertyDocBlock->getLongDescription()
                );
            }
        }

        return $this->_data['types'][$typeName];
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
    public function getResourceNameParts($className)
    {
        if (preg_match('/^(.*)_Webapi_(.*)Controller$/', $className, $matches)) {
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
     *
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
    public function getTypeToClassMap()
    {
        return $this->_data['type_to_class_map'];
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

    /**
     * Identify the shortest available route to the item of specified resource.
     *
     * @param string $resourceName
     * @return string
     * @throws InvalidArgumentException
     */
    public function getRestRouteToItem($resourceName)
    {
        $restRoutes = $this->_data['rest_routes'];
        /** The shortest routes must go first. */
        ksort($restRoutes);
        foreach ($restRoutes as $routePath => $routeMetadata) {
            if ($routeMetadata['actionType'] == Mage_Webapi_Controller_Front_Rest::ACTION_TYPE_ITEM
                && $routeMetadata['resourceName'] == $resourceName
            ) {
                return $routePath;
            }
        }
        throw new InvalidArgumentException(sprintf('No route was found to the item of "%s" resource.', $resourceName));
    }

    /**
     * Retrieve the list of all resources with their versions.
     *
     * @return array
     */
    public function getAllResourcesVersions()
    {
        $resources = array();
        foreach ($this->_data['resources'] as $resourceName => $data) {
            $resources[$resourceName] = array_keys($data['versions']);
        }

        return $resources;
    }

    /**
     * Retrieve a list of all route objects associated with specified method.
     *
     * @param string $resourceName
     * @param string $methodName
     * @param string $version
     * @return Mage_Webapi_Controller_Router_Route_Rest[]
     * @throws InvalidArgumentException
     */
    public function getMethodRestRoutes($resourceName, $methodName, $version)
    {
        $resourceData = $this->getResource($resourceName, $version);
        if (!isset($resourceData['methods'][$methodName]['rest_routes'])) {
            throw new InvalidArgumentException(
                sprintf('"%s" resource does not have any REST routes for "%s" method.', $resourceName, $methodName));
        }
        $routes = array();
        foreach ($resourceData['methods'][$methodName]['rest_routes'] as $routePath) {
            $routes[] = $this->_createRoute($routePath, $resourceName, $this->getActionTypeByMethod($methodName));
        }
        return $routes;
    }
}
