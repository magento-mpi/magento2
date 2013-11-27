<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

use \Magento\Webapi\Model\Config\Converter;

/**
 * Webapi Config Model for Soap.
 */
class Config
{
    /**#@+
     * Keys that a used for service config internal representation.
     */
    const KEY_CLASS = 'class';
    const KEY_IS_SECURE = 'isSecure';
    const KEY_METHOD = 'method';
    const KEY_IS_REQUIRED = 'inputRequired';
    const KEY_ACL_RESOURCES = 'resources';
    /**#@-*/

    /** @var \Magento\Filesystem */
    protected $_filesystem;

    /** @var \Magento\App\Dir */
    protected $_dir;

    /** @var \Magento\Webapi\Model\Config */
    protected $_config;

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /**
     * SOAP services should be stored separately as the list of available operations
     * is collected using reflection, not taken from config as for REST
     *
     * @var array
     */
    protected $_soapServices;

    /**
     * List of SOAP operations available in the system
     *
     * @var array
     */
    protected $_soapOperations;

    /**
     * Service methods metadata collected via reflection.
     *
     * @var array
     */
    protected $_serviceMetadata;

    /** @var \Magento\Webapi\Helper\Config */
    protected $_configHelper;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\App\Dir $dir
     * @param \Magento\Webapi\Model\Config $config
     * @param \Magento\Webapi\Model\Soap\Config\Reader\Soap $reader
     * @param \Magento\Webapi\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Filesystem $filesystem,
        \Magento\App\Dir $dir,
        \Magento\Webapi\Model\Config $config,
        \Magento\Webapi\Model\Soap\Config\Reader\Soap $reader,
        \Magento\Webapi\Helper\Config $configHelper
    ) {
        $this->_filesystem = $filesystem;
        $this->_dir = $dir;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_configHelper = $configHelper;
        $this->_serviceMetadata = $reader->getData($this->_getSoapServices());
    }

    /**
     * Retrieve the list of SOAP operations available in the system
     *
     * @param array $requestedService The list of requested services with their versions
     * @return array <pre>
     * array(
     *     array(
     *         'class' => $serviceClass,
     *         'method' => $serviceMethod
     *         'isSecure' => $isSecure
     *     ),
     *      ...
     * )</pre>
     */
    protected function _getSoapOperations($requestedService)
    {
        if (null == $this->_soapOperations) {
            $this->_soapOperations = array();
            foreach ($this->getRequestedSoapServices($requestedService) as $serviceData) {
                foreach ($serviceData[Converter::KEY_SERVICE_METHODS] as $methodData) {
                    $method = $methodData[Converter::KEY_SERVICE_METHOD];
                    $class = $serviceData[Converter::KEY_SERVICE_CLASS];
                    $operationName = $this->getSoapOperation($class, $method);
                    $this->_soapOperations[$operationName] = array(
                        self::KEY_CLASS => $class,
                        self::KEY_METHOD => $method,
                        self::KEY_IS_SECURE => $methodData[Converter::KEY_IS_SECURE],
                        self::KEY_ACL_RESOURCES => $methodData[Converter::KEY_ACL_RESOURCES]
                    );
                }
            }
        }
        return $this->_soapOperations;
    }

    /**
     * Collect the list of services with their operations available in SOAP.
     *
     * @return array
     */
    protected function _getSoapServices()
    {
        // TODO: Implement caching if this approach is approved
        if (is_null($this->_soapServices)) {
            $this->_soapServices = array();
            foreach ($this->_config->getServices() as $serviceData) {
                $serviceClass = $serviceData[Converter::KEY_SERVICE_CLASS];
                foreach ($serviceData[Converter::KEY_SERVICE_METHODS] as $methodMetadata) {
                    // TODO: Simplify the structure in SOAP. Currently it is unified in SOAP and REST
                    $methodName = $methodMetadata[Converter::KEY_SERVICE_METHOD];
                    $this->_soapServices[$serviceClass]['methods'][$methodName] = array(
                        self::KEY_METHOD => $methodName,
                        self::KEY_IS_REQUIRED => (bool)$methodMetadata[Converter::KEY_IS_SECURE],
                        self::KEY_IS_SECURE => $methodMetadata[Converter::KEY_IS_SECURE],
                        self::KEY_ACL_RESOURCES => $methodMetadata[Converter::KEY_ACL_RESOURCES]
                    );
                    $this->_soapServices[$serviceClass][self::KEY_CLASS] = $serviceClass;
                };
            };
        }
        return $this->_soapServices;
    }

    /**
     * Retrieve service method information, including service class, method name, and isSecure attribute value.
     *
     * @param string $soapOperation
     * @param array $requestedServices The list of requested services with their versions
     * @return array
     * @throws \Magento\Webapi\Exception
     */
    public function getServiceMethodInfo($soapOperation, $requestedServices)
    {
        $soapOperations = $this->_getSoapOperations($requestedServices);
        if (!isset($soapOperations[$soapOperation])) {
            throw new \Magento\Webapi\Exception(
                __('Operation "%1" not found.', $soapOperation),
                0,
                \Magento\Webapi\Exception::HTTP_NOT_FOUND
            );
        }
        return array(
            self::KEY_CLASS => $soapOperations[$soapOperation][self::KEY_CLASS],
            self::KEY_METHOD => $soapOperations[$soapOperation][self::KEY_METHOD],
            self::KEY_IS_SECURE => $soapOperations[$soapOperation][self::KEY_IS_SECURE],
            self::KEY_ACL_RESOURCES => $soapOperations[$soapOperation][self::KEY_ACL_RESOURCES]
        );
    }

    /**
     * Retrieve the list of services corresponding to specified services and their versions.
     *
     * @param array $requestedServices array('FooBarV1', 'OtherBazV2', ...)
     * @return array Filtered list of services
     */
    public function getRequestedSoapServices(array $requestedServices)
    {
        $services = array();
        foreach ($requestedServices as $serviceName) {
            foreach ($this->_getSoapServices() as $serviceData) {
                $serviceWithVersion = $this->_configHelper->getServiceName($serviceData[self::KEY_CLASS]);
                if ($serviceWithVersion === $serviceName) {
                    $services[] = $serviceData;
                }
            }
        }
        return $services;
    }

    /**
     * Generate SOAP operation name.
     *
     * @param string $interfaceName e.g. \Magento\Catalog\Service\ProductInterfaceV1
     * @param string $methodName e.g. create
     * @return string e.g. catalogProductCreate
     */
    public function getSoapOperation($interfaceName, $methodName)
    {
        $serviceName = $this->_configHelper->getServiceName($interfaceName);
        $operationName = $serviceName . ucfirst($methodName);
        return $operationName;
    }

    /**
     * Identify the list of service name parts including sub-services using class name.
     *
     * Examples of input/output pairs: <br/>
     * - 'Magento\Customer\Service\Customer\AddressV1Interface' => array('Customer', 'Address', 'V1') <br/>
     * - 'Vendor\Customer\Service\Customer\AddressV1Interface' => array('VendorCustomer', 'Address', 'V1) <br/>
     * - 'Magento\Catalog\Service\ProductV2Interface' => array('CatalogProduct', 'V2')
     *
     * @param string $className
     * @param bool $preserveVersion Should version be preserved during class name conversion into service name
     * @return array
     * @throws \InvalidArgumentException When class is not valid API service.
     */
    public function getServiceNameParts($className, $preserveVersion = false)
    {
        if (preg_match(\Magento\Webapi\Model\Config::SERVICE_CLASS_PATTERN, $className, $matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Magento') ? '' : $moduleNamespace;
            $serviceNameParts = explode('\\', trim($matches[3], '\\'));
            if ($moduleName == $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceVersion = $matches[4];
                $serviceNameParts[] = $serviceVersion;
            }
            return $serviceNameParts;
        }
        throw new \InvalidArgumentException(sprintf('The service interface name "%s" is invalid.', $className));
    }

    /**
     * Retrieve data type details for the given type name.
     * TODO: modify metadata to contain request input and output param
     *
     * @param string $typeName
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getTypeData($typeName)
    {
        if (!isset($this->_serviceMetadata['types'][$typeName])) {
            throw new \InvalidArgumentException(sprintf('Data type "%s" was not found in config.', $typeName));
        }
        return $this->_serviceMetadata['types'][$typeName];
    }

    /**
     * Add or update type data in config.
     *
     * @param string $typeName
     * @param array $data
     */
    public function setTypeData($typeName, $data)
    {
        if (!isset($this->_serviceMetadata['types'][$typeName])) {
            $this->_serviceMetadata['types'][$typeName] = $data;
        } else {
            $this->_serviceMetadata['types'][$typeName] = array_merge_recursive(
                $this->_serviceMetadata['types'][$typeName],
                $data
            );
        }
    }

    /**
     * Retrieve mapping of complex types defined in WSDL to real data classes.
     *
     * @return array
     */
    public function getTypeToClassMap()
    {
        return !is_null($this->_serviceMetadata['type_to_class_map'])
            ? $this->_serviceMetadata['type_to_class_map']
            : array();
    }

    /**
     * Retrieve specific service interface data.
     *
     * @param string $serviceName
     * @return array
     * @throws \RuntimeException
     */
    public function getServiceMetadata($serviceName)
    {
        if (!isset($this->_serviceMetadata['services'][$serviceName])
            || !is_array($this->_serviceMetadata['services'][$serviceName])
        ) {
            throw new \RuntimeException(__('Requested service is not available: "%1"', $serviceName));
        }
        return $this->_serviceMetadata['services'][$serviceName];
    }
}
