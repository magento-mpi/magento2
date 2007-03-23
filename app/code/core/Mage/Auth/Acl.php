<?php

class Mage_Auth_Acl extends Zend_Acl
{
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Mage_Auth_Acl_Role_Registry();
        }
        return $this->_roleRegistry;
    }
    
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);   
    }
}