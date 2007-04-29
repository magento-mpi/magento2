<?php

class Mage_Auth_Model_Config
{
    public function loadAclResources(Zend_Acl $acl, $resource=null, $parentName=null)
    {
        if (is_null($resource)) {
            $resource = Mage::getConfig()->getNode("admin/acl/resources");
            $resourceName = null;
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
            $acl->add(Mage::getModel('auth', 'acl_resource', $resourceName), $parentName);
        }
        $children = $resource->children();
        
        if (empty($children)) {
            return;
        }
        
        foreach ($children as $res) {
            self::loadAclResources($acl, $res, $resourceName);
        }
    }
    
    public function getAclAssert($name='')
    {
        $asserts = Mage::getConfig()->getNode("admin/acl/asserts");
        if (''===$name) {
            return $asserts;
        }
    
        if (isset($asserts->$name)) {
            return $asserts->$name;
        }

        return false;
    }
    
    public function getAclPrivilegeSet($name='')
    {
        $sets = Mage::getConfig()->getNode("admin/acl/privilegeSets");
        if (''===$name) {
            return $sets;
        } 
        
        if (isset($sets->$name)) {
            return $sets->$name;
        }
        
        return false;
    }

}