<?php

/**
 * IP assertion for admin acl
 * 
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Admin_Model_Acl_Assert_Ip implements Zend_Acl_Assert_Interface 
{
    /**
     * Check whether ip is allowed
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $role
     * @param Zend_Acl_Resource_Interface $resource
     * @param string $privilege
     * @return boolean
     */
    public function assert(Mage_Admin_Model_Acl $acl, Mage_Admin_Model_Acl_Role $role = null,
                           Mage_Admin_Model_Acl_Resource $resource = null, $privilege = null)
    {
        return $this->_isCleanIP($_SERVER['REMOTE_ADDR']);
    }

    protected function _isCleanIP($ip)
    {
        // ...
    }
}