<?php

class Mage_Auth_Model_Acl_Assert_Ip
{
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
        return $this->_isCleanIP($_SERVER['REMOTE_ADDR']);
    }

    protected function _isCleanIP($ip)
    {
        // ...
    }
}