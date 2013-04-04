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
    private static $registry;
    private $services = array();

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct ()
    {
    }

    /**
     * Implements a singleton pattern
     *
     * @return Mage_Core_Service_Registry
     */
    public static function getInstance ()
    {
        if (! self::$registry) {
            self::$registry = new Mage_Core_Service_Registry();
        }
        return self::$registry;
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
     * @return Mage_Core_Service_Registry_ServiceMetadata
     */
    public function addService ($serviceName, $serviceVersion, $className)
    {
        $key = $serviceName . ':' . $serviceVersion;
        try {
            $serviceMetadata = $this->getService($serviceName, $serviceVersion);
        }
        catch (InvalidArgumentException $e) {
            $serviceMetadata = new Mage_Core_Service_Registry_ServiceMetadata($serviceName, $serviceVersion, $className);
            $this->services[$key] = $serviceMetadata;
        }

        return $serviceMetadata;
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
        $key = $serviceName . ':' . $serviceVersion;
        if (isset($this->services[$key])) {
            return $this->services[$key];
        }

        throw new InvalidArgumentException(sprintf('Service "%s" was not found in registry.', $key));
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
     * @return Mage_Core_Service_Registry_MethodMetadata
     * @throw InvalidArgumentException if the service does not exist
     */
    public function addMethod ($methodName, $serviceName, $serviceVersion, $permissions, $inputSchema, $inputElement, $outputSchema, $outputElement)
    {
        $serviceMetadata = $this->getService($serviceName, $serviceVersion);
        return $serviceMetadata->addMethod($methodName, $permissions, $inputSchema, $inputElement, $outputSchema, $outputElement);
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
        $serviceMetadata = $this->getService($serviceName, $serviceVersion);
        return $serviceMetadata->getMethod($methodName);
    }

}
