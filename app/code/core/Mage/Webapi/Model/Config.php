<?php
use Zend\Code\Reflection\ClassReflection,
    Zend\Code\Reflection\DocBlockReflection,
    Zend\Server\Reflection\ReflectionMethod;

/**
 * Web API configuration.
 *
 * This class is responsible for collecting web API configuration using reflection
 * as well as for implementing interface to provide access to collected configuration.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config
{
    /**
     * Cache ID for resource config.
     */
    const CONFIG_CACHE_ID = 'API-RESOURCE-CACHE';

    const VERSION_NUMBER_PREFIX = 'V';

    /**
     * @var Zend\Code\Scanner\DirectoryScanner
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
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

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

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Config $appConfig
     * @param Mage_Core_Model_Cache $cache
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Config $appConfig,
        Mage_Core_Model_Cache $cache,
        Magento_Controller_Router_Route_Factory $routeFactory
    ) {
        $this->_autoloader = Magento_Autoload::getInstance();
        $this->_helperFactory = $helperFactory;
        $this->_helper = $this->_helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_applicationConfig = $appConfig;
        $this->_cache = $cache;
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Initialize config data.
     *
     * @return Mage_Webapi_Model_Config
     */
    public function init()
    {
        $this->_extractData();
        return $this;
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
    public function setTypeData($typeName, $data)
    {

        if (!isset($this->_data['types'][$typeName])) {
            $this->_data['types'][$typeName] = $data;
        } else {
            $this->_data['types'][$typeName] = array_merge_recursive($this->_data['types'][$typeName], $data);
        }
    }

    /**
     * Retrieve specific resource version interface data.
     *
     * Perform metadata merge from previous method versions.
     *
     * @param string $resourceName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return array
     * @throws RuntimeException
     */
    public function getResourceDataMerged($resourceName, $resourceVersion)
    {
        /** Allow to take resource version in two formats: with prefix and without it */
        $resourceVersion = is_numeric($resourceVersion)
            ? self::VERSION_NUMBER_PREFIX . $resourceVersion
            : ucfirst($resourceVersion);
        $this->_checkIfResourceVersionExists($resourceName, $resourceVersion);
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
     * Retrieve resource description for specified version.
     *
     * @param string $resourceName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return array
     * @throws InvalidArgumentException When the specified resource version does not exist.
     */
    protected function _getResourceData($resourceName, $resourceVersion)
    {
        /** Allow to take resource version in two formats: with prefix and without it */
        $resourceVersion = is_numeric($resourceVersion)
            ? self::VERSION_NUMBER_PREFIX . $resourceVersion
            : ucfirst($resourceVersion);
        try {
            $this->_checkIfResourceVersionExists($resourceName, $resourceVersion);
        } catch (RuntimeException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
        return $this->_data['resources'][$resourceName]['versions'][$resourceVersion];
    }

    /**
     * Check if specified version of resource exists. If not - exception is thrown.
     *
     * @param string $resourceName
     * @param string $resourceVersion
     * @throws RuntimeException When resource does not exist.
     */
    protected function _checkIfResourceVersionExists($resourceName, $resourceVersion)
    {
        if (!isset($this->_data['resources'][$resourceName])) {
            throw new RuntimeException($this->_helper->__('Unknown resource "%s".', $resourceName));
        }
        if (!isset($this->_data['resources'][$resourceName]['versions'][$resourceVersion])) {
            throw new RuntimeException($this->_helper->__(
                'Unknown version "%s" for resource "%s".',
                $resourceVersion,
                $resourceName
            ));
        }
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
            $version = str_replace(self::VERSION_NUMBER_PREFIX, '', $version);
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
            /** Allow to take resource version in two formats: with prefix and without it */
            $resourceVersion = is_numeric($resourceVersion)
                ? self::VERSION_NUMBER_PREFIX . $resourceVersion
                : ucfirst($resourceVersion);
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
        /** Allow to take resource version in two formats: with prefix and without it */
        $resourceVersion = is_numeric($resourceVersion)
            ? self::VERSION_NUMBER_PREFIX . $resourceVersion
            : ucfirst($resourceVersion);
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
        throw new InvalidArgumentException(sprintf(
            'The "%s" is not a valid API resource operation name.',
            $operationName
        ));
    }

    /**
     * Identify controller class by operation name.
     *
     * @param string $operationName
     * @return string Resource name on success
     * @throws LogicException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
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
     *     'removed'      => true,            // either 'deprecated' or 'removed' item must be specified
     *     'deprecated'   => true,
     *     'use_resource' => 'operationName'  // resource to be used instead
     *     'use_method'   => 'operationName'  // method to be used instead
     *     'use_version'  => N,               // version of method to be used instead
     * )
     * </pre>
     *
     * @param string $resourceName
     * @param string $method
     * @param string $resourceVersion
     * @return array|bool On success array with policy details; false otherwise.
     * @throws InvalidArgumentException
     */
    public function getDeprecationPolicy($resourceName, $method, $resourceVersion)
    {
        $deprecationPolicy = false;
        $resourceData = $this->_getResourceData($resourceName, $resourceVersion);
        if (!isset($resourceData['methods'][$method])) {
            throw new InvalidArgumentException(sprintf(
                'Method "%s" does not exist in "%s" version of resource "%s".',
                $method,
                $resourceVersion,
                $resourceName
            ));
        }
        $methodData = $resourceData['methods'][$method];
        if (isset($methodData['deprecation_policy']) && is_array($methodData['deprecation_policy'])) {
            $deprecationPolicy = $methodData['deprecation_policy'];
        }
        return $deprecationPolicy;
    }

    /**
     * Extract configuration data from the action controllers files.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _extractData()
    {
        if (is_null($this->_data)) {
            $this->_populateClassMap();

            if ($this->_loadDataFromCache()) {
                return $this;
            }

            $allRestRoutes = array();
            $serverReflection = new Zend\Server\Reflection;
            foreach ($this->_autoLoaderClassMap as $className => $filename) {
                if (preg_match('/(.*)_Webapi_(.*)Controller*/', $className)) {
                    $data = array();
                    $data['controller'] = $className;
                    $data['versions'] = array();
                    /** @var ReflectionMethod $methodReflection */
                    foreach ($serverReflection->reflectClass($className)->getMethods() as $methodReflection) {
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
                throw new LogicException('Cannot populate config - no action controllers were found.');
            }
            $this->_saveDataToCache();
        }
    }

    /**
     * Load config data from cache.
     *
     * @return bool Return true on successful load; false otherwise
     */
    protected function _loadDataFromCache()
    {
        $isLoaded = false;
        if ($this->_cache->canUse(Mage_Webapi_Controller_Handler_Soap::WEBSERVICE_CACHE_NAME)) {
            $cachedData = $this->_cache->load(self::CONFIG_CACHE_ID);
            if ($cachedData !== false) {
                $this->_data = unserialize($cachedData);
                $isLoaded = true;
            }
        }
        return $isLoaded;
    }

    /**
     * Save data to cache if it is enabled.
     */
    protected function _saveDataToCache()
    {
        if ($this->_cache->canUse(Mage_Webapi_Controller_Handler_Soap::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save(
                serialize($this->_data),
                self::CONFIG_CACHE_ID,
                array(Mage_Webapi_Controller_Handler_Soap::WEBSERVICE_CACHE_TAG)
            );
        }
    }

    /**
     * Identify API method version by its reflection.
     *
     * @param ReflectionMethod $methodReflection
     * @return string|bool Method version with prefix on success.
     *      false is returned in case when method should not be exposed via API.
     */
    protected function _getMethodVersion(ReflectionMethod $methodReflection)
    {
        $methodVersion = false;
        $methodNameWithSuffix = $methodReflection->getName();
        $regularExpression = $this->_getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $methodVersion = ucfirst($methodMatches[2]);
        }
        return $methodVersion;
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
        $regularExpression = $this->_getMethodNameRegularExpression();
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

        foreach ($this->_getAdditionalRequiredParamNames($methodReflection) as $additionalRequired) {
            $routePath .= "/$additionalRequired/:$additionalRequired";
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
        $collection = Mage_Webapi_Controller_Handler_Rest::ACTION_TYPE_COLLECTION;
        $item = Mage_Webapi_Controller_Handler_Rest::ACTION_TYPE_ITEM;
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
     * It can happen due creation of routes with optional parameters. HTTP get parameters can be used for that.
     */
    protected function _getPathCombinations($optionalParams, $basePath)
    {
        $pathCombinations = array();
        /** Add current base path to the resulting array of routes. */
        $pathCombinations[] = $basePath;
        foreach ($optionalParams as $key => $paramName) {
            /** Add current param name to the route path and make recursive call. */
            $paramsWithoutCurrent = $optionalParams;
            unset($paramsWithoutCurrent[$key]);
            $currentPath = "$basePath/$paramName/:$paramName";
            $pathCombinations = array_merge(
                $pathCombinations,
                $this->_getPathCombinations(
                    $paramsWithoutCurrent,
                    $currentPath
                )
            );
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
        /** Take the fullest interface that includes optional parameters also. */
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
        /** Take the fullest interface that includes optional parameters also. */
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
        if (!$this->_isSubresource($methodReflection)) {
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
     * Identify if method expects Parent resource ID to be present in the request.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return bool
     */
    protected function _isParentResourceIdExpected(ReflectionMethod $methodReflection)
    {
        $isIdFieldExpected = false;
        if ($this->_isSubresource($methodReflection)) {
            $methodsWithParentId = array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE,
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE,
            );
            $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
            if (in_array($methodName, $methodsWithParentId)) {
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
        $methodsWithId = array(
            Mage_Webapi_Controller_ActionAbstract::METHOD_GET,
            Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
        );
        $methodName = $this->getMethodNameWithoutVersionSuffix($methodReflection);
        if (in_array($methodName, $methodsWithId)) {
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
            throw new InvalidArgumentException(sprintf('"%s" is not a valid resource class.', $className));
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
            throw new InvalidArgumentException(sprintf(
                'The "%s" method of "%s" resource in version "%s" is not registered.',
                $methodName,
                $resourceName,
                $resourceVersion
            ));
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
        $apiTypeRoutePath = Mage_Webapi_Controller_Router_Route_Webapi::API_AREA_NAME
            . '/:' . Mage_Webapi_Controller_Front::API_TYPE_REST;
        $fullRoutePath = $apiTypeRoutePath . $routePath;
        /** @var $route Mage_Webapi_Controller_Router_Route_Rest */
        $route = $this->_routeFactory->createRoute('Mage_Webapi_Controller_Router_Route_Rest', $fullRoutePath);
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
        foreach ($this->getDirectoryScanner()->getFiles(true) as $file) {
            $filename = $file->getFile();
            $classes = $file->getClasses();
            if (count($classes) > 1) {
                throw new LogicException(sprintf(
                    'There can be only one class in the "%s" controller file .',
                    $filename
                ));
            }
            /** @var \Zend\Code\Scanner\ClassScanner $class */
            $class = reset($classes);
            $baseDir = $this->_applicationConfig->getOptions()->getBaseDir() . DIRECTORY_SEPARATOR;
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
        /** Take the fullest interface that also includes optional parameters. */
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
        $deprecationPolicy = $this->_extractDeprecationPolicy($method);
        if ($deprecationPolicy) {
            $methodData['deprecation_policy'] = $deprecationPolicy;
        }
        return $methodData;
    }

    /**
     * Extract method deprecation policy.
     *
     * Return result in the following format:<pre>
     * array(
     *     'removed'      => true,            // either 'deprecated' or 'removed' item must be specified
     *     'deprecated'   => true,
     *     'use_resource' => 'operationName'  // resource to be used instead
     *     'use_method'   => 'operationName'  // method to be used instead
     *     'use_version'  => N,               // version of method to be used instead
     * )
     * </pre>
     *
     * @param ReflectionMethod $methodReflection
     * @return array|bool On success array with policy details; false otherwise.
     * @throws LogicException If deprecation tag format is incorrect.
     */
    protected function _extractDeprecationPolicy(ReflectionMethod $methodReflection)
    {
        $deprecationPolicy = false;
        $methodDocumentation = $methodReflection->getDocComment();
        if ($methodDocumentation) {
            /** Zend server reflection is not able to work with annotation tags of the method. */
            $docBlock = new DocBlockReflection($methodDocumentation);
            $removedTag = $docBlock->getTag('apiRemoved');
            $deprecatedTag = $docBlock->getTag('apiDeprecated');
            if ($removedTag) {
                $deprecationPolicy = array('removed' => true);
                $useMethod = $removedTag->getContent();
            } elseif ($deprecatedTag) {
                $deprecationPolicy = array('deprecated' => true);
                $useMethod = $deprecatedTag->getContent();
            }

            if (isset($useMethod) && is_string($useMethod) && !empty($useMethod)) {
                $invalidFormatMessage = sprintf(
                    'The "%s" method has invalid format of Deprecation policy. '
                        . 'Accepted formats are createV1, catalogProduct::createV1 '
                        . 'and Mage_Catalog_Webapi_ProductController::createV1.',
                    $methodReflection->getDeclaringClass()->getName() . '::' . $methodReflection->getName()
                );
                /** Add information about what method should be used instead of deprecated/removed one. */
                /**
                 * Description is expected in one of the following formats:
                 * - Mage_Catalog_Webapi_ProductController::createV1
                 * - catalogProduct::createV1
                 * - createV1
                 */
                $useMethodParts = explode('::', $useMethod);
                switch (count($useMethodParts)) {
                    case 2:
                        try {
                            /** Support of: Mage_Catalog_Webapi_ProductController::createV1 */
                            $resourceName = $this->translateResourceName($useMethodParts[0]);
                        } catch (InvalidArgumentException $e) {
                            /** Support of: catalogProduct::createV1 */
                            $resourceName = $useMethodParts[0];
                        }
                        $deprecationPolicy['use_resource'] = $resourceName;
                        $methodName = $useMethodParts[1];
                        break;
                    case 1:
                        $methodName = $useMethodParts[0];
                        /** If resource was not specified, current one should be used. */
                        $deprecationPolicy['use_resource'] = $this->translateResourceName(
                            $methodReflection->getDeclaringClass()->getName()
                        );
                        break;
                    default:
                        throw new LogicException($invalidFormatMessage);
                        break;
                }
                try {
                    $methodWithoutVersion = $this->getMethodNameWithoutVersionSuffix($methodName);
                } catch (Exception $e) {
                    throw new LogicException($invalidFormatMessage);
                }
                $deprecationPolicy['use_method'] = $methodWithoutVersion;
                $methodVersion = str_replace($methodWithoutVersion, '', $methodName);
                $deprecationPolicy['use_version'] = ucfirst($methodVersion);
            }
        }
        return $deprecationPolicy;
    }

    /**
     * Process type name.
     * In case parameter type is a complex type (class) - process its properties.
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
        $this->_data['types'][$typeName] = array();
        if ($this->isArrayType($class)) {
            $this->_processType($this->getArrayItemType($class));
        } else {
            if (!$this->_autoloader->classExists($class)) {
                throw new InvalidArgumentException(sprintf('Could not load the "%s" class as parameter type.', $class));
            }
            $reflection = new ClassReflection($class);
            $docBlock = $reflection->getDocBlock();
            $this->_data['types'][$typeName]['documentation'] = $docBlock ? $this->_getDescription($docBlock) : '';
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
                    'documentation' => $varInlineDoc . $this->_getDescription($propertyDocBlock)
                );
            }
        }

        return $this->_data['types'][$typeName];
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
        throw new InvalidArgumentException(sprintf('The controller class name "%s" is invalid.', $className));
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
     * Retrieve list of allowed method names in action controllers.
     *
     * @return array
     */
    protected function _getAllowedMethods()
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
            if ($routeMetadata['actionType'] == Mage_Webapi_Controller_Handler_Rest::ACTION_TYPE_ITEM
                && $routeMetadata['resourceName'] == $resourceName
            ) {
                return $routePath;
            }
        }
        throw new InvalidArgumentException(sprintf('No route to the item of "%s" resource was found.', $resourceName));
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
        $resourceData = $this->_getResourceData($resourceName, $version);
        if (!isset($resourceData['methods'][$methodName]['rest_routes'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'The "%s" resource does not have any REST routes for "%s" method.',
                    $resourceName,
                    $methodName
                ));
        }
        $routes = array();
        foreach ($resourceData['methods'][$methodName]['rest_routes'] as $routePath) {
            $routes[] = $this->_createRoute($routePath, $resourceName, $this->getActionTypeByMethod($methodName));
        }
        return $routes;
    }

    /**
     * Get current directory scanner. Initialize if it was not initialized previously.
     *
     * @return Zend\Code\Scanner\DirectoryScanner
     */
    public function getDirectoryScanner()
    {
        if (!$this->_directoryScanner) {
            $this->_directoryScanner = new Zend\Code\Scanner\DirectoryScanner();
            /** @var Mage_Core_Model_Config_Element $module */
            foreach ($this->_applicationConfig->getNode('modules')->children() as $moduleName => $module) {
                if ($module->is('active')) {
                    $directory = $this->_applicationConfig->getModuleDir('controllers', $moduleName) . DS . 'Webapi';
                    if (is_dir($directory)) {
                        $this->_directoryScanner->addDirectory($directory);
                    }
                }
            }
        }
        return $this->_directoryScanner;
    }

    /**
     * Set directory scanner object.
     *
     * @param Zend\Code\Scanner\DirectoryScanner $directoryScanner
     */
    public function setDirectoryScanner(Zend\Code\Scanner\DirectoryScanner $directoryScanner)
    {
        $this->_directoryScanner = $directoryScanner;
    }
}
