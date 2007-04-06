<?php

class Mage_Auth_Config
{
    static public function loadAclResources(Zend_Acl $acl, $resource=null, $parentName=null)
    {
        if (is_null($resource)) {
            $resource = Mage::getConfig()->getXml()->admin->acl->resources;
            $resourceName = null;
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
            $acl->add(new Mage_Auth_Acl_Resource($resourceName), $parentName);
        }
        $children = $resource->children();
        
        if (empty($children)) {
            return;
        }
        
        foreach ($children as $res) {
            self::loadAclResources($acl, $res, $resourceName);
        }
    }
    
    static public function getAclAssert($name='')
    {
        $asserts = Mage::getConfig()->getXml()->admin->acl->asserts;
        if (''===$name) {
            return $asserts;
        }
    
        if (isset($asserts->$name)) {
            return $asserts->$name;
        }

        return false;
    }
    
    static public function getAclPrivilegeSet($name='')
    {
        $sets = Mage::getConfig()->getXml()->admin->acl->privilegeSets;
        if (''===$name) {
            return $sets;
        } 
        
        if (isset($sets->$name)) {
            return $sets->$name;
        }
        
        return false;
    }

}