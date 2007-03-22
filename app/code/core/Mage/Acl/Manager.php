<?php

class Mage_Acl_Manager extends Zend_Acl
{
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Mage_Acl_Role_Registry();
        }
        return $this->_roleRegistry;
    }

}