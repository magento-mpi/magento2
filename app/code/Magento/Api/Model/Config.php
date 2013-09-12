<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Webservice api config model
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Config extends Magento_Simplexml_Config
{
    /**
     * Constructor
     *
     * @see Magento_Simplexml_Config
     */
    public function __construct($sourceData = null)
    {
        $this->setCacheId('config_api');
        $this->setCacheTags(array(Magento_Api_Model_Cache_Type::CACHE_TAG));

        parent::__construct($sourceData);
        $this->_construct();
    }

    /**
     * Init configuration for webservices api
     *
     * @return Magento_Api_Model_Config
     */
    protected function _construct()
    {
        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Mage::getObjectManager()->get('Magento_Core_Model_Cache_StateInterface');

        if ($cacheState->isEnabled(Magento_Api_Model_Cache_Type::TYPE_IDENTIFIER)) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $config = Mage::getSingleton('Magento_Core_Model_Config_Modules_Reader')->loadModulesConfiguration('api.xml');
        $this->setXml($config->getNode('api'));

        if ($cacheState->isEnabled(Magento_Api_Model_Cache_Type::TYPE_IDENTIFIER)) {
            $this->saveCache();
        }
        return $this;
    }

    /**
     * Retrieve adapter aliases from config.
     *
     * @return array
     */
    public function getAdapterAliases()
    {
        $aliases = array();

        foreach ($this->getNode('adapter_aliases')->children() as $alias => $adapter) {
            $aliases[$alias] = array(
                (string) $adapter->suggest_class, // model class name
                (string) $adapter->suggest_method // model method name
            );
        }
        return $aliases;
    }

    /**
     * Retrieve all adapters
     *
     * @return array
     */
    public function getAdapters()
    {
        $adapters = array();
        foreach ($this->getNode('adapters')->children() as $adapterName => $adapter) {
            /* @var $adapter Magento_Simplexml_Element */
            if (isset($adapter->use)) {
                $adapter = $this->getNode('adapters/' . (string) $adapter->use);
            }
            $adapters[$adapterName] = $adapter;
        }
        return $adapters;
    }

    /**
     * Retrieve active adapters
     *
     * @return array
     */
    public function getActiveAdapters()
    {
        $adapters = array();
        foreach ($this->getAdapters() as $adapterName => $adapter) {
            if (!isset($adapter->active) || $adapter->active == '0') {
                continue;
            }

            if (isset($adapter->required) && isset($adapter->required->extensions)) {
                foreach ($adapter->required->extensions->children() as $extension=>$data) {
                    if (!extension_loaded($extension)) {
                        continue;
                    }
                }
            }

            $adapters[$adapterName] = $adapter;
        }

        return $adapters;
    }

    /**
     * Retrieve handlers
     *
     * @return Magento_Simplexml_Element
     */
    public function getHandlers()
    {
        return $this->getNode('handlers')->children();
    }

    /**
     * Retrieve resources
     *
     * @return Magento_Simplexml_Element
     */
    public function getResources()
    {
        return $this->getNode('resources')->children();
    }

    /**
     * Retrieve resources alias
     *
     * @return Magento_Simplexml_Element
     */
    public function getResourcesAlias()
    {
        return $this->getNode('resources_alias')->children();
    }


    /**
     * Load Acl resources from config
     *
     * @param Magento_Api_Model_Acl $acl
     * @param Magento_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return Magento_Api_Model_Config
     */
    public function loadAclResources(Magento_Api_Model_Acl $acl, $resource=null, $parentName=null)
    {
        $resourceName = null;
        if (is_null($resource)) {
            $resource = $this->getNode('acl/resources');
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
            $acl->addResource(
                Mage::getModel('Magento_Api_Model_Acl_Resource', array('resourceId' => $resourceName)),
                $parentName
            );
        }

        $children = $resource->children();

        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            if ($res->getName() != 'title' && $res->getName() != 'sort_order') {
                $this->loadAclResources($acl, $res, $resourceName);
            }
        }
        return $this;
    }

    /**
     * Get acl assert config
     *
     * @param string $name
     * @return Magento_Core_Model_Config_Element|boolean
     */
    public function getAclAssert($name='')
    {
        $asserts = $this->getNode('acl/asserts');
        if (''===$name) {
            return $asserts;
        }

        if (isset($asserts->$name)) {
            return $asserts->$name;
        }

        return false;
    }

    /**
     * Retrieve privilege set by name
     *
     * @param string $name
     * @return Magento_Core_Model_Config_Element|boolean
     */
    public function getAclPrivilegeSet($name='')
    {
        $sets = $this->getNode('acl/privilegeSets');
        if (''===$name) {
            return $sets;
        }

        if (isset($sets->$name)) {
            return $sets->$name;
        }

        return false;
    }

    public function getFaults($resourceName=null)
    {
        if (is_null($resourceName)
            || !isset($this->getResources()->$resourceName)
            || !isset($this->getResources()->$resourceName->faults)) {
            $faultsNode = $this->getNode('faults');
        } else {
            $faultsNode = $this->getResources()->$resourceName->faults;
        }
        /* @var $faultsNode Magento_Simplexml_Element */

        $faults = array();
        foreach ($faultsNode->children() as $faultName => $fault) {
            $faults[$faultName] = array(
                'code'    => (string) $fault->code,
                'message' => __((string)$fault->message)
            );
        }

        return $faults;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Frontend_File
     */
    public function getCache()
    {
        return Mage::app()->getCache();
    }

    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }

    protected function _saveCache($data, $id, $tags=array(), $lifetime=false)
    {
        return Mage::app()->saveCache($data, $id, $tags, $lifetime);
    }

    protected function _removeCache($id)
    {
        return Mage::app()->removeCache($id);
    }
} // Class Magento_Api_Model_Config End
