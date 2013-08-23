<?php
/**
 * Webapi Config Model for Soap.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_Config extends Mage_Webapi_Model_Config
{
    /** @var Magento_Filesystem */
    protected $_filesystem;

    /** @var Mage_Core_Model_Dir */
    protected $_dir;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

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
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dir
     * @param Mage_Webapi_Helper_Data $helper
     */

    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dir,
        Mage_Webapi_Helper_Data $helper
    ) {
        parent::__construct($config, $configCacheType, $moduleReader);
        $this->_filesystem = $filesystem;
        $this->_dir = $dir;
        $this->_helper = $helper;
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
                foreach ($serviceData[self::KEY_OPERATIONS] as $method => $methodData) {
                    $operationName = $this->_helper->getSoapOperation($serviceData['class'], $method);
                    $this->_soapOperations[$operationName] = array(
                        'class' => $serviceData['class'],
                        'method' => $method,
                        self::SECURE_ATTR_NAME => $methodData[self::SECURE_ATTR_NAME]
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
    public function getSoapServices()
    {
        // TODO: Implement caching if this approach is approved
        if (is_null($this->_soapServices)) {
            $this->_soapServices = array();
            foreach ($this->getRestServices() as $serviceData) {
                $reflection = new ReflectionClass($serviceData['class']);
                foreach ($reflection->getMethods() as $method) {
                    // find if method is secure, look into rest operation definition of each operation
                    // if operation is not defined, assume operation is not secure
                    $isOperationSecure = false;
                    if (isset($serviceData[self::KEY_OPERATIONS][$method->getName()][self::SECURE_ATTR_NAME])) {
                        $secureFlagValue = $serviceData[self::KEY_OPERATIONS]
                        [$method->getName()][self::SECURE_ATTR_NAME];
                        $isOperationSecure = (strtolower($secureFlagValue) === 'true');
                    }

                    // TODO: Simplify the structure in SOAP. Currently it is unified in SOAP and REST
                    $this->_soapServices[$serviceData['class']]['operations'][$method->getName()] = array(
                        'method' => $method->getName(),
                        'inputRequired' => (bool)$method->getNumberOfParameters(),
                        self::SECURE_ATTR_NAME => $isOperationSecure
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
        return $soapOperations[$soapOperation][self::SECURE_ATTR_NAME];
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
            foreach ($this->getSoapServices() as $serviceData) {
                $serviceWithVersion = $this->_helper->getServiceName($serviceData['class']);
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
        // TODO: Use object manager instead of direct DOMDocument instantiation
        $serviceSchema = new DOMDocument();
        $serviceSchema->loadXML($schema);

        return $serviceSchema;
    }
}