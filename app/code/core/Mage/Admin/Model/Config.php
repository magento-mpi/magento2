<?php

/**
 * Configuration for Admin model
 * 
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Admin_Model_Config extends Varien_Simplexml_Config
{
    public function __construct()
    {
        parent::__construct();
        $this->setXml($this->loadFile(Mage::getModuleDir('etc', 'Mage_Admin').DS.'admin.xml'));
    }
    
    /**
     * Load Acl resources from config
     *
     * @param Mage_Admin_Model_Acl $acl
     * @param Mage_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return Mage_Admin_Model_Config
     */
    public function loadAclResources(Mage_Admin_Model_Acl $acl, $resource=null, $parentName=null)
    {
        if (is_null($resource)) {
            $resource = $this->getNode("admin/acl/resources");
            $resourceName = null;
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
            $acl->add(Mage::getModel('admin/acl_resource', $resourceName), $parentName);
        }
        $children = $resource->children();
        
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
        $asserts = $this->getNode("admin/acl/asserts");
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
        $sets = $this->getNode("admin/acl/privilegeSets");
        if (''===$name) {
            return $sets;
        } 
        
        if (isset($sets->$name)) {
            return $sets->$name;
        }
        
        return false;
    }

}