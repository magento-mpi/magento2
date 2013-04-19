<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Web API configuration.
 *
 * This class is responsible for collecting web API configuration using reflection
 * as well as for implementing interface to provide access to collected configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Config
{
    /**#@+
     * Cache parameters.
     */
    const WEBSERVICE_CACHE_NAME = Mage_Webapi_Model_Cache_Type::TYPE_IDENTIFIER;
    const WEBSERVICE_CACHE_TAG = Mage_Webapi_Model_Cache_Type::CACHE_TAG;
    /**#@-*/

    /**#@+
     * Version parameters.
     */
    const VERSION_NUMBER_PREFIX = 'V';
    /**#@-*/

    /** @var Mage_Core_Service_Config_Reader */
    protected $_reader;

    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /**
     * Resources configuration data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Initialize dependencies. Initialize data.
     *
     * @param Mage_Core_Service_Config_Reader $reader
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Model_App $application
     */
    public function __construct(
        Mage_Core_Service_Config_Reader $reader,
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Model_App $application
    ) {
        $this->_reader = $reader;
        $this->_helper = $helper;
        $this->_application = $application;
        $this->_data = $this->_reader->getData();
    }

    /**
     * Retrieve all data about the services registered in the system.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Retrieve data type details for the given type name.
     *
     * @param string $typeName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getTypeData($typeName)
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
     * Identify controller class by operation name.
     *
     * @param string $serviceName
     * @return string
     * @throws LogicException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getServiceClassByServiceName($serviceName)
    {
        if (isset($this->_data['resources'][$serviceName]['controller'])) {
            return $this->_data['resources'][$serviceName]['controller'];
        }
        throw new LogicException(sprintf('Resource "%s" must have associated controller class.', $serviceName));
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
        $serviceName = $this->_helper->translateServiceName($methodReflection->getDeclaringClass()->getName());
        $methodName = $methodReflection->getName();

        if (!isset($this->_data['resources'][$serviceName]['methods'][$methodName])) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" method is not registered in "%s" resource.',
                $methodName,
                $serviceName
            ));
        }
        return $this->_data['resources'][$serviceName]['methods'][$methodName];
    }

    /**
     * Retrieve mapping of complex types defined in WSDL to real data classes.
     *
     * @return array
     */
    public function getTypeToClassMap()
    {
        return !is_null($this->_data['type_to_class_map']) ? $this->_data['type_to_class_map'] : array();
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
     * )
     * </pre>
     *
     * @param string $serviceName
     * @param string $method
     * @return array|bool On success array with policy details; false otherwise.
     * @throws InvalidArgumentException
     */
    public function getDeprecationPolicy($serviceName, $method)
    {
        $deprecationPolicy = false;
        $resourceData = $this->getServiceData($serviceName);
        if (!isset($resourceData['methods'][$method])) {
            throw new InvalidArgumentException(sprintf(
                'Method "%s" does not exist in resource "%s".',
                $method,
                $serviceName
            ));
        }
        $methodData = $resourceData['methods'][$method];
        if (isset($methodData['deprecation_policy']) && is_array($methodData['deprecation_policy'])) {
            $deprecationPolicy = $methodData['deprecation_policy'];
        }
        return $deprecationPolicy;
    }

    /**
     * Check if specified method is deprecated or removed.
     *
     * Throw exception in two cases:<br/>
     * - method is removed<br/>
     * - method is deprecated and developer mode is enabled
     *
     * @param string $serviceName
     * @param string $method
     * @throws Mage_Webapi_Exception
     * @throws LogicException
     */
    public function checkDeprecationPolicy($serviceName, $method)
    {
        $deprecationPolicy = $this->getDeprecationPolicy($serviceName, $method);
        if ($deprecationPolicy) {
            /** Initialize message with information about what method should be used instead of requested one. */
            if (isset($deprecationPolicy['use_resource']) && isset($deprecationPolicy['use_method'])) {
                $messageUseMethod = $this->_helper
                    ->__('Please use "%s" method in "%s" resource instead.',
                    $deprecationPolicy['use_method'],
                    $deprecationPolicy['use_resource']
                );
            } else {
                $messageUseMethod = '';
            }

            $badRequestCode = Mage_Webapi_Exception::HTTP_BAD_REQUEST;
            if (isset($deprecationPolicy['removed'])) {
                $removalMessage = $this->_helper
                    ->__('"%s" method in "%s" resource was removed.',
                    $method,
                    $serviceName
                );
                throw new Mage_Webapi_Exception($removalMessage . ' ' . $messageUseMethod, $badRequestCode);
            } elseif (isset($deprecationPolicy['deprecated']) && $this->_application->isDeveloperMode()) {
                $deprecationMessage = $this->_helper
                    ->__('"%s" method in "%s" resource is deprecated.',
                    $method,
                    $serviceName
                );
                throw new Mage_Webapi_Exception($deprecationMessage . ' ' . $messageUseMethod, $badRequestCode);
            }
        }
    }

    /**
     * Retrieve the list of all resource names.
     *
     * @return array
     */
    public function getResourcesNames()
    {
        return array_keys($this->_data['resources']);
    }

    /**
     * Retrieve resource data.
     *
     * @param string $serviceName
     * @return array
     * @throws LogicException In case when resource with specified name is not defined or its data is not an array
     */
    public function getServiceData($serviceName)
    {
        if (!isset($this->_data['resources'][$serviceName]) || !is_array($this->_data['resources'][$serviceName])) {
            throw new LogicException(sprintf('Resource "%s" is not defined or is invalid.', $serviceName));
        }
        return $this->_data['resources'][$serviceName];
    }

    /**
     * Add a new service (and/or method into the registry)
     *
     * @param string $module eg, Mage_Catalog
     * @param string $serviceId Service ID containing the method
     * @param string $serviceClass Service class containing the method
     * @param string $serviceVersion version of the service containing the method
     * @param string $methodName name of the method to add
     * @param array  $permissions list of permissions needed to execute this method
     * @param string inputSchema location of the XSD file describing the input needed for this method
     * @param string inputElement name of the XML element in the XSD representing the root of the data input structure
     * @param string outputSchema location of the XSD file describing the output from this method
     * @param string outputElement name of the XML element in the XSD representing the root of the data output structure
     * @throw InvalidArgumentException if the service does not exist
     */
    public function addService($module, $serviceId, $serviceClass, $serviceVersion, $methodName, $permissions)
    {

    }
}
