<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api config model
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Config extends Varien_Simplexml_Config
{
    const CACHE_TAG         = 'config_api';

    /**
     * Constructor
     *
     * @see Varien_Simplexml_Config
     */
    public function __construct($sourceData=null)
    {
        $this->setCacheId('config_api');
        $this->setCacheTags(array(self::CACHE_TAG));

        parent::__construct($sourceData);
        $this->_construct();
    }

    /**
     * Init configuration for webservices api
     *
     * @return Mage_Api_Model_Config
     */
    protected function _construct()
    {
        if (Mage::app()->useCache('config_api')) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $mergeConfig = Mage::getModel('core/config_base');

        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        $configFile = $config->getModuleDir('etc', 'Mage_Api').DS.'api.xml';


        if ($mergeConfig->loadFile($configFile)) {
            $config->extend($mergeConfig, true);
        }

        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if (($disableLocalModules && ('local' === (string)$module->codePool)) || $modName=='Mage_Api') {
                    continue;
                }

                $configFile = $config->getModuleDir('etc', $modName).DS.'api.xml';

                if ($mergeConfig->loadFile($configFile)) {
                    $config->extend($mergeConfig, true);
                }
            }
        }

        $this->setXml($config->getNode('api'));

        if (Mage::app()->useCache('config_api')) {
            $this->saveCache();
        }
        return $this;
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
            /* @var $adapter Varien_SimpleXml_Element */
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
     * @return Varien_Simplexml_Element
     */
    public function getHandlers()
    {
        return $this->getNode('handlers')->children();
    }

    /**
     * Retrieve resources
     *
     * @return Varien_Simplexml_Element
     */
    public function getResources()
    {
        return $this->getNode('resources')->children();
    }

    /**
     * Retrieve resources alias
     *
     * @return Varien_Simplexml_Element
     */
    public function getResourcesAlias()
    {
        return $this->getNode('resources_alias')->children();
    }


    /**
     * Load Acl resources from config
     *
     * @param Mage_Api_Model_Acl $acl
     * @param Mage_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return Mage_Api_Model_Config
     */
    public function loadAclResources(Mage_Api_Model_Acl $acl, $resource=null, $parentName=null)
    {
        if (is_null($resource)) {
            $resource = $this->getNode('acl/resources');
            $resourceName = null;
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
            $acl->add(Mage::getModel('api/acl_resource', $resourceName), $parentName);
        }

        if (is_null($resourceName)) {
            $children = $resource->children();
        } elseif (isset($resource->children)){
            $children = $resource->children->children();
        }


        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            $this->loadAclResources($acl, $res, $resourceName);
        }
        return $this;
    }

    /**
     * Get acl assert config
     *
     * @param string $name
     * @return Mage_Core_Model_Config_Element|boolean
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
     * @return Mage_Core_Model_Config_Element|boolean
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
        /* @var $faultsNode Varien_Simplexml_Element */

        $translateModule = 'api';
        if (isset($faultsNode['module'])) {
           $translateModule = (string) $faultsNode['module'];
        }

        $faults = array();
        foreach ($faultsNode->children() as $faultName => $fault) {
            $faults[$faultName] = array(
                'code'    => (string) $fault->code,
                'message' => Mage::helper($translateModule)->__((string)$fault->message)
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
} // Class Mage_Api_Model_Config End