<?php
/**
 * Service Registry
 *
 * This class is the primary interface to obtain metadata on services and methods available
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Service_Registry
{
    const WEBSERVICE_CACHE_NAME = 'config_webservice';
    const WEBSERVICE_CACHE_TAG = 'WEBSERVICE';
    const CONFIG_CACHE_ID = 'API-RESOURCE-CACHE';

    private static $_registry;
    private $_services  = array();

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct ()
    {
        $this->loadFromCache();
    }

    /**
     * Implements a singleton pattern
     *
     * @return Mage_Core_Service_Registry
     */
    public static function getInstance ()
    {
        if (! self::$_registry) {
            self::$_registry = new Mage_Core_Service_Registry();
        }
        return self::$_registry;
    }

    /**
     * Get a object instance corresponding to the requested Service
     *
     * @param string serviceName name of the service to lookup
     * @param string serviceVersion
     * @return mixed the type will be matching the Service requested
     * @throws InvalidArgumentException if the service does not exist
     */
    public function instantiateService ($serviceName, $serviceVersion)
    {
        $serviceMetadata = $this->getService($serviceName, $serviceVersion);
        return $serviceMetadata->instantiate();
    }


    /**
     * Add one more service into the registry
     *
     * @param string serviceName
     * @param string serviceVersion
     * @param string module such as Catalog
     */
    public function addService ($serviceName, $serviceVersion, $module)
    {
        try {
            $serviceMetadata = $this->getService($serviceName, $serviceVersion);
        }
        catch (InvalidArgumentException $e) {
            $this->_services[$serviceName][$serviceVersion] = array(
                'name' => $serviceName,
                'version' => $serviceVersion,
                'module' => $module,
                'methods' => array()
            );
        }
    }

    /**
     * Lookup metadata of a given service
     *
     * @param string serviceName name of the service to lookup
     * @param string serviceVersion
     * @return Mage_Core_Service_Registry_ServiceMetadata
     * @throws InvalidArgumentException if the service does not exist
     */
    public function getService ($serviceName, $serviceVersion)
    {
        if (isset($this->_services[$serviceName])
            && isset($this->_services[$serviceName][$serviceVersion])) {
            $service = $this->_services[$serviceName][$serviceVersion];
            $service['classname'] = "Mage_{$service['module']}_Service_{$service['name']}_{$service['version']}";
            return $service;
        }

        throw new InvalidArgumentException(sprintf('Service "%s:%s" was not found in registry.', $serviceName, $serviceVersion));
    }

    /**
     * Add a new method into an existing Service
     *
     * @param string methodName name of the method to add
     * @param string serviceName name of the service containing the method
     * @param string serviceVersion version of the service containing the method
     * @param array permissions list of permissions needed to execute this method
     * @param string inputSchema location of the XSD file describing the input needed for this method
     * @param string inputElement name of the XML element in the XSD representing the root of the data input structure
     * @param string outputSchema location of the XSD file describing the output from this method
     * @param string outputElement name of the XML element in the XSD representing the root of the data output structure
     * @throw InvalidArgumentException if the service does not exist
     */
    public function addMethod ($methodName, $serviceName, $serviceVersion, $permissions)
    {
        try {
            $methodMetadata = $this->getMethod($methodName, $serviceName, $serviceVersion);
        }
        catch (InvalidArgumentException $e) {
            $this->_services[$serviceName][$serviceVersion]['methods'][$methodName] = array(
                'name' => $methodName,
                'permissions' => $permissions,
            );
        }
    }

    /**
     * Add additional properties to an existing method
     *
     * @param string methodName name of the method to add
     * @param string serviceName name of the service containing the method
     * @param string serviceVersion version of the service containing the method
     * @param array properties additional properties
     * @throws InvalidArgumentException if the service or method do not exist
     */
    public function addMethodProperties ($methodName, $serviceName, $serviceVersion, $properties)
    {
        $methodMetadata = $this->getMethod($methodName, $serviceName, $serviceVersion);
        $this->_services[$serviceName][$serviceVersion]['methods'][$methodName] = array_merge($methodMetadata, $properties);
    }

    /**
     * Lookup metadata of a given method
     *
     * @param string methodName name of the method to lookup
     * @param string serviceName name of the service the method is part of
     * @param string serviceVersion
     * @throws InvalidArgumentException if the service or method do not exist
     */
    public function getMethod ($methodName, $serviceName, $serviceVersion)
    {
        if (isset($this->_services[$serviceName])
            && isset($this->_services[$serviceName][$serviceVersion])
            && isset($this->_services[$serviceName][$serviceVersion]['methods'][$methodName])) {
            $method = $this->_services[$serviceName][$serviceVersion]['methods'][$methodName];
            $method['schema'] = "Mage/{$this->_services[$serviceName][$serviceVersion]['module']}/etc/{$serviceVersion}-{$serviceName}.xsd";
            $method['request_element'] = $method['name'] + 'Request';
            $method['response_element'] = $method['name'] + 'Response';
        }

        throw new InvalidArgumentException(sprintf('Method "%s" of Service "%s:%s" was not found in registry.', $methodName, $serviceName, $serviceVersion));
    }

    public function saveToCache (Mage_Core_Model_CacheInterface $cache)
    {
        if ($cache->canUse(self::WEBSERVICE_CACHE_NAME)) {
            $cache->save(
                serialize($this->_services),
                self::CONFIG_CACHE_ID,
                array(self::WEBSERVICE_CACHE_TAG)
            );
        }
    }

    private function loadFromCache (Mage_Core_Model_CacheInterface $cache)
    {
        if ($cache->canUse(Mage_Core_Service_Config::WEBSERVICE_CACHE_NAME)) {
            $cachedData = $cache->load(self::CONFIG_CACHE_ID);
            if ($cachedData !== false) {
                $this->_services = unserialize($cachedData);
            }
        }
    }
}
