<?php

class Mage_Auth_Acl_Role_Registry extends Zend_Acl_Role_Registry 
{
    public function getChildren($role)
    {
        $roleId = $this->get($role)->getRoleId();

        return $this->_roles[$roleId]['children'];
    }
}