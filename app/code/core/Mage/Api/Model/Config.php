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
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
    /**
     * Constructor
     *
     * @see Varien_Simplexml_Config
     */
    public function __construct($sourceData=null)
    {
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
        $mergeConfig = Mage::getModel('core/config_base');

        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }
                $configFile = $config->getModuleDir('etc', $modName).DS.'api.xml';

                if ($mergeConfig->loadFile($configFile)) {
                    $config->extend($mergeConfig, true);
                }
            }
        }

        $this->setXml($config->getNode('api'));
        return $this;
    }

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

    public function getHandlers()
    {
        return $this->getNode('handlers')->children();
    }

    public function getResources()
    {
        return $this->getNode('resources')->children();
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

        if (isset($resource->all)) {
            $acl->add(Mage::getModel('api/acl_resource', 'all'), null);
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
} // Class Mage_Api_Model_Config End