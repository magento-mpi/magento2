<?php

class Mage_Auth_Model_Acl extends Zend_Acl
{
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = Mage::getModel('auth', 'acl_role_registry');
        }
        return $this->_roleRegistry;
    }
    
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);   
    }
}