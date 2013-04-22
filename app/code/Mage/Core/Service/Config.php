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
    const VERSION_NUMBER_PREFIX = 'V';

    const CACHE_ID = 'services';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Mage_Core_Service_Config_Reader
     */
    protected $_reader;

    /**
     * Module configuration reader
     *
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Varien_Object
     */
    protected $_services;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Config_Modules_Reader $moduleReader
    ) {
        $this->_config = $config;
        $this->_configCacheType = $configCacheType;
        $this->_moduleReader = $moduleReader;
    }

    /**
     * Retrieve list of service files from each module
     *
     * @return array
     */
    protected function _getServiceFiles()
    {
        $files = $this->_moduleReader
            ->getModuleConfigurationFiles('service.xml');
        return (array) $files;
    }

    /**
     * Reader object initialization
     *
     * @return Mage_Core_Service_Config_Reader
     */
    protected function _getReader()
    {
        if (null === $this->_reader) {
            $serviceFiles = $this->_getServiceFiles();
            $this->_reader = $this->_config->getModelInstance('Mage_Core_Service_Config_Reader',
                array('configFiles' => $serviceFiles)
            );
        }
        return $this->_reader;
    }

    /**
     * Return services loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getServices()
    {
        $services = $this->_loadFromCache();
        if ($services && is_string($services)) {
            $data = unserialize($services);
            $_array = isset($data['config']['services']) ? $data['config']['services'] : array();
            $this->_services = new Varien_Object($_array);
        } else {
            $services = $this->_getReader()->getServices();

            $data = $this->_toArray($services);

            $this->_saveToCache(serialize($data));
            $_array = isset($data['config']['services']) ? $data['config']['services'] : array();
            $this->_services = new Varien_Object($_array);
        }

        return $this->_services;
    }

    /**
     * Load services from cache
     *
     * @return null|string
     */
    private function _loadFromCache()
    {
        return $this->_configCacheType->load(self::CACHE_ID);
    }

    /**
     * Save services into the cache
     *
     * @param $data
     * @return Mage_Core_Service_Config
     */
    private function _saveToCache($data)
    {
        $this->_configCacheType->save($data, self::CACHE_ID);
        return $this;
    }

    protected function _toArray($root)
    {
        $result = array();

        if ($root->hasAttributes()) {
            foreach ($root->attributes as $attr) {
                $result[$attr->name] = $attr->value;
            }
        }

        $children = $root->childNodes;

        if ($children->length == 1) {
            $child = $children->item(0);
            if ($child->nodeType == XML_TEXT_NODE) {
                $result['_value'] = $child->nodeValue;
                if (count($result) == 1) {
                    return $result['_value'];
                } else {
                    return $result;
                }
            }
        }

        $group = array();

        for ($i = 0; $i < $children->length; $i++) {
            $child = $children->item($i);

            if (!isset($result[$child->nodeName])) {
                $result[$child->nodeName] = $this->_toArray($child);
            } else {
                if (!isset($group[$child->nodeName])) {
                    $tmp = $result[$child->nodeName];
                    $result[$child->nodeName] = array($tmp);
                    $group[$child->nodeName] = 1;
                }

                $result[$child->nodeName][] = $this->_toArray($child);
            }
        }

        return $result;
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

    /**
     * @param string $serviceReferenceId
     * @return string
     */
    public function getServiceClassByServiceName($serviceReferenceId)
    {
        $result = $this->getServices()->getData($serviceReferenceId . '/class');
        if (empty($result)) {
            $result = $serviceReferenceId;
        }

        return $result;
    }
}
