<?php

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
    protected $_services = null;

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
        if (null === $this->_services) {
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
     * @param string $data
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

        $this->_readAttributes($root, $result);

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

                if ('service' === $child->nodeName) {
                    if (!isset($result['services'])) {
                        $result['services'] = array();
                    }
                    $nodeID = $_children['id'] . (isset($_children['version']) ? ('-version-' . $_children['version']) : '');
                    if (!isset($result['services'][$nodeID])) {
                        $result['services'][$nodeID] = $_children;
                    } else {
                        $result['services'][$nodeID] = array_merge($result['calls'][$nodeID], $_children);
                    }
                } elseif ('call' === $child->nodeName) {
                    if (!isset($result['calls'])) {
                        $result['calls'] = array();
                    }
                    $nodeID = $_children['method'];
                    if (!isset($result['calls'][$nodeID])) {
                        $result['calls'][$nodeID] = $_children;
                    } else {
                        $result['calls'][$nodeID] = array_merge($result['calls'][$nodeID], $_children);
                    }
                } else {
                    if (!isset($result[$child->nodeName])) {
                        $result[$child->nodeName] = $_children;
                    } else {
                        if (!isset($group[$child->nodeName])) {
                            $tmp = $result[$child->nodeName];
                            $result[$child->nodeName] = array($tmp);
                            $group[$child->nodeName] = 1;
                        }

                        $result[$child->nodeName][] = $_children;
                    }
                }

            }
        }

        unset($result['#text']);

        return $result;
    }

    /**
     * @param $node
     * @param array $result
     */
    protected function _readAttributes($node, & $result)
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $result[$attr->name] = $attr->value;
            }
        }
    }

    /**
     * @param string $serviceReferenceId
     * @param mixed $serviceMethod [optional]
     * @param mixed $version [optional]
     * @return string
     */
    public function getServiceClassByServiceName($serviceReferenceId, $serviceMethod = null, $version = null)
    {
        if (null === $version) {
            $version = (string) $this->getServiceVersionBind($serviceReferenceId, $serviceMethod);
        }

        if (!empty($version)) {
            $versionedClassName = $this->getServices()->getData($serviceReferenceId . '-version-' . $version . '/calls/' . $serviceMethod . '/class');
            if (!empty($versionedClassName) && class_exists($versionedClassName)) {
                return $versionedClassName;
            }
            $versionedClassName = $this->getServices()->getData($serviceReferenceId . '-version-' . $version . '/class');
            if (!empty($versionedClassName) && class_exists($versionedClassName)) {
                return $versionedClassName;
            }
        }

        $className = $this->getServices()->getData($serviceReferenceId . '/calls/' . $serviceMethod . '/class');
        if (!empty($className) && class_exists($className)) {
            return $className;
        }

        $className = $this->getServices()->getData($serviceReferenceId . '/class');
        if (!empty($className) && class_exists($className)) {
            return $className;
        }

        if (class_exists($serviceReferenceId)) {
            return $serviceReferenceId;
        }

        throw new Mage_Core_Service_Exception(
            Mage::helper('Mage_Core_Helper_Data')->__('Service %s does not exist!', $serviceReferenceId)
        );
    }

    /**
     * @param string $serviceReferenceId
     * @param string $serviceMethod
     * @return string
     */
    public function getServiceVersionBind($serviceReferenceId, $serviceMethod = null)
    {
        // loookup for default service version
        return '1';
    }
}
