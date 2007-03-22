<?php

class Mage_Auth_Acl_Assert_Time
{
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
        return $this->_isCleanTime(time());
    }

    protected function _isCleanTime($time)
    {
        // ...
    }
}