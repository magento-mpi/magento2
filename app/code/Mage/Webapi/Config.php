<?php

/**
 * Web API configuration.
 *
 * This class store information about web api. Most of it is needed by REST
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Config
{
    const CACHE_ID = 'webapi';
    const KEY_OPERATIONS = 'operations';
    const VERSION_NUMBER_PREFIX = 'V';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Mage_Webapi_Config_Reader
     */
    protected $_reader;

    /**
     * Module configuration reader
     *
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var array
     */
    protected $_restServices;

    /**
     * SOAP services should be stored separately as the list of available operations
     * is collected using reflection, not taken from config as for REST
     *
     * @var  array
     */
    protected $_soapServices;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /**
     * List of SOAP operations available in the system
     *
     * @var array
     */
    protected $_soapOperations;

    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /** @var Magento_Filesystem */
    protected $_filesystem;

    /** @var Mage_Core_Model_Dir */
    protected $_dir;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     * @param Mage_Webapi_Helper_Config $helper
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Controller_Router_Route_Factory $routeFactory,
        Mage_Webapi_Helper_Config $helper,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dir
    )
    {
        $this->_config = $config;
        $this->_configCacheType = $configCacheType;
        $this->_moduleReader = $moduleReader;
        $this->_routeFactory = $routeFactory;
        $this->_helper = $helper;
        $this->_filesystem = $filesystem;
        $this->_dir = $dir;
    }

    /**
     * Retrieve list of service files from each module
     *
     * @return array
     */
    protected function _getConfigFile()
    {
        $files = $this->_moduleReader->getModuleConfigurationFiles('webapi.xml');
        return (array)$files;
    }

    /**
     * Reader object initialization
     *
     * @return Mage_Webapi_Config_Reader
     */
    protected function _getReader()
    {
        if (null === $this->_reader) {
            $configFiles = $this->_getConfigFile();
            $this->_reader = $this->_config->getModelInstance(
                'Mage_Webapi_Config_Reader',
                array('configFiles' => $configFiles)
            );
        }
        return $this->_reader;
    }


    /**
     * Return services loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getRestServices()
    {
        if (null === $this->_restServices) {
            $services = $this->_loadFromCache();
            if ($services && is_string($services)) {
                $data = unserialize($services);
            } else {
                $services = $this->_getReader()->getServices();
                $data = $this->_toArray($services);
                $this->_saveToCache(serialize($data));
            }
            $this->_restServices = isset($data['config']) ? $data['config'] : array();
        }
        return $this->_restServices;
    }

    /**
     * Load services from cache
     */
    private function _loadFromCache()
    {
        return $this->_configCacheType->load(self::CACHE_ID);
    }

    /**
     * Save services into the cache
     *
     * @param string $data serialized version of the webapi registry
     */
    protected function _saveToCache($data)
    {
        $this->_configCacheType->save($data, self::CACHE_ID);
        return $this;
    }

    /**
     * @param DOMDocument|DOMElement $root
     * @return array
     */
    protected function _toArray($root)
    {
        $result = array();

        if ($root->hasAttributes()) {
            foreach ($root->attributes as $attr) {
                $result[$attr->name] = $attr->value;
            }
        }

        $children = $root->childNodes;
        if ($children) {
            if ($children->length == 1) {
                $child = $children->item(0);
                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['value'] = $child->nodeValue;
                    if (count($result) == 1) {
                        return $result['value'];
                    } else {
                        return $result;
                    }
                }
            }

            $group = array();

            for ($i = 0; $i < $children->length; $i++) {
                $child = $children->item($i);
                $_children = & $this->_toArray($child);

                $nodeId = isset($_children['class'])
                    ? $_children['class']
                    :
                    (isset($_children['method']) ? $_children['method'] : $child->nodeName);

                if ('rest-route' === $child->nodeName) {
                    if (!isset($result[self::KEY_OPERATIONS])) {
                        $result[self::KEY_OPERATIONS] = array();
                    }
                    $nodeId = isset($_children['method']) ? $_children['method'] : $child->nodeName;
                    if (!isset($result[self::KEY_OPERATIONS][$nodeId])) {
                        $result[self::KEY_OPERATIONS][$nodeId] = $_children;
                    } else {
                        $result[self::KEY_OPERATIONS][$nodeId] = array_merge(
                            $result['operations'][$nodeId],
                            $_children
                        );
                    }

                    $result[self::KEY_OPERATIONS][$nodeId]['route'] = $result[self::KEY_OPERATIONS][$nodeId]['value'];
                    unset($result[self::KEY_OPERATIONS][$nodeId]['value']);
                } else {
                    if (!isset($result[$nodeId])) {
                        $result[$nodeId] = $_children;
                    } else {
                        if (!isset($group[$nodeId])) {
                            $tmp = $result[$nodeId];
                            $result[$nodeId] = array($tmp);
                            $group[$nodeId] = 1;
                        }
                        $result[$nodeId][] = $_children;
                    }
                }
            }
        }
        unset($result['#text']);

        return $result;
    }

    /**
     * Retrieve info about the given service
     *
     * @param string $serviceName Name
     * @throws InvalidArgumentException if the service does not exist
     */
    public function getRestService($serviceName)
    {
        if (isset($this->_restServices[$serviceName])) {
            return $this->_restServices[$serviceName];
        }

        throw new InvalidArgumentException("Service $serviceName does not exists");
    }

    /**
     * Collect the list of services with their operations available in SOAP.
     * The list of services is taken from webapi.xml configuration files.
     * The list of methods in contrast to REST is taken from PHP Interface using reflection.
     *
     * @return array
     */
    public function getSoapServices()
    {
        /** TODO: Implement caching if this approach is approved */
        if (is_null($this->_soapServices)) {
            $this->_soapServices = array();
            foreach ($this->getRestServices() as $serviceData) {
                $reflection = new ReflectionClass($serviceData['class']);
                foreach ($reflection->getMethods() as $method) {
                    /** TODO: Simplify the structure in SOAP. Currently it is unified in SOAP and REST */
                    $this->_soapServices[$serviceData['class']]['operations'][$method->getName()] = array(
                        'method' => $method->getName(),
                        'inputRequired' => (bool)$method->getNumberOfParameters()
                    );
                    $this->_soapServices[$serviceData['class']]['class'] = $serviceData['class'];
                };
            };
        }
        return $this->_soapServices;
    }

    /**
     * Retrieve info about the given operation
     *
     * @param string $serviceName
     * @param string $operation
     * @throws InvalidArgumentException if the service or operation do not exist
     */
    public function getOperation($serviceName, $operation)
    {
        $service = $this->getRestService($serviceName);

        if (isset($service[self::KEY_OPERATIONS][$operation])) {
            return $service[self::KEY_OPERATIONS][$operation];
        }

        throw new InvalidArgumentException("Operation $operation does not exist");
    }

    /**
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @return array
     * @throws Mage_Webapi_Exception
     */
    public function getRestRoutes(Mage_Webapi_Controller_Request_Rest $request)
    {
        $baseUrlRegExp = '/^\/\w+/';
        preg_match($baseUrlRegExp, $request->getPathInfo(), $matches);
        $serviceBaseUrl = isset($matches[0]) ? $matches[0] : null;
        $httpMethod = $request->getHttpMethod();

        $routes = array();
        foreach ($this->getRestServices() as $serviceName => $serviceData) {
            // skip if baseurl is not null and does not match
            if ($serviceBaseUrl != null && strtolower($serviceBaseUrl) != strtolower($serviceData['baseUrl'])) {
                // baseurl does not match, just skip this service
                continue;
            }
            // TODO: skip if version is not null and does not match
            foreach ($serviceData[self::KEY_OPERATIONS] as $operationName => $operationData) {
                if (strtoupper($operationData['httpMethod']) == strtoupper($httpMethod)) {
                    $secure = isset($operationData['secure']) ? $operationData['secure'] : false;
                    $routes[] = $this->_createRoute(
                        array(
                            'routePath' => $serviceData['baseUrl'] . $operationData['route'],
                            'version' => $request->getResourceVersion(), // TODO: Take version from config
                            'serviceId' => $serviceName,
                            'serviceMethod' => $operationName,
                            'httpMethod' => $httpMethod,
                            'secure' => $secure
                        )
                    );
                }
            }
        }

        return $routes;
    }

    /**
     * Create route object.
     *
     * @param array $routeData Expected format:
     *  <pre>array(
     *      'routePath' => '/categories/:categoryId',
     *      'httpMethod' => 'GET',
     *      'version' => 1,
     *      'serviceId' => 'Mage_Catalog_Service_CategoryService',
     *      'serviceMethod' => 'item'
     *      'secure' => true
     *  );</pre>
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _createRoute($routeData)
    {
        /** @var $route Mage_Webapi_Controller_Router_Route_Rest */
        $route = $this->_routeFactory->createRoute('Mage_Webapi_Controller_Router_Route_Rest', $routeData['routePath']);
        $route->setServiceId($routeData['serviceId'])
            ->setHttpMethod($routeData['httpMethod'])
            ->setServiceMethod($routeData['serviceMethod'])
            ->setServiceVersion(self::VERSION_NUMBER_PREFIX . $routeData['version'])
            ->setSecure($routeData['secure']);
        return $route;
    }

    /**
     * Retrieve the list of SOAP operations available in the system
     *
     * @param array $requestedResource The list of requested resources with their versions
     * @return array <pre>
     * array(
     *     array(
     *         'class' => $serviceClass,
     *         'method' => $serviceMethod
     *     ),
     *      ...
     * )</pre>
     */
    protected function _getSoapOperations($requestedResource)
    {
        if (null == $this->_soapOperations) {
            $this->_soapOperations = array();
            foreach ($this->getRequestedSoapServices($requestedResource) as $serviceData) {
                $resourceName = $this->_helper->translateResourceName($serviceData['class'], false);
                foreach ($serviceData[self::KEY_OPERATIONS] as $method => $methodData) {
                    $operationName = $resourceName . ucfirst($method);
                    $this->_soapOperations[$operationName] = array(
                        'class' => $serviceData['class'],
                        'method' => $method
                    );
                }
            }
        }
        return $this->_soapOperations;
    }

    /**
     * Retrieve the list of services corresponding to specified resources and their versions.
     *
     * @param array $requestedResources <pre>
     * array(
     *     'catalogProduct' => 'V1'
     *     'customer' => 'V2
     * )<pre/>
     * @return array Filtered list of services
     */
    public function getRequestedSoapServices($requestedResources)
    {
        $services = array();
        foreach ($requestedResources as $resourceName => $resourceVersion) {
            foreach ($this->getSoapServices() as $serviceData) {
                $resourceWithVersion = $this->_helper->translateResourceName($serviceData['class']);
                if ($resourceWithVersion != $resourceName . $resourceVersion) {
                    continue;
                }
                $services[] = $serviceData;
                /** Current service was found so no need to continue search */
                break;
            }
        }
        return $services;
    }

    /**
     * Retrieve service class name corresponding to provided SOAP operation name.
     *
     * @param string $soapOperation
     * @param array $requestedResource The list of requested resources with their versions
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getClassBySoapOperation($soapOperation, $requestedResource)
    {
        $soapOperations = $this->_getSoapOperations($requestedResource);
        if (!isset($soapOperations[$soapOperation])) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__(
                    'Operation "%s" not found.',
                    $soapOperation
                ),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $soapOperations[$soapOperation]['class'];
    }

    /**
     * Retrieve service method name corresponding to provided SOAP operation name.
     *
     * @param string $soapOperation
     * @param array $requestedResource The list of requested resources with their versions
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getMethodBySoapOperation($soapOperation, $requestedResource)
    {
        $soapOperations = $this->_getSoapOperations($requestedResource);
        if (!isset($soapOperations[$soapOperation])) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__(
                    'Operation "%s" not found.',
                    $soapOperation
                ),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $soapOperations[$soapOperation]['method'];
    }

    /**
     * Load and return Service XSD for the provided Service Class
     *
     * @param $serviceClass
     * @return DOMDocument
     */
    public function getServiceSchemaDOM($serviceClass)
    {
        /**
         * TODO: Check if Service specific XSD is already cached
         */
        $modulesDir = $this->_dir->getDir(Mage_Core_Model_Dir::MODULES);
        /** TODO: Change pattern to match interface instead of class. Think about sub-services. */
        if (!preg_match(Mage_Webapi_Model_Config_ReaderAbstract::RESOURCE_CLASS_PATTERN, $serviceClass, $matches)) {
            // TODO: Generate exception when error handling strategy is defined
        }
        $vendorName = $matches[1];
        $moduleName = $matches[2];
        /** Convert "_Catalog_Attribute" into "Catalog/Attribute" */
        $servicePath = str_replace('_', '/', ltrim($matches[3], '_'));
        $version = $matches[4];
        $schemaPath = "{$modulesDir}/{$vendorName}/{$moduleName}/etc/schema/{$servicePath}{$version}.xsd";
        if ($this->_filesystem->isFile($schemaPath)) {
            $schema = $this->_filesystem->read($schemaPath);
        } else {
            $schema = '';
        }
        //TODO: Should happen only once the cache is in place
        /** TODO: Use object manager instead of direct DOMDocument instantiation */
        $serviceSchema = new DOMDocument();
        $serviceSchema->loadXML($schema);
        return $serviceSchema;
    }
}
