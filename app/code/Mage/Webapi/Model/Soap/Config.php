<?php
/**
 * Webapi Config Model for Soap.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_Config
{
    /** @var Magento_Filesystem */
    protected $_filesystem;

    /** @var Mage_Core_Model_Dir */
    protected $_dir;

    /** @var Mage_Webapi_Model_Config */
    protected $_config;

    /** @var Mage_Core_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_ObjectManager */
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
     * @param Mage_Core_Model_ObjectManager $objectManager
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dir
     * @param Mage_Webapi_Model_Config $config
     * @param Mage_Core_Helper_Data $helper
     */
    public function __construct(
        Mage_Core_Model_ObjectManager $objectManager,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dir,
        Mage_Webapi_Model_Config $config,
        Mage_Core_Helper_Data $helper
    ) {
        $this->_filesystem = $filesystem;
        $this->_dir = $dir;
        $this->_config = $config;
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
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
     *     ),
     *      ...
     * )</pre>
     */
    protected function _getSoapOperations($requestedService)
    {
        if (null == $this->_soapOperations) {
            $this->_soapOperations = array();
            foreach ($this->getRequestedSoapServices($requestedService) as $serviceData) {
                foreach ($serviceData[Mage_Webapi_Model_Config::KEY_OPERATIONS] as $method => $methodData) {
                    $operationName = $this->getSoapOperation($serviceData['class'], $method);
                    $this->_soapOperations[$operationName] = array(
                        'class' => $serviceData['class'],
                        'method' => $method,
                        Mage_Webapi_Model_Config::SECURE_ATTR_NAME
                            => $methodData[Mage_Webapi_Model_Config::SECURE_ATTR_NAME]
                    );
                }
            }
        }
        return $this->_soapOperations;
    }

    /**
     * Collect the list of services with their operations available in SOAP.
     * The list of services is taken from webapi.xml configuration files.
     * The list of methods in contrast to REST is taken from PHP Interface using reflection.
     *
     * @return array
     */
    protected function _getSoapServices()
    {
        // TODO: Implement caching if this approach is approved
        if (is_null($this->_soapServices)) {
            $this->_soapServices = array();
            foreach ($this->_config->getServices() as $serviceData) {
                $reflection = new ReflectionClass($serviceData['class']);
                foreach ($reflection->getMethods() as $method) {
                    // find if method is secure, look into rest operation definition of each operation
                    // if operation is not defined, assume operation is not secure
                    $isOperationSecure = false;
                    if (isset($serviceData[Mage_Webapi_Model_Config::KEY_OPERATIONS][$method->getName()]
                    [Mage_Webapi_Model_Config::SECURE_ATTR_NAME])) {
                        $secureFlagValue = $serviceData[Mage_Webapi_Model_Config::KEY_OPERATIONS]
                        [$method->getName()][Mage_Webapi_Model_Config::SECURE_ATTR_NAME];
                        $isOperationSecure = (strtolower($secureFlagValue) === 'true');
                    }

                    // TODO: Simplify the structure in SOAP. Currently it is unified in SOAP and REST
                    $this->_soapServices[$serviceData['class']]['operations'][$method->getName()] = array(
                        'method' => $method->getName(),
                        'inputRequired' => (bool)$method->getNumberOfParameters(),
                        Mage_Webapi_Model_Config::SECURE_ATTR_NAME => $isOperationSecure
                    );
                    $this->_soapServices[$serviceData['class']]['class'] = $serviceData['class'];
                };
            };
        }
        return $this->_soapServices;
    }

    /**
     * Retrieve service class name corresponding to provided SOAP operation name.
     *
     * @param string $soapOperation
     * @param array $requestedServices The list of requested services with their versions
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getClassBySoapOperation($soapOperation, $requestedServices)
    {
        $soapOperations = $this->_getSoapOperations($requestedServices);
        if (!isset($soapOperations[$soapOperation])) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Operation "%s" not found.', $soapOperation),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $soapOperations[$soapOperation]['class'];
    }

    /**
     * Retrieve service method name corresponding to provided SOAP operation name.
     *
     * @param string $soapOperation
     * @param array $requestedServices The list of requested services with their versions
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function getMethodBySoapOperation($soapOperation, $requestedServices)
    {
        $soapOperations = $this->_getSoapOperations($requestedServices);
        if (!isset($soapOperations[$soapOperation])) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Operation "%s" not found.', $soapOperation),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $soapOperations[$soapOperation]['method'];
    }

    /**
     * Returns true if SOAP operation is defined as secure
     *
     * @param string $soapOperation
     * @param array $requestedServices The list of requested services with their versions
     * @return bool
     * @throws Mage_Webapi_Exception
     */
    public function isSoapOperationSecure($soapOperation, $requestedServices)
    {
        $soapOperations = $this->_getSoapOperations($requestedServices);
        if (!isset($soapOperations[$soapOperation])) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Operation "%s" not found.', $soapOperation),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $soapOperations[$soapOperation][Mage_Webapi_Model_Config::SECURE_ATTR_NAME];
    }

    /**
     * Retrieve the list of services corresponding to specified services and their versions.
     *
     * @param array $requestedServices <pre>
     * array(
     *     'catalogProduct' => 'V1'
     *     'customer' => 'V2
     * )<pre/>
     * @return array Filtered list of services
     */
    public function getRequestedSoapServices($requestedServices)
    {
        $services = array();
        foreach ($requestedServices as $serviceName) {
            foreach ($this->_getSoapServices() as $serviceData) {
                $serviceWithVersion = $this->getServiceName($serviceData['class']);
                if ($serviceWithVersion === $serviceName) {
                    $services[] = $serviceData;
                }
            }
        }
        return $services;
    }

    /**
     * Load and return Service XSD for the provided Service Class
     *
     * @param $serviceClass
     * @return DOMDocument
     */
    public function getServiceSchemaDOM($serviceClass)
    {
         // TODO: Check if Service specific XSD is already cached
        $modulesDir = $this->_dir->getDir(Mage_Core_Model_Dir::MODULES);

        // TODO: Change pattern to match interface instead of class. Think about sub-services.
        if (!preg_match(Mage_Webapi_Model_Config::SERVICE_CLASS_PATTERN, $serviceClass, $matches)) {
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

        // TODO: Should happen only once the cache is in place
        $serviceSchema = $this->_objectManager->create('DOMDocument');
        $serviceSchema->loadXML($schema);

        return $serviceSchema;
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
        $serviceName = $this->getServiceName($interfaceName);
        $operationName = $serviceName . ucfirst($methodName);
        return $operationName;
    }

    /**
     * Translate service interface name into service name.
     * Example:
     * <pre>
     * - Mage_Customer_Service_CustomerV1Interface         => customer          // $preserveVersion == false
     * - Mage_Customer_Service_Customer_AddressV1Interface => customerAddressV1 // $preserveVersion == true
     * - Mage_Catalog_Service_ProductV2Interface           => catalogProductV2  // $preserveVersion == true
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
     * Identify the list of service name parts including sub-services using class name.
     *
     * Examples of input/output pairs: <br/>
     * - 'Mage_Customer_Service_Customer_AddressV1Interface' => array('Customer', 'Address', 'V1') <br/>
     * - 'Vendor_Customer_Service_Customer_AddressV1Interface' => array('VendorCustomer', 'Address', 'V1) <br/>
     * - 'Mage_Catalog_Service_ProductV2Interface' => array('CatalogProduct', 'V2')
     *
     * @param string $className
     * @param bool $preserveVersion Should version be preserved during class name conversion into service name
     * @return array
     * @throws InvalidArgumentException When class is not valid API service.
     */
    public function getServiceNameParts($className, $preserveVersion = false)
    {
        if (preg_match(Mage_Webapi_Model_Config::SERVICE_CLASS_PATTERN, $className, $matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Mage') ? '' : $moduleNamespace;
            $serviceNameParts = explode('_', trim($matches[3], '_'));
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
        throw new InvalidArgumentException(sprintf('The service interface name "%s" is invalid.', $className));
    }
}